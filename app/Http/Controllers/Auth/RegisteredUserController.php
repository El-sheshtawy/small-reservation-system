<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use App\Models\UserInvitation;
use App\Notifications\RegisteredToActivityNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        if ($request->has('activity')) {
            session()->put('activity', $request->activity);
        }

        $email = null;

        if ($request->has('invitation_token')) {
            $token = $request->input('invitation_token');

            session()->put('invitation_token', $token);

            $invitation = UserInvitation::where('token', $token)
                ->whereNull('registered_at')
                ->firstOrFail();

            $email = $invitation->email;
        }

        return view('auth.register', compact('email'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);


        $role = Role::CUSTOMER->value;
        $company = null;
        if ($request->session()->has('invitation_token')) {
            $invitation = UserInvitation::where('token', $request->session()->get('invitation_token'))
                ->where('email', $request->email)
                ->whereNull('registered_at')
                ->first();

            if ($invitation) {
                $role = $invitation->role_id;
                $company = $invitation->company_id;
                $invitation->update(['registered_at' => now()]);
            } else {
                return back()->withErrors(['invitation' => 'Invitation link does not match the email']);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role,
            'company_id' => $company,
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($request->session()->has('activity')) {
            $activity = Activity::find($request->session()->get('activity'));

            if ($activity) {
                $user->activities()->attach($activity);
                $user->notify(new RegisteredToActivityNotification($activity));

                return redirect()->route('my-activity.show')->with('success', 'You have successfully registered.');
            }
        }

        return redirect()->intended(route('home'));
    }
}
