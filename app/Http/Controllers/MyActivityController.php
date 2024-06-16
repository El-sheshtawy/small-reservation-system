<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MyActivityController extends Controller
{
    public function show()
    {
        $activities = auth()->user()->activities()->orderBy('start_time')->get();

        return view('activities.my-activities', compact('activities'));
    }

    public function cancel(Activity $activity)
    {
        abort_if(auth()->guest() || ! auth()->user()->activities->contains($activity) , ResponseAlias::HTTP_FORBIDDEN);

        auth()->user()->activities()->detach($activity);

        return to_route('my-activity.show')->with('success', 'Activity removed.');
    }
}
