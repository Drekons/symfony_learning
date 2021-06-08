<?php

namespace App\Homework;

interface ArticleContentProviderInterface
{

    public function get(int $paragraphs, string $word = null, int $wordsCount = 0, bool $parseMarkdown = true): string;

}
