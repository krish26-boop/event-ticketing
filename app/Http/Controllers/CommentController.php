<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    //

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'comment' => 'required|string|max:500',
        ]);

        $comment = Comment::create([
            'event_id' => $request->event_id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment]);
    }

    public function getComments($eventId)
    {
        $comments = Comment::where('event_id', $eventId)->with('user')->latest()->get();
        return response()->json($comments);
    }
}
