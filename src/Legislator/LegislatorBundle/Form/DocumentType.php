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
            ->add('name', 'text', array('label' => 'document.name'))
            ->add('description', 'textarea',
                    array('label' => 'document.description'))
            ->add('status', 'choice',
                    array('choices' =>
                            array(0 => 'Nový', 'Pripomienkovanie',
                                    'Vyhodnocovanie pripomienok', 'Dokončený')))
            ->add('file', 'file',
                    array('required' => false, 'label' => 'document.file'))
            ->add('file_substantiation', 'file',
                    array('required' => false, 'label' => 'document.file_substantiation'))
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
