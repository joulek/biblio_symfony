<?php

namespace App\Controller;



use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/livre')]
final class LivreController extends AbstractController
{
    #[Route(name: 'app_livre_index', methods: ['GET'])]
    public function index(LivreRepository $livreRepository): Response
    {
        return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findAll(),
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_livre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $livre = new Livre();
    $form = $this->createForm(LivreType::class, $livre);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        // Récupération du fichier uploadé
        $imageFile = $form->get('imageFile')->getData();

        if ($imageFile) {
            // Créer un nom unique
            $newFilename = uniqid().'.'.$imageFile->guessExtension();

            // Déplacer le fichier vers /public/uploads/livres
            $imageFile->move(
                $this->getParameter('livres_directory'),
                $newFilename
            );

            // Assigner le nom dans l’entité
            $livre->setImage($newFilename);
        }

        $entityManager->persist($livre);
        $entityManager->flush();

        return $this->redirectToRoute('app_livre_index');
    }

    return $this->render('livre/new.html.twig', [
        'livre' => $livre,
        'form' => $form,
    ]);
}


    #[Route('/{id}', name: 'app_livre_show', methods: ['GET'])]
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_livre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_livre_delete', methods: ['POST'])]
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
    }
}
