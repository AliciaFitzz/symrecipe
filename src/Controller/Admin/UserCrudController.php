<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    // Renseigne vers quelle entité on se base pour créer le controller
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    // Configuration du crud
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Utilisateurs') // Label au pluriel
            ->setEntityLabelInSingular('Utilisateur') // Label au singulier
            ->setPageTitle('index', 'SymRecipe - Gestion des utilisateurs') // nom de la page
            ->setPaginatorPageSize(10); // nombre d'utilisateurs par page
    }

    // Configurer les champs
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id') // champs
                ->hideOnForm(), // ne s'affiche pas dans le formulaire
            TextField::new('fullName', 'Nom'),
            TextField::new('pseudo', 'Pseudo'),
            TextField::new('email', 'Email')
                ->hideOnForm(),
            ArrayField::new('roles')
                ->hideOnIndex(), // ne s'affiche pas dans la liste
            DateTimeField::new('createdAt')
                ->hideOnForm()
        ];
    }
}
