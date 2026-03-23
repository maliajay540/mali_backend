<?php

namespace App\Libraries;

/**
 * Stores user context for the request.
 * Required because dynamic properties on Request object are deprecated in PHP 8.2
 */
class UserContext
{
    private static $user;

    public static function setUser($user)
    {
        self::$user = $user;
    }

    public static function getUser()
    {
        return self::$user;
    }

    public static function getUserId()
    {
        return self::$user->uid ?? null;
    }

    public static function isPremium()
    {
        return isset(self::$user->is_premium) && (bool)self::$user->is_premium;
    }
}
