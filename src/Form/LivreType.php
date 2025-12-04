<?php

namespace App\Form;

use App\Entity\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Editeur;
use App\Entity\Categorie;
use App\Entity\Auteur;


class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description', null, [
                'label' => 'Description',
                'attr' => ['rows' => 5],
                'required' => false
            ])
            ->add('qte')
            ->add('prixunitaire')
            ->add('datepub')
            ->add('isbn')
            ->add('imageFile', FileType::class, [
                'label' => 'Image du livre',
                'mapped' => false,
                'required' => false,
            ])
             // 🔽 CATEGORIE (ManyToOne)
        ->add('categorie', EntityType::class, [
            'class' => Categorie::class,
            'choice_label' => 'designation',
            'placeholder' => '— Sélectionner une catégorie —',
            'required' => false,
        ])

        // 🔽 EDITEUR (ManyToOne)
        ->add('editeur', EntityType::class, [
            'class' => Editeur::class,
            'choice_label' => 'nom',
            'placeholder' => '— Sélectionner un éditeur —',
            'required' => false,
        ])



     ->add('auteurs', EntityType::class, [
    'class' => Auteur::class,
    'choice_label' => function(Auteur $auteur) {
        return $auteur->getNom() . ' ' . $auteur->getPrenom();
    },
    'multiple' => true,
    'expanded' => false,
    'required' => true,
    'attr' => [
        'class' => 'auteurs-select',
    ],
]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
