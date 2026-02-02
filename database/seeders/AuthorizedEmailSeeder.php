<?php

namespace Database\Seeders;

use App\Models\AuthorizedEmail;
use Illuminate\Database\Seeder;

class AuthorizedEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emails = [
            'jnt.marois@gmail.com',
            'lesvelosdarmorbzh@gmail.com',
        ];

        foreach ($emails as $email) {
            AuthorizedEmail::firstOrCreate(['email' => $email]);
        }

        $this->command->info('✓ '.count($emails).' emails autorisés ajoutés.');
    }
}
