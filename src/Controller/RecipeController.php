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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class RecipeController extends AbstractController
{
    // LISTE DES RECETTES
    #[Route('/recipe', name: 'app_recipe')]
    #[IsGranted('ROLE_USER')]
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

    #[Route('/recipe/public', name: 'recipe_index_public')]
    public function indexPublic(PaginatorInterface $paginator, RecipeRepository $recipeRepository, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $recipeRepository->findPublicRecipe(null),
            $request->query->getInt('page', 1)
        );

        return $this->render('pages/recipe/index_public.html.twig', [
            'recipes' => $recipes
        ]);
    }

    // PARTAGE D'UNE RECETTE EN PUBLIC
    #[Security("is_granted('ROLE_USER') and recipe.getIsPublic() === true")]
    #[Route('/recipe/{id}', name: 'recipe_show')]
    public function show(Recipe $recipe): Response
    {
        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }

    // FORMULAIRE DE CREATION D'UNE RECETTE
    #[Route('/recipe/new', name: 'new_recipe')]
    #[IsGranted('ROLE_USER')]
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
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
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
