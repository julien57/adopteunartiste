<?php

namespace App\Services\Mail;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class SendMail
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $environment;

    public function __construct(\Swift_Mailer $mailer, Environment $environment)
    {
        $this->mailer = $mailer;
        $this->environment = $environment;
    }

    public function newPasswordUser(string $password, User $user)
    {
        $message = (new \Swift_Message('AdopteUnArtiste - Mot de passe provisoire'))
            ->setFrom('contact@adopteunartiste.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->environment->render(
                    'mail/password_new_user.html.twig', [
                        'password' => $password,
                        'user' => $user,
                    ]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}
