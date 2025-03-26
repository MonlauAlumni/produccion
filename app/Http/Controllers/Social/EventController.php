<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\EventPhoto;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     */
    public function index()
    {
        $events = Event::with(['organizer', 'group'])
            ->where('is_private', false)
            ->orWhereHas('group', function ($query) {
                $query->whereHas('members', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('Social/Events', [
            'events' => $events
        ]);
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        // Get groups where the user is the creator
        $userGroups = Group::where('creator_id', Auth::id())->get();

        return Inertia::render('Social/CreateEvent', [
            'userGroups' => $userGroups
        ]);
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date|after:now',
            'is_private' => 'required|boolean',
            'group_id' => 'nullable|exists:groups,id',
            'photos.*' => 'nullable|image|max:2048',
        ]);

        // Create a unique slug
        $slug = Str::slug($request->title);
        $uniqueSlug = $slug;
        $counter = 1;

        while (Event::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $counter;
            $counter++;
        }

        // Create the event
        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'event_date' => $request->event_date,
            'organizer_id' => Auth::id(),
            'is_private' => $request->is_private,
            'group_id' => $request->is_private ? $request->group_id : null,
            'slug' => $uniqueSlug,
        ]);

        // Handle photos if they exist
        if ($request->hasFile('photos')) {
            $order = 0;
            foreach ($request->file('photos') as $photo) {
                $photoPath = $photo->store('events/photos', 'public');
                
                // Create a new photo record
                EventPhoto::create([
                    'event_id' => $event->id,
                    'photo_path' => Storage::url($photoPath),
                    'order' => $order++
                ]);
            }
        }

        // Add the creator as an attendee
        EventAttendee::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'status' => 'attending',
        ]);

        // Update attendees count
        $event->attendees_count = 1;
        $event->save();

        return redirect()->route('events.show', $event->slug)->with('success', 'Evento creado con éxito!');
    }

    /**
     * Display the specified event.
     */
    public function show($slug)
    {
        $event = Event::where('slug', $slug)
            ->with(['organizer.profile', 'group', 'photos', 'attendees.user.profile'])
            ->firstOrFail();

        $isOrganizer = Auth::id() === $event->organizer_id;
        $isAttending = $event->isAttendingBy(Auth::user());

        // Check if user can view this event
        if ($event->is_private && $event->group) {
            $isMember = $event->group->members()->where('user_id', Auth::id())->exists();
            if (!$isMember && !$isOrganizer) {
                return redirect()->route('events.index')->with('error', 'No tienes permiso para ver este evento privado.');
            }
        }

        return Inertia::render('Social/SingleEvent', [
            'event' => $event,
            'isOrganizer' => $isOrganizer,
            'isAttending' => $isAttending,
        ]);
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // Check if user is the organizer
        if (Auth::id() !== $event->organizer_id) {
            return redirect()->route('events.show', $event->slug)->with('error', 'No tienes permiso para editar este evento.');
        }

        // Get groups where the user is the creator
        $userGroups = Group::where('creator_id', Auth::id())->get();

        return Inertia::render('Social/EditEvent', [
            'event' => $event,
            'userGroups' => $userGroups
        ]);
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // Check if user is the organizer
        if (Auth::id() !== $event->organizer_id) {
            return redirect()->route('events.show', $event->slug)->with('error', 'No tienes permiso para editar este evento.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date',
            'is_private' => 'required|boolean',
            'group_id' => 'nullable|exists:groups,id',
            'photos.*' => 'nullable|image|max:2048',
        ]);

        // Update the event
        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'event_date' => $request->event_date,
            'is_private' => $request->is_private,
            'group_id' => $request->is_private ? $request->group_id : null,
        ]);

        // Handle photos if they exist
        if ($request->hasFile('photos')) {
            $order = $event->photos()->max('order') + 1;
            foreach ($request->file('photos') as $photo) {
                $photoPath = $photo->store('events/photos', 'public');
                
                // Create a new photo record
                EventPhoto::create([
                    'event_id' => $event->id,
                    'photo_path' => Storage::url($photoPath),
                    'order' => $order++
                ]);
            }
        }

        return redirect()->route('events.show', $event->slug)->with('success', 'Evento actualizado con éxito!');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // Check if user is the organizer
        if (Auth::id() !== $event->organizer_id) {
            return redirect()->route('events.show', $event->slug)->with('error', 'No tienes permiso para eliminar este evento.');
        }

        // Delete photos from storage
        foreach ($event->photos as $photo) {
            $path = str_replace('/storage/', '', $photo->photo_path);
            Storage::disk('public')->delete($path);
        }

        // Delete the event
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evento eliminado con éxito!');
    }

    /**
     * Attend an event.
     */
    public function attend($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // Check if user can attend this event
        if ($event->is_private && $event->group) {
            $isMember = $event->group->members()->where('user_id', Auth::id())->exists();
            if (!$isMember) {
                return redirect()->route('events.show', $event->slug)->with('error', 'No puedes asistir a este evento privado.');
            }
        }

        // Check if user is already attending
        if ($event->isAttendingBy(Auth::user())) {
            return redirect()->route('events.show', $event->slug)->with('info', 'Ya estás asistiendo a este evento.');
        }

        // Add user as an attendee
        EventAttendee::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'status' => 'attending',
        ]);

        // Update attendees count
        $event->attendees_count = $event->attendees()->count();
        $event->save();

        return redirect()->route('events.show', $event->slug)->with('success', 'Te has unido al evento!');
    }

    /**
     * Cancel attendance to an event.
     */
    public function cancelAttendance($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // Check if user is attending
        $attendance = $event->attendees()->where('user_id', Auth::id())->first();
        if (!$attendance) {
            return redirect()->route('events.show', $event->slug)->with('info', 'No estás asistiendo a este evento.');
        }

        // Remove attendance
        $attendance->delete();

        // Update attendees count
        $event->attendees_count = $event->attendees()->count();
        $event->save();

        return redirect()->route('events.show', $event->slug)->with('success', 'Has cancelado tu asistencia al evento.');
    }

    /**
     * Remove a photo from an event.
     */
    public function removePhoto(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        if (Auth::id() !== $event->organizer_id) {
            return redirect()->route('events.show', $event->slug)->with('error', 'No tienes permiso para eliminar fotos de este evento.');
        }

        $request->validate([
            'photo_id' => 'required|exists:event_photos,id',
        ]);

        $photo = EventPhoto::findOrFail($request->photo_id);

        // Check if photo belongs to this event
        if ($photo->event_id !== $event->id) {
            return redirect()->route('events.show', $event->slug)->with('error', 'La foto no pertenece a este evento.');
        }

        // Delete photo from storage
        $path = str_replace('/storage/', '', $photo->photo_path);
        Storage::disk('public')->delete($path);

        // Delete photo record
        $photo->delete();

        return redirect()->route('events.show', $event->slug)->with('success', 'Foto eliminada con éxito!');
    }
}

