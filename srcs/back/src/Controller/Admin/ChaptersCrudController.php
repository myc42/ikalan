<?php

namespace App\Controller\Admin;

use App\Entity\Chapters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class ChaptersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Chapters::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // On cache l'ID dans le formulaire comme pour l'entité précédente
            IdField::new('id')->hideOnForm(),
            
            // Le titre du chapitre
            TextField::new('title', 'Titre'),
            
            // IntegerField pour gérer l'ordre d'affichage (qui est un int)
            IntegerField::new('chapter_order', 'Ordre du chapitre'),
            
            // Ici, j'ai gardé le TextEditorField (WYSIWYG) car pour un chapitre, 
            // tu auras sûrement besoin de mettre en forme ton texte (gras, listes, etc.).
            // Si tu veux juste du texte brut, tu peux le remplacer par TextareaField.
            TextEditorField::new('description', 'Description'),
        ];
    }
}