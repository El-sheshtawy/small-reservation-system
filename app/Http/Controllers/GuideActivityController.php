<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class GuideActivityController extends Controller
{
    public function show()
    {
        abort_if(auth()->user()->role_id !== Role::GUIDE->value, Response::HTTP_FORBIDDEN);

        $activities = Activity::where('guide_id', auth()->id())->orderBy('start_time')->get();

        return view('guide-activities', compact('activities'));
    }

    public function export(Activity $activity)
    {
        abort_if(auth()->user()->role_id !== Role::GUIDE->value, Response::HTTP_FORBIDDEN);

        $data = $activity->load(['participants' => function($query) {
            $query->orderByPivot('created_at');
        }]);

        $pdf = Pdf::loadView('activities.pdf', compact('data'))
            ->setPaper('a4', 'portrait')
            ->setWarnings(false);

        return $pdf->download("{$activity->name}.pdf");
    }
}
