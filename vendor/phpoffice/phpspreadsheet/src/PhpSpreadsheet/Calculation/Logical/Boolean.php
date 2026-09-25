<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Logical;

class Boolean
{
    /**
     * true.
     *
     * Returns the boolean true.
     *
     * Excel Function:
     *        =true()
     *
     * @return bool true
     */
    public static function true(): bool
    {
        return true;
    }

    /**
     * false.
     *
     * Returns the boolean false.
     *
     * Excel Function:
     *        =false()
     *
     * @return bool false
     */
    public static function false(): bool
    {
        return false;
    }
}
