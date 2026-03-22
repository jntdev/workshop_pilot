<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\MessageReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedManyMessagesForScrolling();
    }

    private function seedManyMessagesForScrolling(): void
    {
        $nicolas = User::where('email', 'lesvelosdarmorbzh@gmail.com')->first();
        $jonathan = User::where('email', 'jnt.marois@gmail.com')->first();
        $julien = User::where('email', 'julien2705@gmail.com')->first();

        if (! $nicolas || ! $jonathan) {
            $this->command->warn('Users not found — skipping MessageSeeder.');

            return;
        }

        $categories = ['accueil', 'atelier', 'location', 'autre'];
        $contacts = [
            ['name' => 'Jean Dupont', 'phone' => '06 12 34 56 78', 'email' => 'jean.dupont@email.com'],
            ['name' => 'Marie Durand', 'phone' => '06 55 44 33 22', 'email' => 'marie.durand@gmail.com'],
            ['name' => 'Pierre Martin', 'phone' => '06 98 76 54 32', 'email' => 'p.martin@outlook.fr'],
            ['name' => 'Sophie Leroy', 'phone' => '06 11 22 33 44', 'email' => 'sophie.leroy@gmail.com'],
            ['name' => 'Lucas Bernard', 'phone' => '06 77 88 99 00', 'email' => 'lucas.b@email.fr'],
            ['name' => 'Emma Petit', 'phone' => '06 33 22 11 00', 'email' => 'emma.petit@yahoo.fr'],
        ];

        $accueilContents = [
            'Client vient récupérer son vélo demain matin.',
            'Demande de devis pour conversion VAE.',
            'Client souhaite essayer un VTC avant achat.',
            'Rappeler le client pour confirmer rendez-vous.',
            'Nouveau client intéressé par nos services.',
            'Client a laissé son numéro pour rappel.',
            'Demande d\'information sur les tarifs location.',
            'Client passera en fin de journée.',
        ];

        $atelierContents = [
            'Vélo prêt à récupérer - révision complète terminée.',
            'Pneu crevé réparé, à prévenir le client.',
            'Freins à disque remplacés sur VTT client.',
            'Dérailleur ajusté, vélo opérationnel.',
            'Batterie VAE testée, autonomie OK.',
            'Câbles de frein à changer sur vélo bleu.',
            'Roue arrière voilée à dévoiler.',
            'Chaîne usée à remplacer.',
            'Fourche suspendue révisée.',
            'Éclairage avant défaillant à réparer.',
        ];

        $locationContents = [
            'Réservation confirmée pour le week-end.',
            'Vélo à récupérer chez client après location.',
            'Prolongation de location demandée.',
            'Nouveau contrat de location longue durée.',
            'Retour de location prévu demain.',
            'Client souhaite changer de vélo en cours de location.',
            'Caution à rembourser après vérification.',
        ];

        $autreContents = [
            'Commande pièces détachées à passer.',
            'Facturation envoyée au comptable.',
            'Inventaire à faire en fin de semaine.',
            'Fournisseur rappelle demain.',
            'Planning de la semaine à valider.',
            'Réunion équipe jeudi 14h.',
        ];

        $contentsByCategory = [
            'accueil' => $accueilContents,
            'atelier' => $atelierContents,
            'location' => $locationContents,
            'autre' => $autreContents,
        ];

        $replyContents = [
            'OK, c\'est noté.',
            'Je m\'en occupe.',
            'Client prévenu.',
            'Fait, merci !',
            'Je vérifie et je te dis.',
            'Parfait, je prépare le nécessaire.',
            'C\'est pris en charge.',
            'Je le rappelle dans l\'heure.',
            'Terminé, tout est OK.',
            'Je confirme, c\'est fait.',
            'J\'ai bien reçu l\'info.',
            'Je m\'occupe de ça cet après-midi.',
            'Super, merci pour l\'info.',
            'Je vais vérifier ça.',
            'D\'accord, je le fais maintenant.',
        ];

        $users = [$nicolas, $jonathan];
        if ($julien) {
            $users[] = $julien;
        }

        for ($i = 0; $i < 30; $i++) {
            $category = $categories[array_rand($categories)];
            $contents = $contentsByCategory[$category];
            $content = $contents[array_rand($contents)];
            $contact = $contacts[array_rand($contacts)];

            $author = $users[$i % count($users)];
            $forSelf = $i % 7 === 0;
            $isRead = $i % 3 === 0;
            $isResolved = $i % 10 === 0;
            $hasContact = $i % 2 === 0;

            $recipients = array_filter($users, fn ($u) => $u->id !== $author->id);
            $recipient = $forSelf ? null : array_values($recipients)[0];

            $message = Message::factory()
                ->state([
                    'author_user_id' => $author->id,
                    'recipient_user_id' => $recipient?->id,
                ])
                ->when($isRead && ! $forSelf, fn ($f) => $f->read())
                ->when($isResolved, fn ($f) => $f->resolved())
                ->create([
                    'category' => $category,
                    'content' => $content.' (Message #'.($i + 1).')',
                    'contact_name' => $hasContact ? $contact['name'] : null,
                    'contact_phone' => $hasContact ? $contact['phone'] : null,
                    'contact_email' => $hasContact && $i % 3 === 0 ? $contact['email'] : null,
                    'created_at' => now()->subHours(rand(1, 72))->subMinutes(rand(0, 59)),
                ]);

            $nbReplies = $forSelf ? 0 : rand(0, 15);

            for ($j = 0; $j < $nbReplies; $j++) {
                $replyAuthor = $users[$j % count($users)];
                $replyRecipient = array_values(array_filter($users, fn ($u) => $u->id !== $replyAuthor->id))[0];
                $replyIsRead = $j % 2 === 0 || rand(0, 1) === 1;
                $replyContent = $replyContents[array_rand($replyContents)];

                MessageReply::factory()
                    ->state([
                        'author_user_id' => $replyAuthor->id,
                        'recipient_user_id' => $replyRecipient->id,
                    ])
                    ->when($replyIsRead, fn ($f) => $f->read())
                    ->create([
                        'message_id' => $message->id,
                        'content' => $replyContent.' (Réponse #'.($j + 1).')',
                        'created_at' => $message->created_at->addMinutes(($j + 1) * rand(5, 30)),
                    ]);
            }
        }
    }
}
