<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


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

    // PARTAGER UNE RECETTE EN PUBLIC
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

    // NOTER UNE RECETTE
    #[Security("is_granted('ROLE_USER') and (recipe.getIsPublic() === true || user === recipe.getUser())")]
    #[Route('/recipe/{id}', name: 'recipe_show')]
    public function show(Recipe $recipe, Request $request, MarkRepository $markRepository, EntityManagerInterface $manager): Response
    {

        dump($recipe);

        $mark = new Mark();

        $form = $this->createForm(MarkType::class, $mark);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mark->setUser($this->getUser())
                ->setRecipe($recipe);

            // vérification que l'utilisateur ne note pas 2 fois la même recette
            $existingMark = $markRepository->findOneBy(['user' => $this->getUser(), 'recipe' => $recipe]);

            if (!$existingMark) {
                $manager->persist($mark);
            } else {
                $existingMark->setMark($form->getData()->getMark());
            }

            $manager->flush();

            $this->addFlash(
                'success',
                'Votre note a bien été enregistrée.'
            );

            return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
        }

        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
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
