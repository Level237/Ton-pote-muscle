<?php

namespace Database\Seeders;

use App\Enums\BilanQuestionType;
use App\Models\Bilan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BilanSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBilan('Bilan de santé', 'Questionnaire de santé à remplir avant de commencer.', [
            [
                'title' => 'Avez-vous une blessure ou une douleur en cours ?',
                'type' => BilanQuestionType::Choice,
                'options' => ['Non', 'Oui, légère', 'Oui, gênante', 'Oui, je suis suivi'],
            ],
            [
                'title' => 'Précisez si besoin (zone, ancienneté, consignes médicales)',
                'type' => BilanQuestionType::Text,
            ],
            [
                'title' => 'Comment évaluez-vous votre énergie au quotidien ?',
                'type' => BilanQuestionType::Rating,
            ],
            [
                'title' => 'Qualité de sommeil',
                'type' => BilanQuestionType::Choice,
                'options' => ['Moins de 5 h', '5 à 6 h', '7 à 8 h', 'Plus de 8 h'],
            ],
            [
                'title' => 'Suivi médical ou traitement en cours',
                'type' => BilanQuestionType::Text,
            ],
        ]);

        $this->seedBilan('Bilan de départ', 'Objectifs, disponibilités et contexte d’entraînement.', [
            [
                'title' => 'Objectif principal',
                'type' => BilanQuestionType::Choice,
                'options' => [
                    'Perte de poids',
                    'Prise de muscle',
                    'Recomposition corporelle',
                    'Condition physique',
                    'Reprendre en douceur',
                    'Performance sportive',
                    'Autre',
                ],
            ],
            [
                'title' => 'Décrivez votre objectif',
                'type' => BilanQuestionType::Text,
            ],
            [
                'title' => 'Séances possible par semaine',
                'type' => BilanQuestionType::Choice,
                'options' => ['1', '2', '3', '4', '5', '6+'],
            ],
            [
                'title' => 'Durée d’une séance',
                'type' => BilanQuestionType::Choice,
                'options' => ['30 min', '45 min', '60 min', '90 min'],
            ],
            [
                'title' => 'Lieu',
                'type' => BilanQuestionType::Choice,
                'options' => ['Salle', 'Maison', 'Extérieur'],
            ],
            [
                'title' => 'Matériel disponible',
                'type' => BilanQuestionType::Text,
            ],
            [
                'title' => 'Quel résultat souhaitez-vous dans 3 mois ?',
                'type' => BilanQuestionType::Text,
            ],
            [
                'title' => 'Motivation',
                'type' => BilanQuestionType::Rating,
            ],
        ]);

        $this->seedBilan('Bilan de mesure', 'Poids, mensurations, âge et taille.', [
            [
                'title' => 'Quel est votre poids actuel',
                'type' => BilanQuestionType::Number,
            ],
            [
                'title' => 'Tour de taille (cm)',
                'type' => BilanQuestionType::Number,
            ],
            [
                'title' => 'Tour de hanches (cm)',
                'type' => BilanQuestionType::Number,
            ],
            [
                'title' => 'Tour de poitrine (cm)',
                'type' => BilanQuestionType::Number,
            ],
            [
                'title' => 'Tour de bras (cm)',
                'type' => BilanQuestionType::Number,
            ],
            [
                'title' => 'Tour de cuisse (cm)',
                'type' => BilanQuestionType::Number,
            ],
            [
                'title' => 'Âge',
                'type' => BilanQuestionType::Text,
            ],
            [
                'title' => 'Taille',
                'type' => BilanQuestionType::Text,
            ],
        ]);
    }

    /**
     * @param  list<array{title: string, type: BilanQuestionType, options?: list<string>}>  $questions
     */
    private function seedBilan(string $title, string $description, array $questions): void
    {
        $bilan = Bilan::query()->firstOrCreate(
            ['title' => $title],
            [
                'id' => (string) Str::uuid(),
                'description' => $description,
            ],
        );

        $bilan->update(['description' => $description]);
        $bilan->questions()->delete();

        foreach ($questions as $index => $question) {
            $bilan->questions()->create([
                'position' => $index + 1,
                'title' => $question['title'],
                'type' => $question['type'],
                'options' => $question['options'] ?? null,
            ]);
        }
    }
}
