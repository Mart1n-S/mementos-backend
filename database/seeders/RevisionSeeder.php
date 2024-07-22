<?php

namespace Database\Seeders;

use App\Models\Carte;
use App\Models\Theme;
use App\Models\Revision;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RevisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sélectionner deux thèmes publics aléatoires
        $themes = Theme::where('public', true)->inRandomOrder()->take(2)->get();

        if ($themes->count() > 0) {
            foreach ($themes as $theme) {
                $cartes = Carte::where('theme_id', $theme->id)->get();
                $countCartes = $cartes->count();

                foreach ($cartes as $index => $carte) {
                    $niveau = rand(1, 2); // Niveaux 1 et 2 pour les révisions
                    $dateRevision = Carbon::now();

                    if ($niveau == 2) {
                        $dateRevision = $dateRevision->addDays(2); // Niveau 2: Révision doit être faite dans 2 jours
                    }

                    // Pour les deux dernières cartes, on met la date de révision à hier
                    if ($index >= $countCartes - 2) {
                        $dateRevision = Carbon::yesterday();
                        $niveau = 1; // On force le niveau à 1 pour ces cartes
                    }

                    Revision::factory()->create([
                        'carte_id' => $carte->id,
                        'niveau' => $niveau,
                        'dateRevision' => $dateRevision,
                    ]);
                }
            }
        } else {
            echo "Aucun thème public trouvé.";
        }
    }
}
