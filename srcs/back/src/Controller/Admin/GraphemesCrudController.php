<?php

namespace App\Controller\Admin;

use App\Entity\Graphemes;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

class GraphemesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Graphemes::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // On cache toujours l'ID dans les formulaires
            IdField::new('id')->hideOnForm(),
            
            // Relation ManyToOne vers l'entité Modules
            AssociationField::new('moduleId', 'Module'),
            
            // Les champs textes courts
            TextField::new('min', 'Minuscule'),
            TextField::new('maj', 'Majuscule'),
            TextField::new('word', 'Mot repère'),
            
            // BooleanField génère un interrupteur (toggle) oui/non très pratique
            BooleanField::new('isSilentLetter', 'Est une lettre muette ?'),
            
            // Relation ManyToOne vers AudioFiles
            AssociationField::new('audioPath', 'Fichier Audio'),
            
            // Relation OneToOne vers VisualTrap. 
            // On le masque souvent sur la page d'index pour ne pas surcharger le tableau
            AssociationField::new('visualTrap', 'Piège visuel')->hideOnIndex(),
            
            // ArrayField pour le tableau de textes
            ArrayField::new('phoneticList', 'Liste phonétique'),
            
            // Relation ManyToOne vers GraphemeTypes
            AssociationField::new('typeId', 'Type de graphème'),
        ];
    }
}