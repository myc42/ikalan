<?php

namespace App\Controller\Admin;

use App\Entity\UserStreaks;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class UserStreaksCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserStreaks::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(), // Masqué lors de la création/édition
            
            // ✅ CORRECTION : On utilise bien 'user' pour correspondre à ton entité
            AssociationField::new('user', 'Utilisateur'), 
            
            // Un champ entier pour le score de la plus longue série
            IntegerField::new('longestStreak', 'Plus longue série (jours)'),
            
            // Un champ date et heure pour stocker la dernière activité
            // J'ajoute hideOnForm() si cette date est gérée automatiquement par ton code
            DateTimeField::new('lastActivityAt', 'Dernière activité')->hideOnForm(),
        ];
    }
}