<?php

namespace App\Controller\Admin;

use App\Repository\LivreRepository;
use App\Repository\UserRepository;
use App\Repository\EmpruntRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard')]
    public function index(
        LivreRepository $livreRepository,
        UserRepository $userRepository,
        EmpruntRepository $empruntRepository
    ): Response {
        // Vérification supplémentaire de sécurité
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupération des statistiques
        $noteMoyenne = $empruntRepository->getNoteMoyenneGlobale();
        $totalNotes = $empruntRepository->count([]);
        
        $stats = [
            'livres' => $livreRepository->count([]),
            'utilisateurs' => $userRepository->count([]),
            'emprunts' => $empruntRepository->count(['statut' => 'en_cours']),
            'noteMoyenne' => $noteMoyenne,
            'totalNotes' => $totalNotes
        ];

        // Derniers emprunts (activités récentes)
        $derniersEmprunts = $empruntRepository->findBy(
            [], // Critères vides pour tous les enregistrements
            ['dateEmprunt' => 'DESC'],
            5
        );

        $activites = [];
        foreach ($derniersEmprunts as $emprunt) {
            $activites[] = [
                'date' => $emprunt->getDateEmprunt(),
                'utilisateur' => $emprunt->getUser()->getEmail(),
                'action' => 'Emprunt',
                'details' => sprintf('Livre: %s', $emprunt->getLivre()->getTitre())
            ];
        }

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'activites' => $activites,
        ]);
    }
}
