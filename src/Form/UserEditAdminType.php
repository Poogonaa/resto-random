<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (in_array('ROLE_ADMIN', $options['data']->getRoles(), true)) {
            $role_data = true;
        } else {
            $role_data = false;
        }
        $builder
            ->add('pseudo', TextType::class, [
                'required' => false,
                'label' => 'Pseudo :',
                'label_attr'=> [
                    'class'=>'form-label',
                    'style'=>'margin: 0px;width: 100%;text-align: left;color: #002d47;'
                ],
                ])
            ->add('mail', EmailType::class, [
                'required' => false,
                'label' => 'Mail :',
                'label_attr'=> [
                    'class'=>'form-label',
                    'style'=>'margin: 0px;width: 100%;text-align: left;color: #002d47;'
                ],
                ])
            ->add('admin', ChoiceType::class, [
                'placeholder' => false,
                'required' => false,
                'mapped' => false,
                'label' => 'Administrateur ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'data' => $role_data,
                'label_attr'=> [
                    'class'=>'form-label',
                    'style'=>'margin: 0px;width: 100%;text-align: left;color: #002d47;'
                ]
                ])
            ->add('point', NumberType::class, [
                'required' => false,
                'label' => 'Points :',
                'label_attr'=> [
                    'class'=>'form-label',
                    'style'=>'margin: 0px;width: 100%;text-align: left;color: #002d47;'
                ],
            ])
            ->add('active', ChoiceType::class, ['required' => false,
                'placeholder' => false,
                'label' => 'Activer :',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'label_attr'=> [
                    'class'=>'form-label',
                    'style'=>'margin: 0px;width: 100%;text-align: left;color: #002d47;'
                ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
