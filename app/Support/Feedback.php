<?php

namespace App\Support;

class Feedback
{
    /**
     * Flash a success feedback message to the session.
     */
    public static function success(string $message): void
    {
        session()->flash('feedback', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    /**
     * Flash an error feedback message to the session.
     */
    public static function error(string $message): void
    {
        session()->flash('feedback', [
            'type' => 'error',
            'message' => $message,
        ]);
    }
}
