<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstTeam', 'entity', array(
                'class' => 'AppBundle\Entity\User',
                'choice_label' => 'username',
                'multiple' => true,
                'label' => 'Player',
                'attr' => array('class' => 'form-control'),
                'mapped' => false,
                'required' => false
            ))
            ->add('secondTeam', 'entity', array(
                'class' => 'AppBundle\Entity\User',
                'choice_label' => 'username',
                'multiple' => true,
                'label' => 'Player',
                'attr' => array('class' => 'form-control'),
                'mapped' => false,
                'required' => false
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Game',
        ));
    }

    public function getName()
    {
        return 'game_filter';
    }
}
