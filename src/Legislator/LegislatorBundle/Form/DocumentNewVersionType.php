<?php

namespace Legislator\LegislatorBundle\Form;

use Legislator\LegislatorBundle\Form\ContentSectionType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentNewVersionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text',
            		array('read_only' => TRUE,
            			  'disabled' => TRUE,
            			  'label' => 'document.name'))
            ->add('description', 'textarea',
            		array('label' => 'document.description'))
            ->add('is_final_version', 'checkbox',
            		array('label' => 'document.final_version',
            			  'required' => FALSE))
            ->add('file', 'file',
            		array('label' => 'document.file', 'required' => TRUE))
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
