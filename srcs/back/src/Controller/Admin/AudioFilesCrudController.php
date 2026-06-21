<?php

namespace App\Controller\Admin;

use App\Entity\AudioFiles;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class AudioFilesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AudioFiles::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // On affiche l'ID mais on le cache dans les formulaires d'ajout/édition
            IdField::new('id')->hideOnForm(), 
            
            // Le champ 'name' (qui remplace 'title' du code commenté)
            TextField::new('name', 'Nom du fichier'),
            
            // TextField est suffisant pour une chaîne de 255 caractères
            TextField::new('description', 'Description'),
            
            // TextareaField est souvent mieux pour les champs de type TEXT (Doctrine)
            TextareaField::new('storage_key', 'Clé de stockage'),
            
            // IntegerField pour correspondre au type int de ta durée
            IntegerField::new('duration_ms', 'Durée (ms)'),
        ];
    }
}