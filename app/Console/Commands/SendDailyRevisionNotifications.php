<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Revision;
use Illuminate\Console\Command;
use Minishlink\WebPush\WebPush;
use App\Models\PushSubscription;
use Minishlink\WebPush\Subscription;

class SendDailyRevisionNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revision:sendDaily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie une notification de révision quotidienne.';

    /**
     * Execute the console command.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Exécute la commande pour envoyer les notifications de révision quotidienne.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = Carbon::now()->startOfDay();
        // Récupérer les utilisateurs ayant des révisions aujourd'hui
        $usersWithRevisions = Revision::whereDate('dateRevision', $today)
            ->with('user')
            ->get()
            ->groupBy('user_id')
            ->keys();

        foreach ($usersWithRevisions as $userId) {
            $user = User::find($userId);
            $subscriptions = PushSubscription::where('user_id', $user->id)->get();

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
                    $this->info("Message sent successfully for subscription {$endpoint}.");
                } else {
                    $this->error("Message failed to send for subscription {$endpoint}: {$report->getReason()}");
                }
            }
        }
    }
}
