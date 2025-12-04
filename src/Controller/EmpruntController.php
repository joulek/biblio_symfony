<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Entity\Livre;
use App\Repository\EmpruntRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EmpruntController extends AbstractController
{
    #[Route('/emprunt', name: 'app_emprunt')]
    public function index(): Response
    {
        return $this->render('emprunt/index.html.twig', [
            'controller_name' => 'EmpruntController',
        ]);
    }
   #[Route('/emprunter/{id}', name: 'livre_emprunter')]
public function emprunter(Livre $livre, EntityManagerInterface $em, EmpruntRepository $empruntRepo)
{
    $user = $this->getUser();
    
    // Vérifier si l'utilisateur a déjà 3 emprunts en cours
    $empruntsEnCours = $empruntRepo->findBy([
        'user' => $user,
        'statut' => 'en_cours'
    ]);
    
    if (count($empruntsEnCours) >= 3) {
        $this->addFlash('error', 'Vous avez déjà 3 emprunts en cours. Veuillez en retourner un avant d\'en emprunter un nouveau.');
        return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
    }

    // Vérifier si l'utilisateur a déjà emprunté ce livre et ne l'a pas encore retourné
    $dejaEmprunte = $empruntRepo->findOneBy([
        'user' => $user,
        'livre' => $livre,
        'statut' => 'en_cours'
    ]);
    
    if ($dejaEmprunte) {
        $this->addFlash('error', 'Vous avez déjà emprunté ce livre et ne l\'avez pas encore retourné.');
        return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
    }

    // Vérifier stock
    if ($livre->getStock() <= 0) {
        $this->addFlash('error', 'Ce livre n\'est plus disponible.');
        return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
    }

    $emprunt = new Emprunt();
    $emprunt->setUser($user);
    $emprunt->setLivre($livre);
    $emprunt->setDateEmprunt(new \DateTime());
    $emprunt->setDateRetourPrevue((new \DateTime())->modify('+1 month')); // 1 mois au lieu de 15 jours
    $emprunt->setStatut("en_cours");

    // réduire le stock
    $livre->setStock($livre->getStock() - 1);

    $em->persist($emprunt);
    $em->flush();

    $this->addFlash('success', 'Livre emprunté avec succès ! Date de retour prévue : ' . $emprunt->getDateRetourPrevue()->format('d/m/Y'));

    return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
}
#[Route('/my-emprunts', name: 'my_emprunts')]
public function myEmprunts(EmpruntRepository $repo): Response
{
    $user = $this->getUser();

    if (!$user) {
        throw $this->createAccessDeniedException();
    }

    $emprunts = $repo->findBy(['user' => $user], ['dateEmprunt' => 'DESC']);

    return $this->render('emprunt/my_emprunts.html.twig', [
        'emprunts' => $emprunts
    ]);
}

#[Route('/emprunt/{id}/retourner', name: 'app_emprunt_retourner', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function retourner(
    Request $request,
    Emprunt $emprunt,
    EntityManagerInterface $entityManager
): Response {
    // Vérifier que l'utilisateur connecté est bien celui qui a effectué l'emprunt
    if ($this->getUser() !== $emprunt->getUser()) {
        throw $this->createAccessDeniedException('Vous ne pouvez pas retourner cet emprunt.');
    }

    // Vérifier que l'emprunt est bien en cours
    if ($emprunt->getStatut() !== 'en_cours') {
        $this->addFlash('error', 'Cet emprunt a déjà été retourné.');
        return $this->redirectToRoute('app_emprunt_mes_emprunts');
    }

    // Mettre à jour l'emprunt
    $emprunt->setStatut('retourne');
    $emprunt->setDateRetourReelle(new \DateTime());
    
    // Remettre le livre en stock
    $livre = $emprunt->getLivre();
    $livre->setQte($livre->getQte() + 1);
    $livre->setStock($livre->getStock() + 1);

    $entityManager->persist($livre);
    $entityManager->flush();

    $this->addFlash('success', 'Livre retourné avec succès !');

    return $this->redirectToRoute('my_emprunts');
}

    #[Route('/emprunt/{id}/noter', name: 'app_emprunt_noter', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function noter(
        Request $request,
        Emprunt $emprunt,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier que l'utilisateur connecté est bien celui qui a effectué l'emprunt
        if ($this->getUser() !== $emprunt->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas noter cet emprunt.');
        }

        // Vérifier que l'emprunt est bien retourné
        if ($emprunt->getStatut() !== 'retourne') {
            $this->addFlash('error', 'Vous ne pouvez noter que les livres retournés.');
            return $this->redirectToRoute('my_emprunts');
        }

        // Vérifier si le livre a déjà été noté
        if ($emprunt->getNote() !== null) {
            $this->addFlash('error', 'Vous avez déjà noté ce livre.');
            return $this->redirectToRoute('my_emprunts');
        }

        // Récupérer et valider la note
        $note = (int) $request->request->get('note');
        
        if ($note < 1 || $note > 5) {
            $this->addFlash('error', 'La note doit être comprise entre 1 et 5.');
            return $this->redirectToRoute('my_emprunts');
        }

        // Enregistrer la note
        $emprunt->setNote($note);
        $entityManager->flush();

        $this->addFlash('success', 'Merci d\'avoir noté ce livre !');
        return $this->redirectToRoute('my_emprunts');
    }
}
