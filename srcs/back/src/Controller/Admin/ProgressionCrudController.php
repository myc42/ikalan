<?php

namespace App\Controller\Admin;

use App\Entity\Progression;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class ProgressionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Progression::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),

            AssociationField::new('userId', 'Utilisateur'),

            AssociationField::new('moduleId', 'Module'),

            DateTimeField::new('startAt', 'Date de début'),

            DateTimeField::new('completedAt', 'Date de fin'),
        ];
    }
}