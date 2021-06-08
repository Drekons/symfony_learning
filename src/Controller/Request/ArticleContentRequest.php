<?php

namespace App\Controller\Request;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class ArticleContentRequest
 *
 * @package App\Controller\Request
 */
class ArticleContentRequest
{
    /**
     * @var int
     */
    private $paragraphs = 0;
    /**
     * @var string|null
     */
    private $word = null;
    /**
     * @var int
     */
    private $wordsCount = 0;
    /**
     * @var Request
     */
    private $request;

    /**
     * ArticleContentRequest constructor.
     */
    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->valid();
    }

    /**
     * @throws \Exception
     */
    protected function valid()
    {
        $this->paragraphs = (int)$this->request->query->get('paragraphs');

        if ($this->paragraphs < 1) {
            $this->paragraphs = 0;
        }

        $this->word = $this->request->query->get('word');
        $this->wordsCount = (int)$this->request->query->get('wordsCount');

        if ($this->word && $this->wordsCount < 1) {
            $this->wordsCount = 0;
        }
    }

    /**
     * @return int
     */
    public function getParagraphs(): int
    {
        return $this->paragraphs;
    }

    /**
     * @return null
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * @return int
     */
    public function getWordsCount(): int
    {
        return $this->wordsCount;
    }
}
