<?php

namespace App\Form;

use App\Entity\Gender;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'firstname',
                )
            ))
            ->add('lastname', TextType::class, array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'lastname',
                )
            ))
            ->add('email', EmailType::class, array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'email',
                )
            ))
            ->add('gender', ChoiceType::class, array(
                'choices' => array(
                    'man' => '1',
                    'woman' => '2'
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('username', TextType::class, array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'username',
                )
            ))
            ->add('password', PasswordType::class, array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'password',
                )

            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}