<?php

namespace App\Form;

use App\Entity\Tasks;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TasksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('endAt')
            ->add('priority', ChoiceType::class, [
                'choices' => [
                    'Low' => 'LOW',
                    'Normal' => 'NORMAL',
                    'High' => 'HIGH'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Open' => 'OPEN',
                    'In progress' => 'IN PROGRESS',
                    'Closed' => 'CLOSED'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tasks::class,
        ]);
    }
}
