<?php

namespace App\Services\Agenda;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AgendaVersioner
{
    private const CACHE_KEY = 'agenda_version';

    private const CACHE_TTL = 60; // seconds

    /**
     * Get the current agenda version.
     */
    public function current(): int
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $version = DB::table('agenda_meta')
                ->where('id', 1)
                ->value('agenda_version');

            return $version !== null ? (int) $version : 1;
        });
    }

    /**
     * Increment the agenda version atomically and return the new value.
     * Uses UPDATE ... RETURNING for atomicity (prevents race conditions).
     */
    public function bump(): int
    {
        // Use a transaction with row locking to prevent race conditions
        $newVersion = DB::transaction(function () {
            // Lock the row for update
            $row = DB::table('agenda_meta')
                ->where('id', 1)
                ->lockForUpdate()
                ->first();

            if (! $row) {
                // Should never happen if migration ran correctly
                DB::table('agenda_meta')->insert([
                    'id' => 1,
                    'agenda_version' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return 1;
            }

            $newVersion = $row->agenda_version + 1;

            DB::table('agenda_meta')
                ->where('id', 1)
                ->update([
                    'agenda_version' => $newVersion,
                    'updated_at' => now(),
                ]);

            return $newVersion;
        });

        // Update the cache with the new version
        Cache::put(self::CACHE_KEY, $newVersion, self::CACHE_TTL);

        return $newVersion;
    }

    /**
     * Invalidate the cached version (useful for testing).
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
