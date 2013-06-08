<?php

namespace Legislator\LegislatorBundle\Form;

use Legislator\LegislatorBundle\Form\DocumentType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentSectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('level')
            ->add('text')
            ->add('createdOn')
            ->add('modifiedOn')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Legislator\LegislatorBundle\Entity\ContentSection',
        	'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'legislator_contentsection';
    }
}
