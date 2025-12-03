<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Entity\Panier;
use App\Entity\PanierItem;
use App\Repository\PanierRepository;
use App\Repository\PanierItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;


final class PanierController extends AbstractController
{
   #[Route('/panier', name: 'app_panier')]
public function index(
    PanierRepository $panierRepo,
    Security $security
): Response {

    $user = $security->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    $panier = $panierRepo->findOneBy(['user' => $user]);

    return $this->render('panier/index.html.twig', [
        'panier' => $panier
    ]);
}


    #[Route('/panier/add/{id}', name: 'panier_add', methods: ['POST'])]
    public function addToPanier(
        Livre $livre,
        PanierRepository $panierRepo,
        PanierItemRepository $itemRepo,
        EntityManagerInterface $em,
        Security $security
    ): Response {

        /** 1) Vérifier l'utilisateur connecté */
        $user = $security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        /** 2) Récupérer ou créer le panier */
        $panier = $panierRepo->findOneBy(['user' => $user]);
        if (!$panier) {
            $panier = new Panier();
            $panier->setUser($user);
            $em->persist($panier);
        }

        /** 3) Vérifier si le livre est déjà dans le panier */
        $item = $itemRepo->findOneBy([
            'panier' => $panier,
            'livre' => $livre
        ]);

        if ($item) {
            // augmenter la quantité
            $item->setQuantite($item->getQuantite() + 1);
        } else {
            // créer un nouvel item de panier
            $item = new PanierItem();
            $item->setPanier($panier);
            $item->setLivre($livre);
            $item->setQuantite(1);
            $em->persist($item);
        }
        $total = 0;
foreach ($panier->getPanierItems() as $i) {
    $total += $i->getLivre()->getPrixunitaire() * $i->getQuantite();
}
$panier->setTotal($total);


        /** 4) Sauvegarde */
        $em->flush();

        $this->addFlash('success', 'Livre ajouté au panier !');

    return $this->redirectToRoute('app_panier');
    }


    #[Route('/panier/increment/{id}', name: 'panier_increment')]
public function increment(
    PanierItem $item,
    EntityManagerInterface $em
): Response {
    $item->setQuantite($item->getQuantite() + 1);

    // recalcul total de la ligne
    $item->setPrixTotal($item->getLivre()->getPrixunitaire() * $item->getQuantite());

    // recalcul total panier
    $panier = $item->getPanier();
    $this->updatePanierTotal($panier);

    $em->flush();
    return $this->redirectToRoute('app_panier');
}

#[Route('/panier/decrement/{id}', name: 'panier_decrement')]
public function decrement(
    PanierItem $item,
    EntityManagerInterface $em
): Response {
    if ($item->getQuantite() > 1) {
        $item->setQuantite($item->getQuantite() - 1);
        $item->setPrixTotal($item->getLivre()->getPrixunitaire() * $item->getQuantite());
    }

    // recalcul total panier
    $panier = $item->getPanier();
    $this->updatePanierTotal($panier);

    $em->flush();
    return $this->redirectToRoute('app_panier');
}
private function updatePanierTotal(Panier $panier)
{
    $total = 0;
    foreach ($panier->getPanierItems() as $item) {
        $total += $item->getLivre()->getPrixunitaire() * $item->getQuantite();
    }
    $panier->setTotal($total);
}

}
