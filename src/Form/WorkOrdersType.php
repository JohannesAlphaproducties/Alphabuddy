<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\User;
use App\Entity\WorkOrders;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrdersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titel', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('status', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => [
                    'nieuw' => 'nieuw',
                    'bezig' => 'bezig',
                    'Spullen moeten besteld worden' => 'spullen moeten besteld worden',
                    'spullen besteld' => 'spullen besteld',
                    'wachten op externe monteur' => 'wachten op externe monteur',
                    'wachten op backorder' => 'wachten op backorder',
                    'opnieuw inplannen' => 'opnieuw inplannen',
                    'afgesloten' => 'afgesloten'
                ],
            ])
            ->add('time', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control h-25',
                    'type' => 'datetime-local',
                ],
            ])
            ->add('comment', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('priority', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => ['1' => '1', '2' => '2', '3' => '3'],
            ])
            ->add('mechanic', EntityType::class, [
                'attr' => [
                  'class' => 'form-control js-select',
                ],
                'class' => User::class,
                'choice_label' => 'name',
                'multiple' => 'multiple',
            ])

            ->add('company', EntityType::class, [
                'attr' => [
                    'class' => 'form-control js-select',
                ],
                'class' => Company::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WorkOrders::class,
        ]);
    }
}
