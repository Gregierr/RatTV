<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login', TextType::class, [
                'label' => 'login',
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                    new Length([
                        'min' => '6',
                        'max' => '32',
                        'minMessage' => 'Login must have at least "{{ limit }}" letters.',
                        'maxMessage' => 'Login must have at most "{{ limit }}" letters.',
                    ]),
                ],
            ])
            ->add('password', TextType::class, [
                'label' => 'password',
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                    new Length([
                        'min' => '6',
                        'max' => '32',
                        'minMessage' => 'Password must have at least "{{ limit }}" letters.',
                        'maxMessage' => 'Password must have at most "{{ limit }}" letters.',
                    ]),
                ],
            ])
            ->add('Sign_In', SubmitType::class)
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}