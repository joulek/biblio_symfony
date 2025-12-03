<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeItem;
use App\Entity\User;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class CommandeController extends AbstractController
{
    #[Route('/commande/valider', name: 'app_commande_valider', methods: ['POST'])]
    public function valider(
        Security $security,
        PanierRepository $panierRepo,
        EntityManagerInterface $em,
        Request $request,
        MailerInterface $mailer
    ): Response {
        $user = $security->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $numTel = $request->request->get('numTel');
        $adresse = $request->request->get('adresse');
        $modePaiement = $request->request->get('mode_paiement');

        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setNumTel($numTel);
        $em->persist($user);

        $panier = $panierRepo->findOneBy(['user' => $user]);
        if (!$panier || $panier->getPanierItems()->count() === 0) {
            $this->addFlash('error', 'Votre panier est vide ❌');
            return $this->redirectToRoute('app_panier');
        }

        $totalCommande = $panier->getTotal();

        $commande = new Commande();
        $commande->setUser($user);
        $commande->setTotal($totalCommande);
        $commande->setAdresse($adresse);
        $commande->setModePaiement($modePaiement);

        foreach ($panier->getPanierItems() as $pItem) {
            $item = new CommandeItem();
            $item->setCommande($commande);
            $item->setLivre($pItem->getLivre());
            $item->setQuantite($pItem->getQuantite());
            $item->setPrix($pItem->getLivre()->getPrixunitaire());
            $em->persist($item);
        }

        $em->persist($commande);

        $em->flush();


        try {
            $em->refresh($commande);

            $pdfContent = $this->genererPdfFacture($commande);

            $email = (new Email())
                ->from('joulekyosr123@gmail.com')
                ->to($user->getEmail())
                ->subject('📦 Confirmation de commande n°' . $commande->getId())
                ->html($this->renderView('emails/confirmation_commande.html.twig', [
                    'commande' => $commande,
                    'user' => $user
                ]))
                ->attach($pdfContent, 'facture-commande-' . $commande->getId() . '.pdf', 'application/pdf');

            $mailer->send($email);
            $this->addFlash('commande_success', '🎉 Commande validée ! Facture envoyée par email.');

        } catch (\Exception $e) {
            $this->addFlash('warning', 'Commande enregistrée mais échec envoi de l\'email.');
            error_log("Erreur email : " . $e->getMessage());
        }

        foreach ($panier->getPanierItems() as $pItem) {
            $em->remove($pItem);
        }
        $panier->setTotal(0);
        $em->flush();

        return $this->redirectToRoute('app_panier');
    }


    private function genererPdfFacture(Commande $commande): string
    {
        $html = $this->renderView('commande/facture.html.twig', [
            'commande' => $commande,
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    #[Route('/mes-commandes', name: 'app_commandes')]
    public function historique(Security $security): Response
    {
        $user = $security->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('commande/index.html.twig', [
            'commandes' => $user->getCommandes()
        ]);
    }

    #[Route('/commande/{id}/facture', name: 'app_commande_facture')]
    public function facture(EntityManagerInterface $em, int $id): Response
    {
        $commande = $em->getRepository(Commande::class)
            ->createQueryBuilder('c')
            ->leftJoin('c.commandeItems', 'ci')
            ->addSelect('ci')
            ->leftJoin('ci.livre', 'l')
            ->addSelect('l')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$commande) {
            throw $this->createNotFoundException('Commande introuvable 🚫');
        }

        if ($commande->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $pdfContent = $this->genererPdfFacture($commande);

        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="facture-commande-' . $commande->getId() . '.pdf"',
            ]
        );
    }
}
