<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Comment;
use App\Models\Event;
use App\Models\User;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run role seeding inside test
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'organizer', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'attendee', 'guard_name' => 'web']);
    }

    public function test_attendee_can_store_comment()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $user->assignRole('attendee'); // Ensure attendee role

        $this->actingAs($user);

        $commentData = [
            'event_id' => $event->id,
            'comment' => 'This is an attendee comment.'
        ];

        $response = $this->postJson(route('comments.store'), $commentData);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Comment added successfully']);

        $this->assertDatabaseHas('comments', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'comment' => 'This is an attendee comment.'
        ]);
    }

    public function test_cannot_store_comment_without_authentication()
    {
        $event = Event::factory()->create();

        $commentData = [
            'event_id' => $event->id,
            'comment' => 'Unauthorized comment'
        ];

        $response = $this->postJson(route('comments.store'), $commentData);

        $response->assertStatus(403);
    }

    public function test_can_fetch_event_comments()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create();
        Comment::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'comment' => 'Test comment'
        ]);

        $response = $this->getJson(route('comments.get', $event->id));

        $response->assertStatus(200)
                 ->assertJsonFragment(['comment' => 'Test comment']);
    }
}
