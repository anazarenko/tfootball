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
                'choice_label' => 'username'
            ))
            ->add('firstGoals', 'integer')
            ->add('secondGoals', 'integer')
            ->add('secondPlayer', 'entity', array(
                'class' => 'AppBundle\Entity\User',
                'choice_label' => 'username'
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
