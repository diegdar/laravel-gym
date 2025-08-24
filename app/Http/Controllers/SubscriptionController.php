<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SubscriptionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:member.subscription', only: ['index']),
            new Middleware('permission:member.change-subscription', only: ['changeSubscription']),
        ];
    }

    public function index()
    {
        $user = Auth::user();
        $currentSubscription = $user
                         ->subscriptions()
                          ->wherePivot('status', 'active')->first();
        $subscriptions = Subscription::all();

        return view('users.subscription', compact('currentSubscription', 'user', 'subscriptions'));
    }

    public function changeSubscription(Subscription $newSubscription)
    {
        $user = Auth::user();

        $currentSubscription = $user->subscriptions()
                          ->wherePivot('status', 'active')
                          ->first();

        if ($currentSubscription) {
            $user->subscriptions()->updateExistingPivot($currentSubscription->id, [
                'status' => 'inactive',
                'end_date' => now(),
            ]);
        }

        $user->subscriptions()->attach($newSubscription->id, [
            'start_date' => now(),
            'end_date' => now()->addMonths($newSubscription->duration),
            'payment_date' => now(),
            'status' => 'active',
        ]);

        return redirect()->route('member.subscription');
    }
}
