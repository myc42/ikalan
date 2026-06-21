<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
        IdField::new('id')->hideOnForm(),
        TextField::new('telephone', 'Numéro de téléphone'),
        TextField::new('firstName', 'Nom'),
        TextField::new('lastName', 'Prénom'),

        DateField::new('birthdayAt', 'Date de naissance')
            ->setFormat('dd/MM/yyyy'),

        DateTimeField::new('registerAt', 'Date d\'inscription')
            ->setFormat('dd/MM/yyyy HH:mm'),

        DateTimeField::new('updateAt', 'Date de mise à jour')
             ->setFormat('dd/MM/yyyy HH:mm'),

        ChoiceField::new('roles', 'Rôles')
            ->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN',
            ])
            ->allowMultipleChoices(),
            
        TextField::new('password', 'Mot de passe')->hideOnIndex(),
    ];
    }
}