<?php

declare(strict_types=1);

namespace App\Form\Review;

use App\Entity\Review\Review;
use App\Entity\Review\ReviewGroup;
use App\Entity\Review\ReviewTag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ReviewCreatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', options:[
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('text', options: [
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('tags_input', EntityType::class, [
                'mapped' => false,
                'class' => ReviewTag::class,
                'choice_label' => 'name',
                'label' => 'Tags',
                'attr' => [
                    'class' => 'tags-input',
                    'name' => null,
                    'placeholder' => '',
                ],
            ])
            ->add('tags', CollectionType::class, [
                'entry_type' => ReviewTagType::class,
                'allow_add' => true,
                'by_reference' => false,
                'entry_options' => ['label' => false],
            ])
            ->add('author_rating', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 10,
                    'step' => 1,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Regex('/\d+/', "String must be digits"),
                ]
            ])
            ->add('group', EntityType::class, [
                'class' => ReviewGroup::class,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'review-create-button btn btn-primary',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
