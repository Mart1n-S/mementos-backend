<?php

namespace Database\Seeders;

use App\Models\Carte;
use App\Models\Theme;
use App\Models\Revision;
use Illuminate\Database\Seeder;

class RevisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sélectionner un thème public aléatoire
        $theme = Theme::where('public', true)->inRandomOrder()->first();

        if ($theme) {
            $cartes = Carte::where('theme_id', $theme->id)->get();
            foreach ($cartes as $carte) {
                Revision::factory()->create([
                    'carte_id' => $carte->id
                ]);
            }
        } else {
            echo "Aucun thème public trouvé.";
        }
    }
}
