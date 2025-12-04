<?php

namespace App\Service;

use App\Entity\Emprunt;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmpruntNotificationService
{
    private $mailer;
    private $senderEmail;

    public function __construct(MailerInterface $mailer, string $senderEmail)
    {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
    }

    public function envoyerRappelRetour(Emprunt $emprunt): void
    {
        $user = $emprunt->getUser();
        $livre = $emprunt->getLivre();
        $dateRetour = $emprunt->getDateRetourPrevue()->format('d/m/Y');

        $email = (new Email())
            ->from($this->senderEmail)
            ->to($user->getEmail())
            ->subject('Rappel : Retour de votre emprunt')
            ->html($this->getEmailContent($livre->getTitre(), $dateRetour));

        $this->mailer->send($email);
    }

    private function getEmailContent(string $titreLivre, string $dateRetour): string
    {
        return sprintf(
            '<h2>Rappel de retour d\'emprunt</h2>'
            . '<p>Bonjour,</p>'
            . '<p>Nous vous rappelons que vous devez retourner le livre <strong>%s</strong> d\'ici le <strong>%s</strong>.</p>'
            . '<p>Merci de le rapporter dans les délais pour éviter tout désagrément.</p>'
            . '<p>Cordialement,<br>Votre bibliothèque</p>',
            $titreLivre,
            $dateRetour
        );
    }
}
