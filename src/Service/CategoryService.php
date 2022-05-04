<?php

namespace App\Service;

use App\Entity\Category;
use App\Form\CategoryDeleteType;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CategoryService{
    private Request $request;
    private EntityManagerInterface $entityManager;
    private CategoryRepository $categoryRepository;
    private FormFactoryInterface $formFactory;

    public function __construct(RequestStack $request, EntityManagerInterface $entityManager,
                                CategoryRepository $categoryRepository, FormFactoryInterface $formFactory){
        $this->request = $request->getCurrentRequest();
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
        $this->formFactory = $formFactory;
    }

    public function createEditFormulaire(Category $categorie): FormInterface{
        $form = $this->formFactory->create(CategoryType::class,$categorie);
        $form->handleRequest($this->request);
        return $form;
    }

    public function createEdit(Category $categorie): int{
        if(!$this->categoryRepository->findBy(['name' => $categorie->getName()])){
            $this->entityManager->persist($categorie);
            $this->entityManager->flush();
            return $categorie->getId();
        }
        return -1;
    }



    public function supprimerFormulaire(): FormInterface
    {
        $form = $this->formFactory->create(CategoryDeleteType::class);
        $form->handleRequest($this->request);

        return $form;
    }

    public function supprimer(Category $categorie){
        $this->entityManager->remove($categorie);
        $this->entityManager->flush();
    }
}