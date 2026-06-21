<?php

namespace App\Controller\Admin;

use App\Entity\Verbs;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

class VerbsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Verbs::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // L'ID, toujours masqué lors de l'ajout ou la modification
            IdField::new('id')->hideOnForm(),
            
            // Le nom du verbe (ex: "manger", "est", "a", etc.)
            TextField::new('name', 'Nom du verbe'),
            
            // Les relations ManyToOne (EasyAdmin va générer des menus déroulants)
            AssociationField::new('constraintTagId', 'Tag de contrainte'),
            AssociationField::new('personId', 'Personne / Nombre'),
            
            // Le tableau textuel pour la phonétique
            ArrayField::new('phoneticList', 'Liste phonétique'),
            
            // Relation vers le fichier audio
            AssociationField::new('audioPathId', 'Fichier Audio'),
            
            // La relation OneToMany vers ModuleVerbs
            // Masquée sur le formulaire pour garder l'interface propre
            AssociationField::new('moduleVerbs', 'Modules associés')->hideOnForm(),
        ];
    }
}