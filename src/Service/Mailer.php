<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    const SITE_NAME = 'Spill-Coffee-On-The-Keyboard';

    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var string
     */
    private $fromEmail;
    /**
     * @var string
     */
    private $fromName;

    public function __construct(MailerInterface $mailer, string $fromEmail, string $fromName)
    {
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    public function sendWelcomeMail(User $user)
    {
        $this->send('email/welcome.html.twig', 'Добро пожаловать на', $user);
    }

    private function send(string $template, string $subject, User $user, \Closure $callback = null)
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to(new Address($user->getEmail(), $user->getFirstName()))
            ->htmlTemplate($template)
            ->subject($subject . ' ' . self::SITE_NAME);

        if ($callback) {
            $callback($email);
        }

        $this->mailer->send($email);
    }

    public function sendWeeklyNewsletter(User $user, array $articles)
    {
        $this->send(
            'email/news-letter.html.twig',
            'Еженедельная рассылка',
            $user,
            function (TemplatedEmail $email) use ($articles) {
                $email
                    ->context(
                        [
                            'articles' => $articles,
                        ]
                    );
            }
        );
    }

    public function sendAdminStatisticReport(User $user, $fp)
    {
        $this->send(
            'email/admin-statistic.html.twig',
            'Статистика администратора',
            $user,
            function (TemplatedEmail $email) use ($fp) {
                $email
                    ->attach($fp, 'report_' . date('Y_m_d') . '.csv');
            }
        );
    }

    public function sendArticleCreated(Article $article, User $user)
    {
        $this->send(
            'email/new-article.html.twig',
            "Новая статья на сайте",
            $user,
            function (TemplatedEmail $email) use ($article) {
                $email
                    ->context(
                        [
                            'article' => $article,
                        ]
                    );
            }
        );
    }
}
