<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Livre;
use App\Entity\Auteur;
use App\Entity\Editeur;
use App\Entity\Categorie;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Commande;



#[IsGranted('ROLE_ADMIN')]
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{

    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        $url = $adminUrlGenerator->setController(LivreCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('Bibliothèque');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Livres', 'fa fa-book', entityFqcn: Livre::class);
        yield MenuItem::linkToCrud('Auteurs', 'fa fa-user', entityFqcn: Auteur::class);
        yield MenuItem::linkToCrud('Editeurs', 'fa fa-building', entityFqcn: Editeur::class);
        yield MenuItem::linkToCrud('Categories', 'fa fa-list', entityFqcn: Categorie::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', entityFqcn: User::class);
        yield MenuItem::linkToCrud('Commandes', 'fa fa-shopping-cart', entityFqcn: Commande::class);




    }
}
