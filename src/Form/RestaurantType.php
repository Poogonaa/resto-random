<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Restaurant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(!$options['data']->getId()){
            $restaurant = null;
        }
        else{
            $restaurant = $options['data'];
        }

        $builder
            ->add('name', TextType::class, ['required' => false,])
            ->add('city', TextType::class, ['required' => false,])
            ->add('postal_code', IntegerType::class, ['required' => false,])
            ->add('number', IntegerType::class, ['required' => false,])
            ->add('street', TextType::class, ['required' => false,])
            ->add('complement', TextType::class, ['required' => false,])
            ->add('Category', EntityType::class, [
                'label' => 'Catégories :',
                'class' => Category::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name',
                'required' => true,
            ]);

        $imageConstraints = [
            new Image([
                'maxSize' => '10M',
                'mimeTypesMessage' => 'Choisissez une image valide',
            ]),
        ];

        if($restaurant == null){
            $imageConstraints[] = new NotNull([
                'message' => 'Vous devez ajouter une photo.',
            ]);
        }

        $builder
            ->add('pictureFile', FileType::class, ['required' => false,
                'mapped' => false,
                'constraints' => $imageConstraints,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}