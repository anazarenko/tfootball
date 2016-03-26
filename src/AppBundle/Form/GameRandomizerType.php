<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameRandomizerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('players', 'entity', array(
                'class' => 'AppBundle\Entity\User',
                'choice_label' => 'username',
                'query_builder' => function(EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('u');
                    // the function returns a QueryBuilder object
                    return $qb
                        ->where($qb->expr()->notLike('u.roles', ':role'))
                        ->setParameter('role', '%ROLE_SPECTATOR%');
                },
                'multiple' => true,
                'label' => 'Players',
                'attr' => array('class' => 'form-control'),
                'mapped' => false
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
        return 'randomizer';
    }
}
