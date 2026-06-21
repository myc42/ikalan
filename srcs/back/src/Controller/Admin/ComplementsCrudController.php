<?php

namespace App\Controller\Admin;

use App\Entity\Complements;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

class ComplementsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Complements::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // L'ID est masqué dans les formulaires de création/édition
            IdField::new('id')->hideOnForm(),
            
            // Le nom du complément
            TextField::new('name', 'Nom du complément'),
            
            // AssociationField gère automatiquement les relations ManyToOne
            // Il créera une liste déroulante (select) avec les ConstraintTags disponibles
            AssociationField::new('constraintTagId', 'Tag de contrainte'),
            
            // ArrayField est parfait pour gérer les colonnes de type tableau (array/json)
            // Il permettra d'ajouter/supprimer des éléments textuels dynamiquement
            ArrayField::new('phoneticList', 'Liste phonétique'),
            
            // Une autre relation ManyToOne pour le fichier audio
            AssociationField::new('audioPathId', 'Fichier Audio'),
            
            // Note: Je n'ai pas inclus la relation OneToMany (moduleComplements) par défaut.
            // Généralement, on gère les enfants depuis le parent ou on la masque sur l'index pour éviter de surcharger l'affichage.
            // Si tu as besoin de l'afficher, tu peux décommenter la ligne suivante :
            // AssociationField::new('moduleComplements', 'Modules associés')->hideOnIndex(),
        ];
    }
}