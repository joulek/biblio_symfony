<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $email = (new Email())
                    ->from('joulekyosr123@gmail.com')
                    ->replyTo($data['email'])
                    ->to('joulekyosr123@gmail.com')
                    ->subject('Contact: ' . $data['subject'])
                    ->text($data['message'])
                    ->html("
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                            <h2 style='color: #6d2534;'>📧 Nouveau message de contact</h2>
                            <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                                <p><strong>Email de l'expéditeur :</strong> {$data['email']}</p>
                                <p><strong>Sujet :</strong> {$data['subject']}</p>
                            </div>
                            <div style='background: white; padding: 20px; border-left: 4px solid #6d2534;'>
                                <p><strong>Message :</strong></p>
                                <p style='line-height: 1.6;'>" . nl2br(htmlspecialchars($data['message'])) . "</p>
                            </div>
                        </div>
                    ");

                // Envoi DIRECT sans queue
                $mailer->send($email);

                $this->addFlash('success-contact', 'Votre message a été envoyé avec succès !');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'envoi: ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
