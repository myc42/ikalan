<?php

namespace App\Controller\Admin;

use App\Entity\ModuleSubjects;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ModuleSubjectsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ModuleSubjects::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // L'ID, masqué dans les formulaires d'ajout et d'édition
            IdField::new('id')->hideOnForm(),
            
            // La relation vers le Module
            AssociationField::new('moduleId', 'Module'),
            
            // La relation vers le Sujet
            AssociationField::new('subjectId', 'Sujet'),
        ];
    }
}