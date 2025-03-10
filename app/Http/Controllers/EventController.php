<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        if ($request->ajax()) {
            $events = Event::where('user_id', Auth::id())->get();
            // return view('events.index',compact('events'));
            return response()->json(['events' => $events]);
        }
        return view('events.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        if ($request->ajax()) {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'date' => 'required|date|after:today',
                'location' => 'required',
                'tickets_available' => 'required|integer|min:1',
            ]);
            $event = auth()->user()->events()->create($request->all());
            return response()->json(['message' => 'Event created successfully']);
        }
        // return redirect()->route('events.index')->with('success', 'Event created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
        $event = $event->where(['events.id' => $event->id])->with('tickets')->first();
        // dd($event);

        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
        // $this->authorize('update', $event);
        $event = $event->where(['events.id' => $event->id])->with('tickets')->first();
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        //
        if ($event->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        // dd($request);
        // Validate the input data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'tickets_available' => 'required|integer|min:1',
            'early_price' => 'required|numeric|min:0',
            'early_quantity' => 'required|integer|min:0',
            'regular_price' => 'required|numeric|min:0',
            'regular_quantity' => 'required|integer|min:0',
            'vip_price' => 'required|numeric|min:0',
            'vip_quantity' => 'required|integer|min:0',
        ]);

        // Update event details
        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'location' => $validated['location'],
            'tickets_available' => $validated['tickets_available'],
        ]);

        // Update tickets for this event
        $ticketTypes = [
            'early' => ['price' => $validated['early_price'], 'quantity' => $validated['early_quantity']],
            'regular' => ['price' => $validated['regular_price'], 'quantity' => $validated['regular_quantity']],
            'vip' => ['price' => $validated['vip_price'], 'quantity' => $validated['vip_quantity']],
        ];

        foreach ($ticketTypes as $type => $details) {
            $ticket = $event->tickets()->where('type', $type)->first();
            if ($ticket) {
                // Update existing ticket
                $ticket->update([
                    'price' => $details['price'],
                    'quantity' => $details['quantity'],
                ]);
            } else {
                // Create a new ticket if it doesn't exist
                $event->tickets()->create([
                    'type' => $type,
                    'price' => $details['price'],
                    'quantity' => $details['quantity'],
                    'sold' => 0, // Assume newly added tickets haven't been sold yet
                ]);
            }
        }

        return response()->json(['message' => 'Event updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
        if ($event->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $event->delete();
        return response()->json(['message' => 'Event cancelled successfully']);
    }
}
