<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('phone')
            ->add('fax')
            ->add('website')
            ->add('type')
            ->add('employees')
            ->add('revenue')
            ->add('description')
            ->add('billing_address')
            ->add('billing_zip')
            ->add('billing_town')
            ->add('email')
            ->add('tlId')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
