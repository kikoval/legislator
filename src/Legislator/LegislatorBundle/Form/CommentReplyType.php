<?php

namespace Legislator\LegislatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentReplyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isAccepted', 'choice',
                    array('choices' =>
                            array(1 => 'comment.accepted', 0 => 'comment.declined'),
                          'expanded' => true,
                          'label' => 'comment.acceptance'))
            ->add('reply', 'textarea', array('label' => 'comment.substantiation'))
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
        return 'legislator_comment_reply';
    }
}
