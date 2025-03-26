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

    public function create()
    {
        $userGroups = Group::where('creator_id', Auth::id())->get();

        return Inertia::render('Social/CreateEvent', [
            'userGroups' => $userGroups
        ]);
    }

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

        $slug = Str::slug($request->title);
        $uniqueSlug = $slug;
        $counter = 1;

        while (Event::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $counter;
            $counter++;
        }

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

        if ($request->hasFile('photos')) {
            $order = 0;
            foreach ($request->file('photos') as $photo) {
                $photoPath = $photo->store('events/photos', 'public');
                
                EventPhoto::create([
                    'event_id' => $event->id,
                    'photo_path' => Storage::url($photoPath),
                    'order' => $order++
                ]);
            }
        }

        EventAttendee::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'status' => 'attending',
        ]);

        $event->attendees_count = 1;
        $event->save();

        return redirect()->route('events.show', $event->slug)->with('success', 'Evento creado con éxito!');
    }


    public function show($slug)
    {
        $event = Event::where('slug', $slug)
            ->with(['organizer.profile', 'group', 'photos', 'attendees.user.profile'])
            ->firstOrFail();

        $isOrganizer = Auth::id() === $event->organizer_id;
        $isAttending = $event->isAttendingBy(Auth::user());

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

    public function edit($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        if (Auth::id() !== $event->organizer_id) {
            return redirect()->route('events.show', $event->slug)->with('error', 'No tienes permiso para editar este evento.');
        }

        $userGroups = Group::where('creator_id', Auth::id())->get();

        return Inertia::render('Social/EditEvent', [
            'event' => $event,
            'userGroups' => $userGroups
        ]);
    }

    public function update(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

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

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'event_date' => $request->event_date,
            'is_private' => $request->is_private,
            'group_id' => $request->is_private ? $request->group_id : null,
        ]);

        if ($request->hasFile('photos')) {
            $order = $event->photos()->max('order') + 1;
            foreach ($request->file('photos') as $photo) {
                $photoPath = $photo->store('events/photos', 'public');
                
                EventPhoto::create([
                    'event_id' => $event->id,
                    'photo_path' => Storage::url($photoPath),
                    'order' => $order++
                ]);
            }
        }

        return redirect()->route('events.show', $event->slug)->with('success', 'Evento actualizado con éxito!');
    }

    public function destroy($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        if (Auth::id() !== $event->organizer_id) {
            return redirect()->route('events.show', $event->slug)->with('error', 'No tienes permiso para eliminar este evento.');
        }

        foreach ($event->photos as $photo) {
            $path = str_replace('/storage/', '', $photo->photo_path);
            Storage::disk('public')->delete($path);
        }

        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evento eliminado con éxito!');
    }

    public function attend($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        if ($event->is_private && $event->group) {
            $isMember = $event->group->members()->where('user_id', Auth::id())->exists();
            if (!$isMember) {
                return redirect()->route('events.show', $event->slug)->with('error', 'No puedes asistir a este evento privado.');
            }
        }

        if ($event->isAttendingBy(Auth::user())) {
            return redirect()->route('events.show', $event->slug)->with('info', 'Ya estás asistiendo a este evento.');
        }

        EventAttendee::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'status' => 'attending',
        ]);

        $event->attendees_count = $event->attendees()->count();
        $event->save();

        return redirect()->route('events.show', $event->slug)->with('success', 'Te has unido al evento!');
    }

    public function cancelAttendance($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $attendance = $event->attendees()->where('user_id', Auth::id())->first();
        if (!$attendance) {
            return redirect()->route('events.show', $event->slug)->with('info', 'No estás asistiendo a este evento.');
        }

        $attendance->delete();

        $event->attendees_count = $event->attendees()->count();
        $event->save();

        return redirect()->route('events.show', $event->slug)->with('success', 'Has cancelado tu asistencia al evento.');
    }

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

        if ($photo->event_id !== $event->id) {
            return redirect()->route('events.show', $event->slug)->with('error', 'La foto no pertenece a este evento.');
        }

        $path = str_replace('/storage/', '', $photo->photo_path);
        Storage::disk('public')->delete($path);

        $photo->delete();

        return redirect()->route('events.show', $event->slug)->with('success', 'Foto eliminada con éxito!');
    }
}

