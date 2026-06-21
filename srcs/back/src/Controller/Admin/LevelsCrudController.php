<?php

namespace App\Controller\Admin;

use App\Entity\Levels;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class LevelsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Levels::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // L'ID masqué dans le formulaire
            IdField::new('id')->hideOnForm(),
            
            // Le nom du niveau (ex: Débutant, Intermédiaire, etc.)
            TextField::new('name', 'Nom du niveau'),
            
            // TextareaField est très bien pour un champ texte standard.
            // (Tu peux utiliser TextEditorField si tu veux pouvoir mettre le texte en gras, italique, etc.)
            TextareaField::new('description', 'Description'),
            
            // La relation OneToMany vers les mots. 
            // On la masque sur le formulaire pour garder une interface propre, 
            // mais ça permet de voir combien de mots sont associés à ce niveau dans le tableau (index).
            AssociationField::new('words', 'Mots associés')->hideOnForm(),
        ];
    }
}