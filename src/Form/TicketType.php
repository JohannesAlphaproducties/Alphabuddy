<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('priority', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => ['1' => '1', '2' => '2', '3' => '3'],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => ['nog beginnen' => 'beginnen', 'bezig' => 'bezig', 'klaar, de ticket wordt naar de klant verstuurd' => 'klaar'],
            ])
            ->add('responsible', EntityType::class, [
                'attr' => [
                    'class' => 'form-control js-select',
                ],
                'class' => User::class,
                'multiple' => true,
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
            'data_class' => Ticket::class,
        ]);
    }
}
