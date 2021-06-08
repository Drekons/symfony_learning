<?php

namespace App\Homework;

class CommentContentProvider implements CommentContentProviderInterface
{

    private const PARAGRAPHS = [
        'Повседневная практика показывает, что внедрение современных методик позволяет оценить значение '
        . 'благоприятных перспектив.',
        'Мы вынуждены отталкиваться от того, что убеждённость некоторых оппонентов требует от нас анализа '
        . 'переосмысления внешнеэкономических политик.',
        'Прежде всего, повышение уровня гражданского сознания предоставляет широкие возможности для '
        . 'приоритизации разума над эмоциями.',
        'С другой стороны, постоянное информационно-пропагандистское обеспечение нашей деятельности однозначно '
        . 'определяет каждого участника как способного принимать собственные решения касаемо соответствующих '
        . 'условий активизации.',
        'Как принято считать, сделанные на базе интернет-аналитики выводы, превозмогая сложившуюся непростую '
        . 'экономическую ситуацию, своевременно верифицированы.',
    ];
    /**
     * @var PasteWords
     */
    private $pasteWords;

    /**
     * CommentContentProvider constructor.
     *
     * @param PasteWords $pasteWords
     */
    public function __construct(PasteWords $pasteWords)
    {
        $this->pasteWords = $pasteWords;
    }

    public function get(string $word = null, int $wordsCount = 0): string
    {
        $text = self::PARAGRAPHS[rand(0, count(self::PARAGRAPHS) - 1)];

        if ($word) {
            $text = $this->pasteWords->paste($text, $word, $wordsCount);
        }

        return $text;
    }
}
