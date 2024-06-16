<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Notifications\RegisteredToActivityNotification;
use Symfony\Component\HttpFoundation\Response;

class ActivityRegisterController extends Controller
{
    public function store(Activity $activity)
    {
        $activity_id = $activity->id;

        if (auth()->guest()) {
            return to_route('register', ['activity' => $activity_id]);
        }

        abort_if(auth()->user()->activities()->where('id', $activity_id)->exists(), Response::HTTP_CONFLICT);

        auth()->user()->activities()->attach($activity);

        auth()->user()->notify(new RegisteredToActivityNotification($activity));


//        return redirect()->route('activity.show', $activity)->with('success', 'Activity registered successfully');
        return to_route('my-activity.show')->with('success', 'You have successfully registered.');
    }
}
