<?php

namespace Database\Seeders;

use App\Models\Theme;
use App\Models\Categorie;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $themesByCategory = [
            'Histoire' => ['La Renaissance', 'La Révolution française'],
            'Programmation' => ['Introduction à Python', 'Développement Web avec JavaScript'],
            'Anglais' => ['Grammaire anglaise pour débutants', 'Conversation avancée'],
            'Pays' => ['Culture japonaise', 'Histoire des États-Unis'],
            'Autres' => ['Sujets divers en science', 'Innovations en technologie'],
            'Science' => ['Les bases de la physique quantique', 'Biologie moderne'],
            'Mathématiques' => ['Algèbre de base', 'Calcul différentiel'],
            'Art' => ['Impressionnisme', 'Art contemporain'],
            'Musique' => ['Histoire du jazz', 'Techniques de composition'],
            'Cinéma' => ['Le cinéma d\'horreur', 'L\'ère du cinéma muet'],
            'Technologie' => ['Intelligence Artificielle', 'Blockchain expliqué'],
            'Santé' => ['Nutrition et bien-être', 'Premiers secours'],
            'Sport' => ['Football moderne', 'Psychologie du sport'],
            'Nature' => ['Conservation de la biodiversité', 'Changement climatique'],
            'Gastronomie' => ['Cuisines du monde', 'Techniques de pâtisserie'],
            'Finance' => ['Investissement pour débutants', 'Crypto-monnaies'],
            'Politique' => ['Politique comparée', 'Élections et démocratie'],
            'Voyage' => ['Voyager en Asie', 'Écotourisme'],
            'Education' => ['Systèmes éducatifs mondiaux', 'E-learning et technologie éducative'],
            'Mode' => ['Histoire de la mode', 'Tendances mode 2024']
        ];

        // Crée 2 thèmes pour chaque catégorie
        foreach ($themesByCategory as $categoryName => $themes) {
            $category = Categorie::where('nom', $categoryName)->first();
            foreach ($themes as $themeName) {
                Theme::factory()->create([
                    'nom' => $themeName,
                    'category_id' => $category->id,
                    'user_id' => rand(1, 10)
                ]);
            }
        }
    }
}
