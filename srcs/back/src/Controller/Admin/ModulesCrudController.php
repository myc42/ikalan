<?php

namespace App\Controller\Admin;

use App\Entity\Modules;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;


class ModulesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Modules::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // L'ID masqué sur les formulaires
            IdField::new('id')->hideOnForm(),
            
            // Relation ManyToOne vers le Chapitre
            AssociationField::new('chapterId', 'Chapitre'),
            
            // Le titre du module
            TextField::new('title', 'Titre du module'),
            
            // L'ordre d'affichage
            IntegerField::new('module_order', 'Ordre du module'),
            
            // Comme la description est de type 'text[]', ArrayField est nécessaire ici
            ArrayField::new('description', 'Description (paragraphes)'),
            
            // La version du contenu
            IntegerField::new('content_version', 'Version du contenu'),
            
            // --- RELATIONS OneToMany ---
            // On les masque généralement sur le formulaire de création/édition
            // car il est plus logique d'ajouter ces éléments depuis leurs propres menus
            AssociationField::new('moduleSubjects', 'Sujets associés')->hideOnForm(),
            AssociationField::new('moduleVerbs', 'Verbes associés')->hideOnForm(),
            AssociationField::new('moduleComplements', 'Compléments associés')->hideOnForm(),
            IntegerField::new('wordLevel', 'Word level'),

        ];
    }
}