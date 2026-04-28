<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotificationService
{
    private WebPush $webPush;

    public function __construct()
    {
        $auth = [
            'VAPID' => [
                'subject'    => config('services.vapid.subject'),
                'publicKey'  => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ];

        $this->webPush = new WebPush($auth);
    }

    public function sendToAll(string $title, string $body, string $url = '/'): void
    {
        $subscriptions = PushSubscription::all();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $payload = json_encode(['title' => $title, 'body' => $body, 'url' => $url]);

        foreach ($subscriptions as $sub) {
            $this->webPush->queueNotification(
                Subscription::create([
                    'endpoint'        => $sub->endpoint,
                    'keys' => [
                        'p256dh' => $sub->public_key,
                        'auth'   => $sub->auth_token,
                    ],
                ]),
                $payload
            );
        }

        /** @var MessageSentReport $report */
        foreach ($this->webPush->flush() as $report) {
            if (!$report->isSuccess()) {
                // Remove expired/invalid subscriptions
                PushSubscription::where('endpoint', $report->getEndpoint())->delete();
            }
        }
    }
}
