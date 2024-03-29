<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifiant', TextType::class)
            ->add('password', PasswordType::class)
            ->add('email', EmailType::class)
            ->add('statut', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                'Stagiaire' => 'Stagiaire',
                'Personnel de l\'Afpar' => 'Personnel',
                'Cuisinier' => 'Cuisinier',
                'Externe' => 'Externe',]
            ])
            ->add('delegue', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('userInfo', UserInfoType::class, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
