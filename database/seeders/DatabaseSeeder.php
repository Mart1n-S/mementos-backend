<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Categorie;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer un utilisateur spécifique
        User::create([
            'pseudo' => 'Martin',
            'email' => 'martin@gmail.com',
            'password' => bcrypt('M@rt1n13'),
            'niveauRevision' => 7,
            'email_verified_at' => now()
        ]);

        // Créer 10 utilisateurs avec des emails vérifiés
        User::factory(10)->create();

        // Créer 5 utilisateurs avec des emails non vérifiés
        User::factory(5)->unverified()->create();

        // Créer des catégories prédéfinies
        Categorie::factory()->predefined();

        // Attendre que les catégories soient créées et ensuite créer des thèmes pour chaque catégorie, puis créer des cartes pour chaque thème
        // puis créer une révision pour un user
        $this->call([
            ThemeSeeder::class,
            CarteSeeder::class,
            RevisionSeeder::class,
        ]);
    }
}
