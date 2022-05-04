<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Service\RestaurantService;
use App\Service\RandomGeneratorService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RestaurantController extends AbstractController{
    private RandomGeneratorService $randomGenerator;
    private RestaurantService $restaurantManager;

    public function __construct(RandomGeneratorService $randomGenerator, RestaurantService $restaurantManager){
        $this->randomGenerator = $randomGenerator;
        $this->restaurantManager = $restaurantManager;
    }

    /**
     * @throws Exception
     */
    #[Route('/', name: 'app_accueil',  methods: 'GET')]
    public function accueil(): Response {
        $value = $this->randomGenerator->randomGenerator();
        if($value == -1){
            return $this->render('restaurant\erreur_bdd_vide.html.twig');
        }
        else{
            return $this->render('restaurant/chargement.html.twig', [
                'id' => $value,
            ]);
        }
    }

    #[Route('/utilisateur/restaurant/formulaire/{id}', name: 'app_modifier_restaurant')]
    #[Route('/utilisateur/restaurant/formulaire', name: 'app_ajouter_restaurant')]
    public function ajouterRestaurant(Restaurant $restaurant = null): Response {
        if($restaurant == null){
            $restaurant = new Restaurant();
            $restaurant->setPicture('');
            $restaurant->setUser(null);
        }

        $form = $this->restaurantManager->createEditFormulaire($restaurant);

        if($form->isSubmitted() && $form->isValid()){
            if($restaurant->getUser() != null){
                $id = $this->restaurantManager->createEdit($restaurant, $form);
            }
            else{
                $id = $this->restaurantManager->createEdit($restaurant, $form, $this->getUser()->getUserIdentifier());
            }
            return $this->redirectToRoute('app_nouveau_restaurant', [
                'id' => $id,
            ]);
        }

        return $this->renderForm('restaurant/formulaire.html.twig',[
            'form' => $form,
        ]);
    }

    #[Route('/utilisateur/restaurant/supprimer/{id}', name: 'app_supprimer_restaurant', methods: 'DELETE')]
    public function supprimerRestaurant(Restaurant $restaurant): Response
    {
        $this->restaurantManager->supprimer($restaurant);
        return $this->redirectToRoute('app_consulter_restaurant');
    }

    #[Route('/utilisateur/restaurant/nouveau/{id}', name: 'app_nouveau_restaurant', defaults: ["randomMode" => false, "ajoutMode" => true], methods: 'GET')]
    #[Route('/restaurant/hasard/{id}', name: 'app_hasard', defaults: ["randomMode" => true, "ajoutMode" => false] ,methods: 'GET')]
    #[Route('/restaurant/consulter/{id}', name: 'app_consulter_un_restaurant', defaults: ["randomMode" => false, "ajoutMode" => false] ,methods: 'GET')]
    public function afficherUnRestaurant(Restaurant $restaurant, bool $randomMode, bool $ajoutMode): Response {
        $form = $this->restaurantManager->supprimerFormulaire();

        return $this->renderForm('restaurant/consulter_un.html.twig', [
            'form' => $form,
            'restaurant' => $restaurant,
            'randomMode' => $randomMode,
            'ajoutMode' => $ajoutMode,
            'titre' => $this->restaurantManager->paramAffichage($randomMode, $ajoutMode),
        ]);
    }

    #[Route('/restaurant/consulter', name: 'app_consulter_restaurant', methods: 'GET')]
    public function consulterListeRestaurant(): Response{
        return $this->render('restaurant/consulter.html.twig', [
            'restaurants' => $this->restaurantManager->getAllASC(),
        ]);
    }
}