<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuthorizedEmail extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'email',
    ];

    public function setEmailAttribute(string $value): void
    {
        $this->attributes['email'] = Str::lower($value);
    }
}
