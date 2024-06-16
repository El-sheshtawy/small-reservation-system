<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use App\Models\Company;
use App\Models\Activity;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class CompanyActivityController extends Controller
{
    public function index(Company $company)
    {
        Gate::authorize('viewAny', $company);

        $company->load('activities');

        return view('companies.activities.index', compact('company'));
    }

    public function create(Company $company)
    {
        Gate::authorize('create', $company);

        $guides = User::where('company_id', $company->id)
            ->where('role_id', Role::GUIDE->value)
            ->pluck('name', 'id');

        return view('companies.activities.create', compact('guides', 'company'));
    }

    public function store(StoreActivityRequest $request, Company $company)
    {
        Gate::authorize('create', $company);

        $activity = Activity::create($request->validated() + [
                'company_id' => $company->id,
            ]);

        if($request->hasFile('image') && $request->file('image')->isValid()){
            $activity->addMediaFromRequest('image')->toMediaCollection('images');
        }

        return redirect()->route('companies.activities.index', $company);
    }

    public function edit(Company $company, Activity $activity)
    {
        Gate::authorize('update', $company);

        $guides = User::where('company_id', $company->id)
            ->where('role_id', Role::GUIDE->value)
            ->pluck('name', 'id');

        return view('companies.activities.edit', compact('guides', 'activity', 'company'));
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(UpdateActivityRequest $request, Company $company, Activity $activity)
    {
        Gate::authorize('update', $company);

        $activity->update($request->validated());

        if($request->hasFile('image') && $request->file('image')->isValid()){

            $activity->clearMediaCollection('images');

            $activity->addMediaFromRequest('image')->toMediaCollection('images');
        }

        return to_route('companies.activities.index', $company);
    }

    public function destroy(Company $company, Activity $activity)
    {
        Gate::authorize('delete', $company);

        $activity->delete();

        return to_route('companies.activities.index', $company);
    }
}
