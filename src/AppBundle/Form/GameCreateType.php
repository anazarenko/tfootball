<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstTeam', 'entity', array(
                'class' => 'AppBundle\Entity\User',
                'choice_label' => 'username',
                'multiple' => true,
                'label' => 'Player',
                'attr' => array('class' => 'form-control')
            ))
            ->add('firstScore', 'number', array(
                'invalid_message' => 'Invalid number',
                'attr' => array('class' => 'form-control')
            ))
            ->add('secondScore', 'number', array(
                'invalid_message' => 'Invalid number',
                'attr' => array('class' => 'form-control')
            ))
            ->add('secondTeam', 'entity', array(
                'class' => 'AppBundle\Entity\User',
                'choice_label' => 'username',
                'multiple' => true,
                'label' => 'Player',
                'attr' => array('class' => 'form-control')
            ))
            ->add('form', 'choice', array(
                'choices' => array(0 => '1x1', 1 => '2x2'),
                'data' => 0,
                'attr' => array('class' => 'form-control')
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
        return 'game_create';
    }
}
