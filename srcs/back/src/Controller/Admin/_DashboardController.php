<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class _DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        return $this->redirectToRoute('admin_user_index');

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
                

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Symfony');
    }

    public function configureMenuItems(): iterable
    {
            yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

            yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fas fa-users');
            yield MenuItem::linkTo(UserDevicesCrudController::class, 'Appareils', 'fas fa-mobile-alt');
yield MenuItem::linkTo(TrophyCrudController::class, 'Trophées', 'fas fa-trophy');
yield MenuItem::linkTo(UserStreaksCrudController::class, 'Séries (Streaks)', 'fas fa-fire');


            yield MenuItem::linkTo(ChaptersCrudController::class, 'Chapitres', 'fas fa-book-open');
            yield MenuItem::linkTo(ModulesCrudController::class, 'Modules', 'fas fa-cubes');

            yield MenuItem::linkTo(VisualTrapCrudController::class, 'Pièges visuels', 'fas fa-eye');
            yield MenuItem::linkTo(GraphemeTypesCrudController::class, 'Types de graphèmes', 'fas fa-spell-check');
            yield MenuItem::linkTo(GraphemesCrudController::class, 'Graphèmes', 'fas fa-font');
            yield MenuItem::linkTo(LevelsCrudController::class, 'Niveaux', 'fas fa-layer-group');

            yield MenuItem::linkTo(WordsCrudController::class, 'Mots', 'fas fa-book');
            yield MenuItem::linkTo(ComplementsCrudController::class, 'Compléments', 'fas fa-puzzle-piece');
            yield MenuItem::linkTo(VerbsCrudController::class, 'Verbes', 'fas fa-bolt');
            yield MenuItem::linkTo(SubjectsCrudController::class, 'Sujets', 'fas fa-user');
            yield MenuItem::linkTo(PersonNumbersCrudController::class, 'Personnes & Nombres', 'fas fa-users');

            yield MenuItem::linkTo(ConstraintTagsCrudController::class, 'Tags de contraintes', 'fas fa-tags');

            yield MenuItem::linkTo(ModuleComplementsCrudController::class, 'Compléments de modules', 'fas fa-puzzle-piece');
            yield MenuItem::linkTo(ModuleSubjectsCrudController::class, 'Sujets de modules', 'fas fa-user-tag');
            yield MenuItem::linkTo(ModuleVerbsCrudController::class, 'Verbes de modules', 'fas fa-cogs');

            yield MenuItem::linkTo(AudioFilesCrudController::class, 'Audios', 'fas fa-volume-up');

                    
    }
}
