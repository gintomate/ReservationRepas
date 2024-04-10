<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserInfo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('statut', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    'Stagiaire' => 'Stagiaire',
                    'Personnel de l\'Afpar' => 'Personnel',
                    'Cuisinier' => 'Cuisinier',
                    'Externe' => 'Externe',
                ]
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
