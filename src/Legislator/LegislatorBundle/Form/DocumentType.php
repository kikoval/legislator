<?php

namespace Legislator\LegislatorBundle\Form;

use Legislator\LegislatorBundle\Form\ContentSectionType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Legislator\LegislatorBundle\Entity\Document;

class DocumentType extends AbstractType
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
            ->add('status', 'choice',
                    array('choices' => Document::getStatusMessages()))
            ->add('is_final_version', 'checkbox',
            		array('label' => 'document.final_version',
        				  'required' => FALSE))
        	->add('is_archived', 'checkbox',
        			array('label' => 'document.is_archived',
        				  'required' => FALSE))
        	->add('groups', null, array('required' => FALSE))
            ->add('file', 'file',
                    array('required' => FALSE, 'label' => 'document.file'))
            ->add('file_substantiation', 'file',
                    array('required' => FALSE, 'label' => 'document.file_substantiation'))
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
