<?php

namespace App\Form;

use App\Entity\Promo;
use App\Entity\UserInfo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class UserInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('dateDeNaissance', DateType::class, [
                'placeholder' =>  [
                    'year' => 'Choisissez une année', 'month' => 'Choisissez un mois', 'day' => 'Choisissez un jour',
                ],
                'widget' => 'choice',
                'by_reference' => true,
                'format' => 'yyyy-MM-dd',
                'years' =>  range(1907, date('Y')),
            ])
            ->add('promo', EntityType::class, [
                'class' => Promo::class,
                'choice_label' => 'nomPromo',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserInfo::class,
        ]);
    }
}
