<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController{
    private CategoryService $categoryManager;

    public function __construct(CategoryService $categoryManager){
        $this->categoryManager = $categoryManager;
    }

    #[Route('/utilisateur/categorie/formulaire', name: 'app_ajouter_categorie')]
    #[Route('/utilisateur/categorie/formulaire/{id}', name: 'app_modifier_categorie')]
    public function ajouterModifierCategorie(Category $categorie = null): Response {
        if($categorie == null){
            $categorie = new Category();
        }
        $form = $this->categoryManager->createEditFormulaire($categorie);

        if($form->isSubmitted() && $form->isValid()){
            $id = $this->categoryManager->createEdit($categorie);
            if($id != -1){
                return $this->redirectToRoute('app_nouvelle_categorie', ['id' => $id]);
            }
        }

        return $this->renderForm('categorie/formulaire.html.twig',[
            'form' => $form,
        ]);
    }

    #[Route('/utilisateur/categorie/supprimer/{id}', name: 'app_supprimer_categorie', methods: 'DELETE')]
    public function supprimerCategorie(Category $categorie): RedirectResponse
    {
        $this->categoryManager->supprimer($categorie);
        return $this->redirectToRoute('app_consulter_categorie');
    }

    #[Route('/utilisateur/categorie/nouveau/{id}', name: 'app_nouvelle_categorie', methods: 'GET')]
    public function afficherUneCategorie(Category $category): Response {
        return $this->render('categorie/ajouter.html.twig', [
            'categorie' => $category,
        ]);
    }

    #[Route('/categorie/consulter', name: 'app_consulter_categorie', methods: 'GET')]
    public function consulterListeCategorie(CategoryRepository $repository): Response
    {
        $categories = $repository->findBy([], ['name' => 'ASC']);
        $form = $this->categoryManager->supprimerFormulaire();

        return $this->renderForm('categorie/consulter.html.twig', [
            'categories' => $categories,
            'form' => $form,
        ]);
    }
}