<?php

namespace App\Controller\Admin;

use App\Entity\UserModuleProgress;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField; // <-- Remplace TextField par ça

class UserModuleProgressCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserModuleProgress::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),

            // Relations avec les autres entités
            AssociationField::new('userId', 'Utilisateur'),
            AssociationField::new('moduleId', 'Module'),

            // Scores et Facteurs (Décimaux)
            NumberField::new('globalScore', 'Score global')
                ->setNumDecimals(3),
            
            NumberField::new('easeFactor', 'Facteur de facilité (EF)')
                ->setNumDecimals(3),

            // Statistiques et Intervalles (Entiers)
            IntegerField::new('consecutivePerfectScores', 'Scores parfaits consécutifs'),
            IntegerField::new('intervalDays', 'Intervalle (jours)'),

            // Statut textuel
            ChoiceField::new('status', 'Statut'),

            // Dates et Planification
            DateTimeField::new('targetAt', 'Date cible'),
            DateTimeField::new('windowStartAt', 'Début de la fenêtre'),
            DateTimeField::new('windowEndAt', 'Fin de la fenêtre'),
            DateTimeField::new('lastSeenAt', 'Dernière consultation'),
        ];
    }
}