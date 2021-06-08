<?php

namespace App\Homework;

class RegistrationSpamFilter
{
    private const AVAILABLE_DOMAINS = ['.ru', '.com', '.org'];

    public function filter(string $email): bool
    {
        if (!preg_match('(\\' . join('$|\.', self::AVAILABLE_DOMAINS) . '$)', $email) > 0) {
            return true;
        }

        return false;
    }
}
