<?php

trait AP_Pluralize
{
    /**
     * Pluralizes a word
     *
     * @param string $singular Singular form of word
     */
    public static function pluralize($singular)
    {
        $last_letter = strtolower($singular[strlen($singular) - 1]);
        switch ($last_letter) {
            case 'y':
                return substr($singular, 0, -1) . 'ies';
            case 's':
                return $singular . 'es';
            default:
                return $singular . 's';
        }
    }
}
