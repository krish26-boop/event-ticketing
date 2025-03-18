<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Event;
use App\Models\User;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
{
    parent::setUp();
    
    // Run role seeding inside test
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'organizer', 'guard_name' => 'web']);
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'attendee', 'guard_name' => 'web']);
}

    public function test_organizer_can_create_event()
    {
        $organizer = User::factory()->create();
        $organizer->assignRole('organizer'); // Ensure user has correct role
        $this->actingAs($organizer);

        $eventData = [
            'title' => 'Tech Conference 2025',
            'description' => 'An amazing tech event.',
            'date' => now()->addDays(10)->toDateString(),
            'location' => 'Ahmedabad',
            'tickets_available' => 100,
            'user_id' => $organizer->id
        ];

        $response = $this->postJson(route('events.store'), $eventData);

        
        $response->assertStatus(200)->assertJson(['message' => 'Event created successfully']);

        $this->assertDatabaseHas('events', ['title' => 'Tech Conference 2025']);
    }

    public function test_non_organizer_cannot_create_event()
    {
        $attendee = User::factory()->create()->assignRole('attendee'); // Regular attendee
        $this->actingAs($attendee);

        $eventData = [
            'title' => 'Unauthorized Event',
            'description' => 'This should not be allowed.',
            'date' => now()->addDays(10),
            'location' => 'Mumbai',
            'tickets_available' => 50,
        ];

        $response = $this->postJson(route('events.store'), $eventData);
        $response->assertStatus(403); // Forbidden
    }

    public function test_unauthenticated_user_cannot_create_event()
    {
        $eventData = [
            'title' => 'No Login Event',
            'description' => 'This should be denied.',
            'date' => now()->addDays(10),
            'location' => 'Delhi',
            'tickets_available' => 20,
        ];

        $response = $this->postJson(route('events.store'), $eventData);
        $response->assertStatus(401); // Unauthorized
    }

    public function test_organizer_can_update_their_own_event()
    {
        $organizer = User::factory()->create()->assignRole('organizer');
        $this->actingAs($organizer);

        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $updateData = ['title' => 'Updated Event Title',
                        'description' => 'Updated event description.',
                        'date' => now()->addDays(15)->toDateString(),
                        'location' => 'Updated Location',
                        'tickets_available' => 150,
                        'early_price' => 50.00,
                        'early_quantity' => 20,
                        'regular_price' => 100.00,
                        'regular_quantity' => 50,
                        'vip_price' => 200.00,
                        'vip_quantity' => 30];

        $response = $this->patchJson(route('events.update', $event->id), $updateData);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Event updated successfully']);

        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Updated Event Title']);
    }

    public function test_organizer_cannot_update_other_organizers_event()
    {
        $organizer1 = User::factory()->create()->assignRole('organizer');
        $organizer2 = User::factory()->create()->assignRole('organizer');

        $event = Event::factory()->create(['user_id' => $organizer1->id]);

        $this->actingAs($organizer2);

        $response = $this->patchJson(route('events.update', $event->id), ['title' => 'Unauthorized Update']);
        $response->assertStatus(403); // Forbidden
    }

    public function test_unauthenticated_user_cannot_update_event()
    {
        $event = Event::factory()->create();

        $response = $this->patchJson(route('events.update', $event->id), ['title' => 'Hacked Title']);
        $response->assertStatus(401); // Unauthorized
    }

    public function test_organizer_can_delete_their_own_event()
    {
        $organizer = User::factory()->create()->assignRole('organizer');
        $this->actingAs($organizer);

        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $response = $this->deleteJson(route('events.destroy', $event->id));

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Event cancelled successfully']);

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_organizer_cannot_delete_other_organizers_event()
    {
        $organizer1 = User::factory()->create()->assignRole('organizer');
        $organizer2 = User::factory()->create()->assignRole('organizer');

        $event = Event::factory()->create(['user_id' => $organizer1->id]);

        $this->actingAs($organizer2);

        $response = $this->deleteJson(route('events.destroy', $event->id));
        $response->assertStatus(403); // Forbidden
    }

    public function test_unauthenticated_user_cannot_delete_event()
    {
        $event = Event::factory()->create();

        $response = $this->deleteJson(route('events.destroy', $event->id));
        $response->assertStatus(401); // Unauthorized
    }
}
