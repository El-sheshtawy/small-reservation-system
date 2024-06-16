<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\User;
use App\Notifications\RegisteredToActivityNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_register_to_activity_when_activity_not_registered()
    {
        $activity = Activity::factory()->create([
            'name' => 'Test name',
            'description' => 'Test description ',
            'price' => 100,
        ]);
        $response = $this->get(route('activity.show', $activity));

        $response
            ->assertOk()
            ->assertSee($activity->name)
            ->assertSee($activity->description)
            ->assertSee(100)
            ->assertViewHas('activity', function () use ($activity) {
                return $activity->name === 'Test name';
            })
            ->assertSee('Register to Activity')
            ->assertDontSeeText('You have already registered.');
    }

    public function test_user_can_see_you_have_already_registered_when_activity_registered()
    {
        $activity = Activity::factory()->create([
            'name' => 'Test name',
            'description' => 'Test description ',
            'price' => 100,
        ]);

        $user = User::factory()->customer()->create();

        $user->activities()->attach($activity);

        $response = $this->actingAs($user)->get(route('activity.show', $activity));

        $response
            ->assertOk()
            ->assertSee($activity->name)
            ->assertSee($activity->description)
            ->assertSee(100)
            ->assertViewHas('activity', function () use ($activity) {
                return $activity->name === 'Test name';
            })
            ->assertSee('You have already registered.')
            ->assertDontSeeText('Register to Activity');
    }

    // Authenticated users can register for the activity, receive a notification, and are redirected to the My activities page.
    public function test_guest_redirected_with_activity_want_to_register_and_activity_can_be_registered_once_per_user()
    {
        $activity = Activity::factory()->create();

        $guestResponse = $this->post(route('activities.register',$activity ));
        $guestResponse->assertRedirectToRoute('register', ['activity' => $activity->id]);

        $user = User::factory()->customer()->create();

        $response = $this->actingAs($user)->post(route('activities.register',$activity ));

        $response = $this->actingAs($user)->post(route('activities.register',$activity ));

        $response->assertConflict();
    }

    public function test_authenticated_users_can_register_for_the_activity_and_receive_a_notification_and__redirected_to_the_my_activities_page()
    {
        Notification::fake();

        $activity = Activity::factory()->create([
            'name' => 'Test name',
            'description' => 'Test description ',
            'price' => 100,
        ]);

        $user = User::factory()->customer()->create();

        $response = $this->actingAs($user)->post(route('activities.register',$activity ));


        Notification::assertSentTo($user, RegisteredToActivityNotification::class);

        $response->assertRedirectToRoute('my-activity.show');

        $this->assertCount(1, $user->activities()->get());
    }
}
