<?php

namespace App\Extensions\Contracts\Auth;

use Illuminate\Contracts\Auth\Authenticatable as DefaultAuthenticatable;

interface Authenticatable extends DefaultAuthenticatable
{
    /**
     * Get the name of password for the user.
     *
     * @return string
     */
    public static function getAuthPasswordName();
}