<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Http\Controllers\Controller;
use App\Models\PushSubscription;

class PushController extends Controller
{
    /**
     * Abonnement à la notification push
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'endpoint' => 'required',
            'keys.auth' => 'required',
            'keys.p256dh' => 'required'
        ]);

        PushSubscription::where('user_id', $request->user()->id)->delete();

        $subscription = PushSubscription::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'endpoint' => $request->endpoint
            ],
            [
                'keys_auth' => $request->keys['auth'],
                'keys_p256dh' => $request->keys['p256dh']
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Methode pour le test de notification push (TEST DEV)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotification()
    {
        $subscriptions = PushSubscription::all();

        $auth = [
            'VAPID' => [
                'subject' => 'mailto:your-email@example.com',
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ];

        $webPush = new WebPush($auth);

        foreach ($subscriptions as $subscription) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'keys' => [
                        'auth' => $subscription->keys_auth,
                        'p256dh' => $subscription->keys_p256dh,
                    ],
                ]),
                json_encode([
                    'title' => "Hey ! {$subscription->user->pseudo}",
                    'body' => 'C\'est l\'heure de réviser votre Mementos!',
                    'icon' => 'src/assets/images/logo.svg',
                    'badge' => 'src/assets/images/logo.svg',
                    'url' => '/mon-mementos'
                ])
            );
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if ($report->isSuccess()) {
                echo "[v] Message sent successfully for subscription {$endpoint}.";
            } else {
                echo "[x] Message failed to send for subscription {$endpoint}: {$report->getReason()}";
            }
        }
    }

    /**
     * Methode pour le test de notification push  (TEST DEV)
     * si c'est le user que j'ai créé qui est le premier dans la table sinon utiliser sendNotification
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testNotification()
    {

        $subscription = PushSubscription::first();

        if (!$subscription) {
            return response()->json(['error' => 'No subscription found'], 404);
        }

        $auth = [
            'VAPID' => [
                'subject' => 'mailto:your-email@example.com',
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ];

        $webPush = new WebPush($auth);

        $webPush->queueNotification(
            Subscription::create([
                'endpoint' => $subscription->endpoint,
                'keys' => [
                    'auth' => $subscription->keys_auth,
                    'p256dh' => $subscription->keys_p256dh,
                ],
            ]),
            json_encode([
                'title' => "Hey ! {$subscription->user->pseudo}",
                'body' => 'C\'est l\'heure de réviser votre Mementos!',
                'icon' => 'src/assets/images/logo.svg',
                'badge' => 'src/assets/images/logo.svg',
                'url' => '/mon-mementos'
            ])
        );

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if ($report->isSuccess()) {
                echo "[v] Message sent successfully for subscription {$endpoint}.";
            } else {
                echo "[x] Message failed to send for subscription {$endpoint}: {$report->getReason()}";
            }
        }

        return response()->json(['success' => true]);
    }
}
