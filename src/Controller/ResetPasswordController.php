<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    #[Route('/mot-de-passe-oublie', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, UserRepository $repo, MailerInterface $mailer, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $repo->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'Aucun compte trouvé avec cet email.');
                return $this->redirectToRoute('app_forgot_password');
            }

            // Générer un code à 6 chiffres
            $code = random_int(100000, 999999);
            $user->setResetCode($code);

            $em->flush();

            // Envoyer l'email
            $mail = (new Email())
                ->from('noreply@tonsite.com')
                ->to($user->getEmail())
                ->subject('Code de réinitialisation')
                ->html("<p>Votre code de réinitialisation est : <strong>$code</strong></p>");

            $mailer->send($mail);

            return $this->redirectToRoute('app_verify_code', ['email' => $email]);
        }

        return $this->render('security/forgot_password.html.twig');
    }

    #[Route('/verify-code', name: 'app_verify_code')]
    public function verifyCode(Request $request, UserRepository $repo): Response
    {
        $email = $request->query->get('email');

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $code = $request->request->get('code');

            $user = $repo->findOneBy(['email' => $email]);

            if (!$user || $user->getResetCode() != $code) {
                $this->addFlash('error', 'Code incorrect.');
                return $this->redirectToRoute('app_verify_code', ['email' => $email]);
            }

            return $this->redirectToRoute('app_reset_password', ['email' => $email]);
        }

        return $this->render('security/verify_code.html.twig', [
            'email' => $email
        ]);
    }

    #[Route('/reset-password', name: 'app_reset_password')]
    public function resetPassword(
        Request $request,
        UserRepository $repo,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        $email = $request->query->get('email');
        $user = $repo->findOneBy(['email' => $email]);

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');

            $hashed = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashed);
            $user->setResetCode(null);

            $em->flush();

            $this->addFlash('success', 'Mot de passe réinitialisé avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'email' => $email
        ]);
    }
}
