<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\User;
use App\Entity\WorkOrders;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class WorkOrdersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titel', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
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
                    'klaar voor ondertekenen' => 'klaar voor ondertekenen'
                ],
            ])
            ->add('time', DateTimeType::class, [
                'widget' => 'single_text',
//                'date_format' => 'Y-m-d H:i:s',
                'attr' => [
                    'class' => 'form-control h-25',
                    'type' => 'datetime-local',
                ],
                'label' => 'Tijd',
            ])
            ->add('comment', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Commentaar',
            ])
            ->add('priority', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => ['1' => '1', '2' => '2', '3' => '3'],
                'label' => 'Prioritijd',
            ])
            ->add('mechanic', EntityType::class, [
                'attr' => [
                  'class' => 'form-control js-select',
                ],
                'class' => User::class,
                'choice_label' => 'name',
                'multiple' => 'multiple',
                'label' => 'Monteur',
            ])

            ->add('company', EntityType::class, [
                'attr' => [
                    'class' => 'form-control js-select',
                ],
                'class' => Company::class,
                'label' => 'Bedrijf',
            ])

            ->add('image', FileType::class, [
                'attr' => [
                    'class' => 'form-control-file',
                ],
                'label' => 'Pdf, png, jpg of jpeg',
                'mapped' => false,
//                'multiple' => true,
                'required' => false,
//                'constraints' => [
//                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '15000k',
                                'mimeTypes' => [
                                    'application/pdf',
                                    'image/png',
                                    'image/jpeg'
                                ],
                                'mimeTypesMessage' => 'fout',
                            ])
                        ]
                    ])
//                ]
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WorkOrders::class,
        ]);
    }
}
