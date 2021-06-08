<?php

namespace App\Controller\Admin\Requests;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class ArticleStoreRequest
 *
 * @package App\Controller\Admin\Requests
 */
class ArticleStoreRequest
{
    private const REQUIRED       = ['title', 'description', 'body'];
    private const SESSION_PREFIX = 'form_article_';

    /**
     * @var array
     */
    private $data   = [];
    /**
     * @var array
     */
    private $files  = [];
    /**
     * @var array
     */
    private $errors = [];
    /**
     * @var Session
     */
    private $session;

    /**
     * ArticleStoreRequest constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param array $data
     * @param array $files
     *
     * @return bool
     */
    public function valid(array $data, array $files): bool
    {
        $this->data = $data;
        $this->files = $files;

        foreach (self::REQUIRED as $field) {
            if (empty($this->data[$field])) {
                $this->errors[$field] = 'Поле обязательно к заполнению';
            }
        }

        if (empty($this->errors)) {
            $this->deleteSession();
        } else {
            $this->saveSession();
        }

        return empty($this->errors);
    }

    private function saveSession()
    {
        foreach ($this->data as $field => $value) {
            $this->session->set(self::SESSION_PREFIX . $field, $value);
        }
    }

    public function deleteSession()
    {
        foreach ($this->data as $field => $value) {
            $this->session->remove(self::SESSION_PREFIX . $field);
        }
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (empty($this->errors)) {
            // @todo store file
        }

        return $this->data;
    }
}
