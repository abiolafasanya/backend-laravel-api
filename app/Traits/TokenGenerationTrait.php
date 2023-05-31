<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait TokenGenerationTrait
{
    protected function generateToken($user)
    {
        $applicationKey = config('app.secret', Str::random(68));
        return $user->createToken($applicationKey)->plainTextToken;
    }
}
