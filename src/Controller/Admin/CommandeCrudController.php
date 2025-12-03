<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;



class CommandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commande::class;
    }
public function configureFields(string $pageName): iterable
{
    return [
        IdField::new('id')->onlyOnIndex(),

        AssociationField::new('user', 'Utilisateur')
            ->formatValue(function ($value, $entity) {
                return $entity->getUser()->getEmail();
            }),

        DateTimeField::new('date', 'Date Commande'),

        NumberField::new('total', 'Montant Total'),

        TextField::new('modePaiement', 'Mode de Paiement'),

        // ➕ Afficher nombre total d'articles
        NumberField::new('nombreArticles', 'Nbre Articles')
            ->onlyOnIndex(),

        CollectionField::new('commandeItems', 'Articles')
            ->onlyOnDetail()
            ->setTemplatePath('admin/commande_items.html.twig'),
    ];
}



    // 🔒 Désactiver les actions: new, edit, delete
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }

    // 🔒 Supprimer le bouton "Add Commande" dans la barre
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN')
            ->setPageTitle('index', 'Liste des Commandes')
            ->showEntityActionsInlined(); // juste les ...
    }
}
