<?php

namespace App\EventSubscriber;

use App\Entity\Article;
use App\Events\ArticleCreatedEvent;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ArticleCreatedSubscriber
 *
 * @package App\EventSubscriber
 */
class ArticleCreatedSubscriber implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ArticleCreatedSubscriber constructor.
     *
     * @param  Mailer          $mailer
     * @param  UserRepository  $userRepository
     */
    public function __construct(Mailer $mailer, UserRepository $userRepository)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

    public function onArticleCreated(ArticleCreatedEvent $event)
    {
        $author = $event->getArticle()->getAuthor();
        $needSendEmail = $author ? !in_array('ROLE_ADMIN', $author->getRoles()) : true;

        if ($needSendEmail) {
            $this->sendArticleCreatedEmail($event->getArticle());
        }
    }

    private function sendArticleCreatedEmail(Article $article)
    {
        foreach ($this->userRepository->getAdminsList() as $user) {
            $this->mailer->sendArticleCreated($article, $user);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            ArticleCreatedEvent::class => 'onArticleCreated',
        ];
    }
}
