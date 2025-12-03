<?php

namespace App\Controller\Admin;

use App\Entity\Livre;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;

class LivreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Livre::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            // ----- Champs simples -----
            TextField::new('titre', 'Titre'),
            IntegerField::new('qte', 'Quantité'),
            MoneyField::new('prixunitaire', 'Prix unitaire')->setCurrency('TND'),
            DateField::new('datepub', 'Date de publication'),
            IntegerField::new('isbn', 'ISBN'),

            // ----- Upload image -----
            ImageField::new('image', 'Image du livre')
                ->setBasePath('uploads/livres')              // URL publique
                ->setUploadDir('public/uploads/livres')      // Dossier réel
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),

            // ----- Relations -----
            AssociationField::new('categorie', 'Catégorie')
                ->setRequired(true),

            AssociationField::new('editeur', 'Éditeur')
                ->setRequired(true),

            // ManyToMany → auteurs
            AssociationField::new('auteurs', 'Auteurs')
                ->setFormTypeOptions([
                    'by_reference' => false,  // indispensable ManyToMany
                    'multiple' => true,
                ]),
        ];
    }
}
