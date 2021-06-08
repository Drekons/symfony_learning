<?php

namespace App\Homework;

interface CommentContentProviderInterface
{
    public function get(string $word = null, int $wordsCount = 0): string;
}
