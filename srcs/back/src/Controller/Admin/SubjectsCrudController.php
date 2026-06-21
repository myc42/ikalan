<?php

namespace App\Controller\Admin;

use App\Entity\Subjects;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

class SubjectsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Subjects::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // On masque l'ID dans les formulaires
            IdField::new('id')->hideOnForm(),
            
            // Le nom du sujet (ex: "Je", "Tu", "Le chat", etc.)
            TextField::new('name', 'Nom du sujet'),
            
            // La relation ManyToOne vers la table PersonNumbers
            // Va générer un select avec "1ère singulier", etc. (grâce au __toString qu'on a fait avant !)
            AssociationField::new('personId', 'Personne / Nombre'),
            
            // Le tableau textuel pour la phonétique
            ArrayField::new('phoneticList', 'Liste phonétique'),
            
            // La relation ManyToOne vers le fichier audio
            AssociationField::new('audioPathId', 'Fichier Audio'),
            
            // La relation OneToMany vers ModuleSubjects
            // Généralement masquée sur le formulaire pour ne pas le surcharger
            AssociationField::new('moduleSubjects', 'Modules associés')->hideOnForm(),
        ];
    }
}