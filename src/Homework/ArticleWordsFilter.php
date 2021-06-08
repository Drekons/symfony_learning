<?php

namespace App\Homework;

class ArticleWordsFilter
{

    private const BLOCKED_WORDS = ['стакан', 'вода', 'вилка'];

    public function filter($string, $words = [])
    {
        $words = array_merge($words, self::BLOCKED_WORDS);

        foreach ($words as $word) {
            $string = preg_replace('/(' . ucfirst($word) . '.*\s)|(' . $word . '.*\s)/mU', '', $string);
            $string = preg_replace('/ (\s'. ucfirst($word) . '.*)|(\s' . $word . '.*)/mU', '', $string);
        }

        return $string;
    }
}
