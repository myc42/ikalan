<?php

namespace App\Controller\Admin;

use App\Entity\UserDevices;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class UserDevicesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserDevices::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(), // On cache l'ID dans le formulaire de création/édition
            
            // AssociationField permet d'afficher un menu déroulant pour choisir l'utilisateur lié
            AssociationField::new('userId', 'Utilisateur'), 
            
            TextField::new('osName', 'Nom de l\'OS (ex: iOS, Android)'),
            TextField::new('osVersion', 'Version de l\'OS'),
            IntegerField::new('appVersion', 'Version de l\'application'),
        ];
    }
}