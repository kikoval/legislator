<?php

namespace Legislator\LegislatorBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder->add('firstName', 'text', array('label' => 'form.firstname'))
                ->add('surname', 'text', array('label' => 'form.surname'));

        parent::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'legislator_user_profile';
    }
}
