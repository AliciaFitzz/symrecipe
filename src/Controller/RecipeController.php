<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    // LISTE DES RECETTES
    #[Route('/recipe', name: 'app_recipe')]
    public function index(PaginatorInterface $paginator, RecipeRepository $recipeRepository, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $recipeRepository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    // FORMULAIRE DE CREATION D'UNE RECETTE
    #[Route('/recipe/new', name: 'new_recipe')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $recipe->setUser($this->getUser());
            $entityManager->persist($recipe);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été créé avec succès.'
            );

            return $this->redirectToRoute('app_recipe');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // FORMULAIRE DE MODIFICATION D'UNE RECETTE
    #[Route('/recipe/edit/{id}', name: 'edit_recipe')]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();

            $entityManager->persist($recipe); // comme un commit
            $entityManager->flush(); // comme un push

            // message affiché à l'utilisateur pour lui signaler que son form a été soumis
            $this->addFlash(
                'success',
                'Votre recette a été modifié avec succès.'
            );

            return $this->redirectToRoute('app_recipe');
        }

        $form->createView();

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form
        ]);
    }

    // SUPPRESSION D'UNE RECETTE
    #[Route('/recipe/delete/{id}', name: 'delete_recipe')]
    public function delete(EntityManagerInterface $entityManager, Recipe $recipe): Response
    {
        $entityManager->remove($recipe);
        $entityManager->flush();

        // message affiché à l'utilisateur pour lui signaler que son form a été supprimé
        $this->addFlash(
            'success',
            'Votre recette a été supprimé avec succès.'
        );

        return $this->redirectToRoute('app_recipe');
    }
}
