<?php

namespace App\Controller\Admin;

use App\Entity\Words;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class WordsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Words::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // L'ID, toujours masqué dans les formulaires
            IdField::new('id')->hideOnForm(),
            
            // Le mot en lui-même
            TextField::new('word', 'Mot'),
            
            // Les deux champs booléens qui afficheront des interrupteurs (toggle)
            BooleanField::new('isSyllable', 'Est une syllabe ?'),
            BooleanField::new('isSightWord', 'Est un mot global ?'),
            
            // Les deux tableaux (array) pour la segmentation et la phonétique
            ArrayField::new('segmentation', 'Segmentation'),
            ArrayField::new('phoneticList', 'Liste phonétique'),
            
            // Les relations (les entités cibles ont déjà leur __toString() !)
            AssociationField::new('levelId', 'Niveau'),
            AssociationField::new('audioPath', 'Fichier Audio'),
        ];
    }
}