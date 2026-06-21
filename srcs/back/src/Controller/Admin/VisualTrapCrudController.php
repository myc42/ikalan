<?php

namespace App\Controller\Admin;

use App\Entity\VisualTrap;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

class VisualTrapCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return VisualTrap::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // L'ID, masqué dans les formulaires comme d'habitude
            IdField::new('id')->hideOnForm(),
            
            // Si le nom est relativement court (1 à 3 mots), TextField suffit.
            // S'il s'agit d'une phrase longue (vu que c'est un type TEXT en base), 
            // tu peux le remplacer par TextareaField::new('name', 'Nom du piège visuel')
            TextField::new('name', 'Nom du piège visuel'),
            
            // Le tableau textuel pour stocker ta liste d'éléments
            ArrayField::new('list', 'Liste d\'éléments'),
        ];
    }
}