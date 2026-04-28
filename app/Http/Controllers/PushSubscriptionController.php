<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'endpoint'   => 'required|string',
            'public_key' => 'required|string',
            'auth_token' => 'required|string',
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'user_id'    => auth()->id(),
                'public_key' => $data['public_key'],
                'auth_token' => $data['auth_token'],
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request)
    {
        $endpoint = $request->validate(['endpoint' => 'required|string'])['endpoint'];
        PushSubscription::where('endpoint', $endpoint)->delete();
        return response()->json(['ok' => true]);
    }
}
