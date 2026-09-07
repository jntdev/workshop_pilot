<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MigrationSqliteCompatibilityTest extends TestCase
{
    #[Test]
    public function the_stock_movements_enum_migration_does_not_fail_under_sqlite(): void
    {
        config(['database.connections.sqlite_probe' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]]);

        $connection = DB::connection('sqlite_probe');
        $connection->getSchemaBuilder()->create('stock_movements', function ($table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->string('type');
            $table->timestamps();
        });

        $migration = require base_path('database/migrations/2026_09_07_105533_add_sale_movement_types_to_stock_movements_table.php');

        // Sous SQLite, la migration ne doit lever aucune exception (elle est un no-op sur ce driver)
        $this->assertSame('sqlite', $connection->getDriverName());

        Schema::swap($connection->getSchemaBuilder());
        DB::setDefaultConnection('sqlite_probe');

        try {
            $migration->up();
            $this->assertTrue(true);
        } finally {
            DB::setDefaultConnection('mysql');
            Schema::swap(DB::connection()->getSchemaBuilder());
        }
    }

    #[Test]
    public function the_stock_movements_type_column_stays_not_null_on_mysql(): void
    {
        $migration = require base_path('database/migrations/2026_09_07_105533_add_sale_movement_types_to_stock_movements_table.php');
        $migration->up();

        $column = DB::selectOne("SHOW COLUMNS FROM stock_movements WHERE Field = 'type'");

        $this->assertSame('NO', $column->Null);
    }
}
