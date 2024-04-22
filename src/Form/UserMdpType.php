<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserMdpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userPassword', PasswordType::class, [
                'constraints' => [
                    new UserPassword([
                        'message' => 'Votre ancien mot de passe n\'est pas correct.',
                    ]),
                    new NotBlank(),
                ],'label'=>'Votre ancien mot de passe'
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Nouveau mot de passe'],
                'second_options' => ['label' => 'Confirmez votre nouveau mot de passe'],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_])(?=.*\d).+/',
                        'message' => 'Votre nouveau mot de passe ne respecte pas les normes de sécurité.',
                    ]),
                    new NotBlank(),
                    new Length([
                        'min' => 7,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} lettres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
