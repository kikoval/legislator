<?php

namespace Legislator\LegislatorBundle\Form;

use Legislator\LegislatorBundle\Form\ContentSectionType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', 'textarea')
//             ->add('version')
                ->add('file')
//                ->add('Add document', 'submit', array('attr' => array('class' => 'btn')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Legislator\LegislatorBundle\Entity\Document'
        ));
    }

    public function getName()
    {
        return 'legislator_document';
    }
}
