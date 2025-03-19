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
        $events = DB::table('events')->get();

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
        ]);

        $validated['user_id'] = Auth::id() ?? null;

        DB::table('events')->insert($validated);

        return redirect()->route('calendar'); // Esto actualizará los eventos en Inertia
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
            'title' => 'sometimes|required|string',
            'start' => 'sometimes|required|date',
        ]);

        DB::table('events')->where('id', $eventId)->update($validated);

        $event = DB::table('events')->where('id', $eventId)->first();

        return response()->json([
            'message' => 'Evento actualizado con éxito.',
            'event' => $event,
        ]);
    }

    /**
     * Eliminar eventos sin usar el modelo.
     *
     * @param int $eventId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($eventId)
    {
        DB::table('events')->where('id', $eventId)->delete();

        return response()->json([
            'message' => 'Evento eliminado con éxito.',
        ]);
    }
}
