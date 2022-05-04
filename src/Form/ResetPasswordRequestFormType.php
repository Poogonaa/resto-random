<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if(array_key_exists('data', $options)){
            $builder
                ->add('mail', EmailType::class, [
                    'attr' => ['readonly' => true, ],
                ])
            ;
        }
        else{
            $builder
                ->add('mail', EmailType::class, [
                    'attr' => ['autocomplete' => 'email'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrez votre mail s\'il vous plait',
                        ]),
                    ],
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
