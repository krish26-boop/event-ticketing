<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_creation()
    {
        $comment = new Comment([
            'event_id' => 1,
            'user_id' => 1,
            'comment' => 'Unit test comment',
        ]);

        $this->assertEquals(1, $comment->event_id);
        $this->assertEquals(1, $comment->user_id);
        $this->assertEquals('Unit test comment', $comment->comment);
    }
}
