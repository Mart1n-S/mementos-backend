<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Categorie>
 */
class CategorieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    /**
     * Predefined categories.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function predefined()
    {
        $categories = [
            ['nom' => 'Histoire', 'pathImage' => 'histoire.jpg', 'couleur' => '#A88DFF'],
            ['nom' => 'Programmation', 'pathImage' => 'programmation.jpg', 'couleur' => '#EFD81D'],
            ['nom' => 'Anglais', 'pathImage' => 'anglais.jpg', 'couleur' => '#6ED3EA'],
            ['nom' => 'Pays', 'pathImage' => 'pays.jpg', 'couleur' => '#50db4d'],
            ['nom' => 'Autres', 'pathImage' => 'autres.jpg', 'couleur' => '#636363'],
            ['nom' => 'Science', 'pathImage' => 'science.jpg', 'couleur' => '#FFD700'],
            ['nom' => 'Mathématiques', 'pathImage' => 'mathematiques.jpg', 'couleur' => '#FF4500'],
            ['nom' => 'Art', 'pathImage' => 'art.jpg', 'couleur' => '#6A5ACD'],
            ['nom' => 'Musique', 'pathImage' => 'musique.jpg', 'couleur' => '#00BFFF'],
            ['nom' => 'Cinéma', 'pathImage' => 'cinema.jpg', 'couleur' => '#FF6347'],
            ['nom' => 'Technologie', 'pathImage' => 'technologie.jpg', 'couleur' => '#0A75AD'],
            ['nom' => 'Santé', 'pathImage' => 'sante.jpg', 'couleur' => '#E34234'],
            ['nom' => 'Sport', 'pathImage' => 'sport.jpg', 'couleur' => '#228B22'],
            ['nom' => 'Nature', 'pathImage' => 'nature.jpg', 'couleur' => '#6B8E23'],
            ['nom' => 'Gastronomie', 'pathImage' => 'gastronomie.jpg', 'couleur' => '#D2691E'],
            ['nom' => 'Finance', 'pathImage' => 'finance.jpg', 'couleur' => '#FFDF00'],
            ['nom' => 'Politique', 'pathImage' => 'politique.jpg', 'couleur' => '#B22222'],
            ['nom' => 'Voyage', 'pathImage' => 'voyage.jpg', 'couleur' => '#2F4F4F'],
            ['nom' => 'Education', 'pathImage' => 'education.jpg', 'couleur' => '#4682B4'],
            ['nom' => 'Mode', 'pathImage' => 'mode.jpg', 'couleur' => '#FF69B4']
        ];

        foreach ($categories as $category) {
            $this->create($category);
        }

        return $this;
    }
}
