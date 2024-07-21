<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Revision;
use Carbon\Carbon;

class UpdateRevisions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revisions:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mettre à jour les dates de révision des cartes qui n\'ont pas été révisées à leur date prévue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Récupérer les révisions qui n'ont pas été révisées à leur date prévue (hier)
        $yesterday = Carbon::yesterday();

        $revisions = Revision::where('dateRevision', $yesterday)->get();

        foreach ($revisions as $revision) {
            // Calculate the new date of revision based on the level
            $newDateRevision = Carbon::yesterday()->addDays(2 ** ($revision->niveau - 1));

            // Update the revision
            $revision->dateRevision = $newDateRevision;
            $revision->dateDerniereRevision = Carbon::today();
            $revision->save();
        }

        $this->info('Révisions mise à jour avec succès');
    }
}
