<?php

namespace AppBundle\Form;

use AppBundle\Entity\Tournament;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TournamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('form', ChoiceType::class, array(
                'choices' => array(
                    Tournament::FORM_SINGLE => 'Single',
                    Tournament::FORM_DOUBLE => 'Double'
                ),
                'label' => 'Type'
            ))
            ->add('regularGameCount', ChoiceType::class, array(
                'choices' => array(
                    1 => 1,
                    2 => 2
                ),
                'label' => 'Regular game count'
            ))
            ->add('playoffTeamCount', ChoiceType::class, array(
                'choices' => array(
                    2 => 2,
                    4 => 4,
                    8 => 8
                ),
                'label' => 'Playoff team count'
            ))
            ->add('playoffGameCount', ChoiceType::class, array(
                'choices' => array(
                    1 => 1,
                    2 => 2
                ),
                'label' => 'Playoff game count'
            ))
            ->add('finalGameCount', ChoiceType::class, array(
                'choices' => array(
                    1 => 1,
                    2 => 2
                ),
                'label' => 'Final game count'
            ))
            ->add('users', EntityType::class, array(
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
                'mapped' => false,
                'required' => false
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Tournament',
        ));
    }

    public function getName()
    {
        return 'tournament_form';
    }
}
