<?php

namespace Legislator\LegislatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'document.name'))
            ->add('description', 'textarea',
                    array('label' => 'document.description'))
            ->add('comment_until', 'date',
                    array('label' => 'document.comment_until',
                          'required' => FALSE))
            ->add('groups', null,
                    array('label' => 'document.groups',
                          'required' => FALSE,
                          'expanded' => TRUE))
            ->add('file', 'file',
                    array('label' => 'document.file', 'required' => TRUE))
            ->add('file_substantiation', 'file',
                    array('label' => 'document.file_substantiation',
                          'required' => FALSE))
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
