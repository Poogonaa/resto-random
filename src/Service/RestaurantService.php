<?php

namespace App\Service;

use App\Entity\Restaurant;
use App\Form\RestaurantDeleteType;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RestaurantService{
    private Request $request;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private RestaurantRepository $restaurantRepository;
    private FormFactoryInterface $formFactory;
    private FileService $fileManager;

    public function __construct(RequestStack         $request, EntityManagerInterface $entityManager,
                                UserRepository       $userRepository, RestaurantRepository $restaurantRepository,
                                FormFactoryInterface $formFactory, FileService $fileManager){
        $this->request = $request->getCurrentRequest();
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->restaurantRepository = $restaurantRepository;
        $this->formFactory = $formFactory;
        $this->fileManager = $fileManager;
    }

    public function createEditFormulaire(Restaurant $restaurant): FormInterface{
        $form = $this->formFactory->create(RestaurantType::class, $restaurant);
        $form->handleRequest($this->request);
        return $form;
    }

    public function createEdit(Restaurant $restaurant, FormInterface $form, String $pseudo = null): int{
        if($form['pictureFile']->getData() != null){
            $restaurant->setPicture($this->fileManager->uploadPicture($form['pictureFile']->getData(), $restaurant->getPicture()));
        }
        if($restaurant->getUser() == null){
            $user = $this->userRepository->findOneBy(['pseudo' => $pseudo]);
            $restaurant->setUser($user);
            $user->addRestaurant($restaurant);
            $user->setPoint($user->getPoint()+5);
            $this->entityManager->persist($user);
        }
        $this->entityManager->persist($restaurant);
        $this->entityManager->flush();
        return $restaurant->getId();
    }

    public function supprimerFormulaire(): FormInterface
    {
        $form = $this->formFactory->create(RestaurantDeleteType::class);
        $form->handleRequest($this->request);
        return $form;
    }

    public function supprimer(Restaurant $restaurant){

        $this->fileManager->deletePicture($restaurant->getPicturePath());
        $this->entityManager->remove($restaurant);
        $this->entityManager->flush();
    }

    public function getAllASC(): array {
        return $this->restaurantRepository->findBy([], ['name' => 'ASC']);
    }

    public function paramAffichage(bool $randomMode, bool $ajoutMode): string
    {
        if($randomMode){
            $titre = "Restaurant au hasard !";
        }
        else if($ajoutMode){
            $titre = "Restaurant ajouté !";
        }
        else{
            $titre = "Consulter le restaurant !";
        }

        return $titre;
    }
}