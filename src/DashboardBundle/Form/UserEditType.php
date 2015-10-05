<?php

namespace DashboardBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array('attr' => array('class' => 'form-control')))
            ->add('email', 'email', array('attr' => array('class' => 'form-control')))
            ->add('password', 'password', array('required' => false, 'attr' => array('class' => 'form-control')))
            ->add(
                'roles',
                'choice',
                array(
                    'choices' => array(
                        0 => 'ROLE_ADMIN',
                        1 => 'ROLE_USER'
                    ),
                    'data' => '1',
                    'label' => 'Role',
                    'attr' => array('class' => 'form-control')
                )
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
        return 'user_edit';
    }
}
