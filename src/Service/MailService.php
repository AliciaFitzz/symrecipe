<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailService
{

    private MailerInterface $mailer;


    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    // ENVOIE D'UN EMAIL
    public function sendEmail(string $from, string $subject, string $htmlTemplate, array $context, string $to = 'admin@symrecipe.com'): void
    {
        $email = (new TemplatedEmail()) // Créer un template pour les emails
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($htmlTemplate) // Renvoie vers notre template
            ->context($context);

        $this->mailer->send($email);
    }
}
