<?php

namespace App\Controller\Admin;

use App\Entity\ModuleComplements;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ModuleComplementsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ModuleComplements::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // L'ID, toujours masqué dans les formulaires
            IdField::new('id')->hideOnForm(),
            
            // La relation vers le Module
            AssociationField::new('moduleId', 'Module'),
            
            // La relation vers le Complément
            AssociationField::new('complementId', 'Complément'),
        ];
    }
}