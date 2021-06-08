<?php

namespace App\Homework;

class PasteWords
{
    /**
     * @param string      $text
     * @param string|null $word
     * @param int         $wordsCount
     *
     * @return string
     */
    public function paste(string $text, string $word = null, int $wordsCount = 0): string
    {
        $textArr = explode(' ', $text);
        for ($i = 0; $i < $wordsCount; $i++) {
            array_splice($textArr, rand(0, count($textArr) - 1), 0, [$word]);
        }

        return join(' ', $textArr);
    }
}
