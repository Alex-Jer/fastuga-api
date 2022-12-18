<?php

namespace App\Helpers;

class OrderHelper
{
    private static $currTickNumber = 0;

    public static function nextTicketNumber(): int
    {
        if (self::$currTickNumber === 99)
            self::$currTickNumber = 0;
        return self::$currTickNumber++;
    }
}
