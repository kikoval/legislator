<?php

namespace Legislator\LegislatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Legislator\LegislatorBundle\Entity\Comment;

class CommentReplyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('result', 'choice',
                    array('choices' => Comment::getResultChoices(),
                          'expanded' => false,
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
