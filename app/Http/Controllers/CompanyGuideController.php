<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Mail\RegistrationInvite;
use App\Models\User;
use App\Models\Company;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreGuideRequest;
use App\Http\Requests\UpdateGuideRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CompanyGuideController extends Controller
{
    public function index(Company $company)
    {
        Gate::authorize('viewAny', $company);

        $guides = $company->users()->where('role_id', Role::GUIDE->value)->get();

        return view('companies.guides.index', compact('company', 'guides'));
    }

    public function create(Company $company)
    {
        Gate::authorize('create', $company);

        return view('companies.guides.create', compact('company'));
    }

    public function store(StoreGuideRequest $request, Company $company)
    {
        Gate::authorize('create', $company);

        $invitation = UserInvitation::create([
            'email' => $request->input('email'),
            'token' => Str::uuid(),
            'company_id' => $company->id,
            'role_id' => Role::GUIDE->value,
        ]);

        Mail::to($request->input('email'))->send(new RegistrationInvite($invitation));

        return to_route('companies.guides.index', $company);
    }

    public function edit(Company $company,User $guide)
    {
        Gate::authorize('update', $company);

        return view('companies.guides.edit', compact('company', 'guide'));
    }

    public function update(UpdateGuideRequest $request, Company $company, User $guide)
    {
        Gate::authorize('update', $company);

        $guide->update($request->validated());

        return to_route('companies.guides.index', $company);
    }

    public function destroy(Company $company, User $guide)
    {
        Gate::authorize('delete', $company);

        $guide->delete();

        return to_route('companies.guides.index', $company);
    }
}
