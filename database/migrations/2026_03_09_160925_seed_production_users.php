<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $users = [
            ['email' => 'jnt.marois@gmail.com', 'name' => 'Jonathan', 'work_mode' => 'atelier'],
            ['email' => 'lesvelosdarmorbzh@gmail.com', 'name' => 'Nicolas', 'work_mode' => 'comptoir'],
            ['email' => 'julien2705@gmail.com', 'name' => 'Julien', 'work_mode' => 'julien'],
        ];

        foreach ($users as $data) {
            DB::table('users')->updateOrInsert(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'work_mode' => $data['work_mode'],
                    'password' => Str::random(64),
                    'email_verified_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        //
    }
};
