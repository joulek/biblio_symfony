<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class NoteController extends AbstractController
{
    #[Route('/livre/{id}/noter', name: 'app_livre_noter', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function noter(
        Livre $livre,
        Request $request,
        EntityManagerInterface $entityManager,
        NoteRepository $noteRepository
    ): Response {
        $user = $this->getUser();
        
        // Vérifier si l'utilisateur a déjà noté ce livre
        $noteExistante = $noteRepository->findOneBy([
            'user' => $user,
            'livre' => $livre
        ]);

        $note = $noteExistante ?? new Note();
        $note->setUser($user);
        $note->setLivre($livre);
        
        $valeur = (int) $request->request->get('note');
        $note->setValeur($valeur);
        
        $entityManager->persist($note);
        $entityManager->flush();
        
        $this->addFlash('success', 'Votre note a été enregistrée avec succès.');
        
        return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
    }
}
