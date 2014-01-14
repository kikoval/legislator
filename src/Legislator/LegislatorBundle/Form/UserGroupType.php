<?php
namespace Legislator\LegislatorBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class UserGroupType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('groups');
    }

    public function getName()
    {
        return 'legislator_user_group';
    }
}
