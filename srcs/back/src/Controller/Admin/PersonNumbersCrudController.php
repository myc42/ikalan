<?php

namespace App\Controller\Admin;

use App\Entity\PersonNumbers;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PersonNumbersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PersonNumbers::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // L'ID, toujours caché quand on crée ou modifie
            IdField::new('id')->hideOnForm(),
            
            // Le seul champ utile ici : le nom
            TextField::new('name', 'Personne / Nombre (ex: 1ère singulier)'),
        ];
    }
}