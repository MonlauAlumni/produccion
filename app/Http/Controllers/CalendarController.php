<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CalendarController extends Controller
{
    /**
     * Mostrar el calendario con eventos desde la base de datos.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $events = DB::table('calendar_events')->get();

        return inertia('Calendar', [
            'events' => $events,
        ]);
    }

    /**
     * Guardar eventos en la base de datos sin usar el modelo.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'start' => 'required|date',
            'description' => 'sometimes|string|nullable',
        ]);

        // Prepare data for calendar_events table
        $data = [
            'user_id'    => Auth::id(),
            'title'      => $validated['title'],
            'description'=> $validated['description'] ?? null,
            'start_date' => $validated['start'],
            'end_date'   => $validated['start'],
            'all_day'    => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        // Insert and return slug or id
        $id = DB::table('calendar_events')->insertGetId($data);
        $event = DB::table('calendar_events')->where('id', $id)->first();
        // If AJAX/json request, return JSON
        if ($request->expectsJson()) {
            return response()->json(['event' => $event]);
        }

        return redirect()->route('calendar.index');
    }

    /**
     * Actualizar eventos sin usar el modelo.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $eventId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $eventId)
    {
        $validated = $request->validate([
            'title'       => 'sometimes|required|string',
            'start'       => 'sometimes|required|date',
            'description' => 'sometimes|string|nullable',
        ]);

        // Build update payload for calendar_events
        $data = [];
        if (isset($validated['title'])) {
            $data['title'] = $validated['title'];
        }
        if (array_key_exists('description', $validated)) {
            $data['description'] = $validated['description'];
        }
        if (isset($validated['start'])) {
            $data['start_date'] = $validated['start'];
            $data['end_date']   = $validated['start'];
        }
        $data['updated_at'] = now();

        DB::table('calendar_events')->where('id', $eventId)->update($data);

        $event = DB::table('calendar_events')->where('id', $eventId)->first();

        // Return redirect for Inertia requests
        if ($request->header('X-Inertia')) {
            return redirect()->route('calendar.index');
        }
        return response()->json([
            'message' => 'Evento actualizado con éxito.',
            'event'   => $event,
        ]);
    }

    /**
     * Eliminar eventos sin usar el modelo.
     *
     * @param int $eventId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $eventId)
    {
        DB::table('calendar_events')->where('id', $eventId)->delete();
        // Always return JSON for AJAX delete
        return response()->json(['message' => 'Evento eliminado con éxito.']);
    }
}
