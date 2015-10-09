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
            ->add('firstPlayer', 'entity', array(
                'class' => 'AppBundle\Entity\User',
                'choice_label' => 'username',
                'label' => 'Player'
            ))
            ->add('firstGoals', 'number', array('invalid_message' => 'Invalid number'))
            ->add('secondGoals', 'number', array('invalid_message' => 'Invalid number'))
            ->add('secondPlayer', 'entity', array(
                'class' => 'AppBundle\Entity\User',
                'choice_label' => 'username',
                'label' => 'Player'
            ))
            ->add('form', 'choice', array(
                'choices' => array(0 => '1x1', 1 => '2x2'),
                'data' => 0
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
