<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_can_be_created()
    {
        $event = Event::factory()->create([
            'title' => 'Musical Event',
            'description' => 'A Musical event for developers.',
            'date' => now()->addDays(10),
            'location' => 'Ahmedabad',
            'tickets_available' => 100,
        ]);

        $this->assertDatabaseHas('events', ['title' => 'Musical Event']);
    }

    public function test_event_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $event->user->id);
    }

}
