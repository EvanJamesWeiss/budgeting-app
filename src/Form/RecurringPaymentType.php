<?php

namespace App\Form;

use App\Entity\ExpenseTemplate;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class RecurringPaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Title is required.'),
                    new Length(max: 255, maxMessage: 'Title cannot exceed 255 characters.'),
                ],
            ])
            ->add('defaultAmount', NumberType::class, [
                'required' => false,
                'scale' => 2,
            ])
            ->add('isStatic', ChoiceType::class, [
                'choices' => [
                    'Fixed' => true,
                    'Variable' => false,
                ],
            ])
            ->add('paidBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'constraints' => [
                    new NotBlank(message: 'Paid by is required.'),
                ],
            ])
            ->add('defaultSplitRatio', NumberType::class, [
                'scale' => 2,
                'constraints' => [
                    new NotBlank(message: 'Split ratio is required.'),
                    new Range(min: 0, max: 1, notInRangeMessage: 'Split ratio must be between 0 and 1.'),
                ],
            ])
        ;

        // Enforce defaultAmount is required when isStatic=true
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $form = $event->getForm();
            $data = $event->getData();

            if (!$form->isValid()) {
                return;
            }

            $isStatic = $form->get('isStatic')->getData();
            $defaultAmount = $form->get('defaultAmount')->getData();

            if ($isStatic === true && ($defaultAmount === null || $defaultAmount === '')) {
                $form->get('defaultAmount')->addError(
                    new FormError('Amount is required for fixed recurring payments.')
                );
            }

            if ($defaultAmount !== null && $defaultAmount !== '' && $defaultAmount < 0) {
                $form->get('defaultAmount')->addError(
                    new FormError('Amount must be 0 or greater.')
                );
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExpenseTemplate::class,
        ]);
    }
}
