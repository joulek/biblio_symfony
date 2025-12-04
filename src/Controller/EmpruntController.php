<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Entity\Livre;
use App\Repository\EmpruntRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
public function emprunter(Livre $livre, EntityManagerInterface $em)
{
    // Vérifier stock
    if ($livre->getStock() <= 0) {
        $this->addFlash('error', 'Ce livre n\'est plus disponible.');
        return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
    }

    $emprunt = new Emprunt();
    $emprunt->setUser($this->getUser());
    $emprunt->setLivre($livre);
    $emprunt->setDateEmprunt(new \DateTime());
    $emprunt->setDateRetourPrevue((new \DateTime())->modify('+15 days'));
    $emprunt->setStatut("en_cours");

    // réduire le stock
    $livre->setStock($livre->getStock() - 1);

    $em->persist($emprunt);
    $em->flush();

    $this->addFlash('success', 'Livre emprunté avec succès !');

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
#[Route('/retourner/{id}', name: 'livre_retourner')]
public function retourner(Emprunt $emprunt, EntityManagerInterface $em)
{
    $emprunt->setDateRetourReelle(new \DateTime());
    $emprunt->setStatut("retourne");

    $livre = $emprunt->getLivre();
    $livre->setStock($livre->getStock() + 1);

    $em->flush();

    $this->addFlash('success', 'Livre retourné avec succès !');

    return $this->redirectToRoute('my_emprunts');
}

}
