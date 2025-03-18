<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class CommentController extends Controller
{
    //
    use HasRoles;

    public function store(Request $request)
    {
        // dd($request->all());
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated'], 401); // Return 401 for guests
        }
        
        if (!Auth::user()->hasRole('attendee')) {
            return response()->json(['message' => 'Unauthorized'], 403); // Return 403 for non-attendees
        }

        $request->validate([
            'event_id' => 'required|exists:events,id',
            'comment' => 'required|string|max:500',
        ]);

        $comment = Comment::create([
            'event_id' => $request->event_id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        auth()->user()->comments()->create($comment);

        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment]);
    }

    public function getComments($eventId)
    {
        $comments = Comment::where('event_id', $eventId)->with('user')->latest()->get();
        return response()->json($comments);
    }
}
