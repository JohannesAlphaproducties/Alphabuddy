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
                ],
                'label' => 'Beschrijving',
            ])
            ->add('hours', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'type' => 'datetime-local',
                ],
                'label' => 'Uren',
            ])
            ->add('date', DateTimeType::class, [
//                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control h-25',
                ],
                'label' => 'Datum',
            ])
            ->add('workorder', EntityType::class, [
                'attr' => [
                    'class' => 'form-control js-select required',
                ],
                'choice_label' => function ($workOrder) {
                    return $workOrder->getTitel(). ' ' . $workOrder->getCompany();
                },
                'required' => true,
                'class' => WorkOrders::class,
                'label' => 'Werkbon',
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
