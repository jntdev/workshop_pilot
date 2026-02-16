<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            // 1. Authorized emails for authentication
            AuthorizedEmailSeeder::class,

            // 2. Clients (needed by quotes and reservations)
            ClientSeeder::class,

            // 3. Demo quotes and invoices
            QuoteSeeder::class,

            // 4. Historical invoices for dashboard stats
            HistoricalInvoicesSeeder::class,

            // 5. Calculate KPIs from actual invoice data
            MonthlyKpiSeeder::class,

            // 6. Reservations de location vélos
            ReservationSeeder::class,
        ]);
    }
}
