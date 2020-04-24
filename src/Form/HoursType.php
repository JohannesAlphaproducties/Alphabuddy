<?php

namespace App\Form;

use App\Entity\Hours;
use App\Entity\User;
use App\Entity\WorkOrders;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('hours', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'type' => 'datetime-local',
                ]
            ])
            ->add('date', DateTimeType::class, [
//                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control h-25',
                ]
            ])
            ->add('workorder', EntityType::class, [
                'attr' => [
                    'class' => 'form-control js-select required',
                ],
                'required' => true,
                'class' => WorkOrders::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Hours::class,
        ]);
    }
}
