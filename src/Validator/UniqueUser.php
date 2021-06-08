<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class UniqueUser extends Constraint
{
    public $message = 'Пользователь с e-mail "{{ value }}" уже зарегистрирован.';
}
