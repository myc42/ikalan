<?php

namespace App\Controller\Admin;

use App\Entity\ModuleVerbs;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ModuleVerbsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ModuleVerbs::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // L'ID, toujours masqué dans les formulaires
            IdField::new('id')->hideOnForm(),
            
            // La relation vers le Module
            AssociationField::new('moduleId', 'Module'),
            
            // La relation vers le Verbe
            AssociationField::new('verbId', 'Verbe'),
        ];
    }
}