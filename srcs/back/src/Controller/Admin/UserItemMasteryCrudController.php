<?php

namespace App\Controller\Admin;

use App\Entity\UserItemMastery;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserItemMasteryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserItemMastery::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),

            AssociationField::new('userId', 'Utilisateur'),

            TextField::new('itemType', 'Type d\'item'),

            TextField::new('itemId', 'ID de l\'item'),

            NumberField::new('masteryScore', 'Score de maîtrise')
                ->setNumDecimals(3),

            IntegerField::new('avgResponseMs', 'Temps de réponse moyen (ms)'),

            IntegerField::new('errorCount', 'Nombre d\'erreurs'),

            DateTimeField::new('lastSeenAt', 'Dernière consultation'),
        ];
    }
}