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
            ->add('isAccepted')
            ->add('reply')
//             ->add('Add comment', 'submit')
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
