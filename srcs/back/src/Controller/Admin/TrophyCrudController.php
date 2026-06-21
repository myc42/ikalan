<?php

namespace App\Controller\Admin;

use App\Entity\Trophy;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class TrophyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Trophy::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(), // Masqué lors de la création/édition
            
            // Liaison avec l'utilisateur possédant ces trophées
            AssociationField::new('userId', 'Utilisateur'), 
            
            // Champs entiers pour les différents compteurs de succès
            IntegerField::new('perfectChapter', 'Chapitres parfaits'),
            IntegerField::new('moduleMaster', 'Modules maîtrisés'),
            IntegerField::new('flawlessStreak', 'Séries sans faute'),
            
            // Champ date et heure pour la dernière mise à jour des trophées
            DateTimeField::new('updateAt', 'Mis à jour le'),
        ];
    }
}