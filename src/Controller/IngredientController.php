<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    // AFFICHE LA LISTE DES INGREDIENTS
    #[Route('/ingredient', name: 'app_ingredient')]
    public function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, Request $request): Response
    {
        /* je stock les données dans ma variable. 
        j'uilise le bundle paginator, à l'intérieur je met mon repo
        findBy va récupérer la liste des ingrédients en fonction de l'utilisateur courant 
        je lui donne la première et la limite de page*/
        $ingredients = $paginator->paginate(
            $ingredientRepository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    // FORMULAIRE DE CREATION D'INGREDIENT
    #[Route('/ingredient/new', name: 'new_ingredient')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $ingredient->setUser($this->getUser());

            $entityManager->persist($ingredient); // comme un commit
            $entityManager->flush(); // comme un push

            // message affiché à l'utilisateur pour lui signaler que son form a été soumis
            $this->addFlash(
                'success',
                'Votre ingrédient a été créer avec succès.'
            );

            return $this->redirectToRoute('app_ingredient');
        }

        $form->createView();

        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form
        ]);
    }

    // FORMULAIRE DE MODIFICATION D'UN INGREDIENT
    #[Route('/ingredient/edit/{id}', name: 'edit_ingredient')]
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $entityManager->persist($ingredient); // comme un commit
            $entityManager->flush(); // comme un push

            // message affiché à l'utilisateur pour lui signaler que son form a été soumis
            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès.'
            );

            return $this->redirectToRoute('app_ingredient');
        }

        $form->createView();

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form
        ]);
    }

    // SUPPRESSION D'UN INGREDIENT
    #[Route('/ingredient/delete/{id}', name: 'delete_ingredient')]
    public function delete(EntityManagerInterface $entityManager, Ingredient $ingredient): Response
    {
        $entityManager->remove($ingredient);
        $entityManager->flush();

        // message affiché à l'utilisateur pour lui signaler que son form a été supprimé
        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec succès.'
        );

        return $this->redirectToRoute('app_ingredient');
    }
}
