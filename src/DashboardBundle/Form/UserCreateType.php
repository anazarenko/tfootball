<?php

namespace DashboardBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserCreateType extends AbstractType
{

    public function __construct($options = array())
    {
        $this->roles = $options['roles'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array('attr' => array('class' => 'form-control')))
            ->add('email', 'email', array('attr' => array('class' => 'form-control')))
            ->add('password', 'password', array('attr' => array('class' => 'form-control')))
            ->add(
                'roles',
                'choice',
                array(
                    'choices' => array(
                        0 => 'ROLE_ADMIN',
                        1 => 'ROLE_USER',
                        2 => 'ROLE_SPECTATOR'
                    ),
                    'data' => '1',
                    'label' => 'Role',
                    'attr' => array('class' => 'form-control')
                )
//            ->add('roles', 'choice', array(
//                    'required' => true,
//                    'multiple' => true,
//                    'choices' => $this->roles
//                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'user_create';
    }
}
