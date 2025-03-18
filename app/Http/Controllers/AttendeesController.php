<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use App\Mail\TicketPurchaseConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class AttendeesController extends Controller
{
    //

    public function index(Request $request)
    {
        //
        if ($request->ajax()) {
            $attendees = User::whereHas('events', function ($query) {
                $query->where('user_id', Auth::id());
            })->with(['events.orders.orderitems.tickets','events.orders.user'])->get();

        // dd($attendees);
        return response()->json(['attendees' => $attendees]);
        }
        return view('attendees.index');
    }

    
    // Search events based on keyword, location, or date
    public function search(Request $request)
    {
        $query = Event::query();

        if ($request->filled('keyword')) {
            $query->where('title', 'like', '%' . $request->keyword . '%')
                  ->orWhere('description', 'like', '%' . $request->keyword . '%');
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        return response()->json($query->paginate(10));
    }
    

    // Get upcoming events
    public function upcoming(Request $request)
    {
        if ($request->ajax()) {

            $query = Event::where('date', '>=', now());

            if ($request->filled('keyword')) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->keyword . '%')
                      ->orWhere('description', 'like', '%' . $request->keyword . '%');
                });
            }

            if ($request->filled('location')) {
                $query->where('location', 'like', '%' . $request->location . '%');
            }

            if ($request->filled('date')) {
                $query->whereDate('date', $request->date);
            }
            
            return response()->json($query->orderBy('date')->paginate(10));
        }

        return abort(403, 'Unauthorized action.');
    }

    public function purchaseTickets(Request $request)
    {
        $request->validate([
            'tickets' => 'required|array',
            'tickets.*.event_id' => 'required|exists:events,id',
            'tickets.*.ticket_id' => 'required|exists:tickets,id',
            'tickets.*.ticket_quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        $totalAmount = 0;
        $orderItems = [];

        DB::beginTransaction();
        try {
            // Calculate total price
            foreach ($request->tickets as $item) {
                $ticket = Ticket::find($item['ticket_id']);
                if (!$ticket || ($ticket->quantity - $ticket->sold) < $item['ticket_quantity']) {
                    return response()->json(['message' => "Not enough tickets available for {$ticket->type}"], 400);
                }

                $totalAmount += $ticket->price * $item['ticket_quantity'];
                $eventId = $item['event_id'];
                $orderItems[] = [
                    'ticket_id' => $ticket->id,
                    'type' => $ticket->type,
                    'quantity' => $item['ticket_quantity'],
                    'price' => $ticket->price,
                ];
            }

            // Create order
            $order = Order::create([
                'event_id' => $eventId,
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'payment_status' => 'pending',
            ]);

            // Create order items and reduce ticket stock
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_id' => $item['ticket_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
                $ticket = Ticket::find($item['ticket_id']);
                $ticket->decrement('quantity', $item['quantity']);
                $ticket->increment('sold', $item['quantity']);
            }

            DB::commit();

            Mail::to($user->email)->send(new TicketPurchaseConfirmation($user, $order, $orderItems));

            return response()->json(['message' => 'Order placed successfully', 'order_id' => $order->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Order failed', 'error' => $e->getMessage()], 500);
        }
    }
}
