<?php

namespace App\Controller\Admin;

use App\Entity\ConstraintTags;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConstraintTagsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ConstraintTags::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // L'ID caché dans les formulaires
            IdField::new('id')->hideOnForm(),
            
            // Le seul champ modifiable de cette entité : le nom
            TextField::new('name', 'Nom du tag'),
        ];
    }
}