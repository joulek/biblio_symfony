<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use App\Repository\AuteurRepository;
use App\Repository\CategorieRepository;
use App\Repository\EditeurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(
        Request $request,
        LivreRepository $livreRepository,
        AuteurRepository $auteurRepository,
        CategorieRepository $categorieRepository,
        EditeurRepository $editeurRepository,
        PaginatorInterface $paginator
    ) {
        $query = $request->query->get('q');
        $type = $request->query->get('type', 'all');
        $sort = $request->query->get('sort', 'relevance');
        $page = $request->query->getInt('page', 1);
        $limit = 12;

        $results = [
            'livres' => null,
            'auteurs' => null,
            'categories' => null,
            'editeurs' => null,
        ];

        /* ========================================
           LIVRES
        ======================================== */
        if ($type === 'all' || $type === 'livres') {

            $livresQuery = $livreRepository->createQueryBuilder('l')
                ->where('l.titre LIKE :q')
                ->orWhere('l.description LIKE :q')
                ->setParameter('q', '%' . $query . '%');

            // TRI compatible PostgreSQL
            switch ($sort) {
                case 'title_asc':
                    $livresQuery->orderBy('l.titre', 'ASC');
                    break;
                case 'title_desc':
                    $livresQuery->orderBy('l.titre', 'DESC');
                    break;
                case 'date_asc':
                    $livresQuery->orderBy('l.datePublication', 'ASC');
                    break;
                case 'date_desc':
                    $livresQuery->orderBy('l.datePublication', 'DESC');
                    break;
                default:
                    // Tri par pertinence simplifié compatible PostgreSQL
                    $livresQuery->orderBy('l.titre', 'ASC');
                    break;
            }

            $results['livres'] = $paginator->paginate(
                $livresQuery->getQuery(),
                $page,
                $limit,
                ['pageParameterName' => 'livres_page']
            );
        }

        /* ========================================
           AUTEURS
        ======================================== */
        if ($type === 'all' || $type === 'auteurs') {
            $auteursQuery = $auteurRepository->createQueryBuilder('a')
                ->where('a.nom LIKE :q')
                ->orWhere('a.prenom LIKE :q')
                ->setParameter('q', '%' . $query . '%')
                ->orderBy('a.nom', 'ASC');

            $results['auteurs'] = $paginator->paginate(
                $auteursQuery->getQuery(),
                $page,
                $limit,
                ['pageParameterName' => 'auteurs_page']
            );
        }

        /* ========================================
           CATÉGORIES
        ======================================== */
        /* ---------------- CATÉGORIES ---------------- */
if ($type === 'all' || $type === 'categories') {

    $categoriesQuery = $categorieRepository->createQueryBuilder('c')
        ->where('c.designation LIKE :q')
        ->setParameter('q', '%' . $query . '%')
        ->orderBy('c.designation', 'ASC');

    $results['categories'] = $paginator->paginate(
        $categoriesQuery->getQuery(),
        $page,
        $limit,
        ['pageParameterName' => 'categories_page']
    );
}

        /* ========================================
           ÉDITEURS
        ======================================== */
        if ($type === 'all' || $type === 'editeurs') {
            $editeursQuery = $editeurRepository->createQueryBuilder('e')
                ->where('e.nom LIKE :q')
                ->setParameter('q', '%' . $query . '%')
                ->orderBy('e.nom', 'ASC');

            $results['editeurs'] = $paginator->paginate(
                $editeursQuery->getQuery(),
                $page,
                $limit,
                ['pageParameterName' => 'editeurs_page']
            );
        }

        return $this->render('search/results.html.twig', [
            'query' => $query,
            'type' => $type,
            'sort' => $sort,
            'results' => $results,
        ]);
    }
}
