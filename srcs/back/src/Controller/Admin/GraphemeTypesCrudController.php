<?php

namespace App\Controller\Admin;

use App\Entity\GraphemeTypes;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GraphemeTypesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GraphemeTypes::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // L'ID, toujours masqué lors de l'ajout ou la modification
            IdField::new('id')->hideOnForm(),
            
            // Le nom du type de graphème
            TextField::new('name', 'Nom du type'),
        ];
    }
}