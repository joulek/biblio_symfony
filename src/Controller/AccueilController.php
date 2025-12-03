<?php

namespace App\Controller;

use App\Repository\AuteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\LivreRepository;

final class AccueilController extends AbstractController
{
   #[Route('/', name: 'app_accueil')]
    public function index(LivreRepository $livreRepository, AuteurRepository $auteurRepository): Response
    {
        $livres = $livreRepository->findBy([], ['id' => 'DESC'], 5);
        $auteur = $auteurRepository->findBy([], ['id' => 'DESC'], 5);
        // Les 6 derniers livres

        return $this->render('accueil/index.html.twig', [
            'livres' => $livreRepository->findAll(),
            'auteurs' => $auteurRepository->findAll(),// si tu veux tous les livres
        ]);

}
}
