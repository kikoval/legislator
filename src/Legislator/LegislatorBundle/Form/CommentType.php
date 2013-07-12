<?php

namespace Legislator\LegislatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Legislator\LegislatorBundle\Entity\Comment;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'textarea', array('label' => 'comment.content'))
            ->add('substantiation', 'textarea', array('label' => 'comment.substantiation'))
            ->add('type', 'choice',
            		array('label' => 'comment.type',
            			  'choices' => Comment::getTypes()))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Legislator\LegislatorBundle\Entity\Comment'
        ));
    }

    public function getName()
    {
        return 'legislator_comment';
    }
}
