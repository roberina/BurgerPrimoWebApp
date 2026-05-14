<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotificationService
{
    private ?WebPush $webPush = null;

    private function webPush(): WebPush
    {
        if ($this->webPush === null) {
            $auth = [
                'VAPID' => [
                    'subject'    => config('services.vapid.subject'),
                    'publicKey'  => config('services.vapid.public_key'),
                    'privateKey' => config('services.vapid.private_key'),
                ],
            ];

            // The library emits E_USER_NOTICE when bcmath/gmp aren't in the
            // web-server PHP context. Suppress it — it's a speed hint, not an error.
            $prev = error_reporting(error_reporting() & ~E_USER_NOTICE);
            $this->webPush = new WebPush($auth);
            error_reporting($prev);
        }

        return $this->webPush;
    }

    public function sendToAdmins(string $title, string $body, string $url = '/'): void
    {
        $adminIds = \App\Models\User::where('is_admin', true)->pluck('id');
        $subscriptions = PushSubscription::whereIn('user_id', $adminIds)->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $this->dispatch($subscriptions, $title, $body, $url);
    }

    public function sendToUser(int $userId, string $title, string $body, string $url = '/'): void
    {
        $subscriptions = PushSubscription::where('user_id', $userId)->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $this->dispatch($subscriptions, $title, $body, $url);
    }

    public function sendToOnlineCouriers(string $title, string $body, string $url = '/'): void
    {
        $courierIds = \App\Models\User::where('is_courier', true)
            ->where('courier_online', true)
            ->pluck('id');

        $subscriptions = PushSubscription::whereIn('user_id', $courierIds)->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $this->dispatch($subscriptions, $title, $body, $url);
    }

    public function sendToAll(string $title, string $body, string $url = '/'): void
    {
        $subscriptions = PushSubscription::all();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $this->dispatch($subscriptions, $title, $body, $url);
    }

    private function dispatch(\Illuminate\Support\Collection $subscriptions, string $title, string $body, string $url): void
    {
        $webPush = $this->webPush();
        $payload = json_encode(['title' => $title, 'body' => $body, 'url' => $url]);

        foreach ($subscriptions as $sub) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'keys'     => ['p256dh' => $sub->public_key, 'auth' => $sub->auth_token],
                ]),
                $payload
            );
        }

        /** @var MessageSentReport $report */
        foreach ($webPush->flush() as $report) {
            if (!$report->isSuccess()) {
                PushSubscription::where('endpoint', $report->getEndpoint())->delete();
            }
        }
    }
}
