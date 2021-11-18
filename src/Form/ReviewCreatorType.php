<?php

namespace App\Form;

use App\Entity\Review;
use App\Entity\ReviewGroup;
use App\Entity\ReviewTags;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewCreatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('text')
            ->add('tags_input', EntityType::class, [
                'mapped' => false,
                'class' => ReviewTags::class,
                'choice_label' => 'name',
                'label' => 'Tags',
                'attr' => [
                    'class' => 'tags-input',
                    'name' => null,
                ],
            ])
            ->add('tags', CollectionType::class, [
                'entry_type' => ReviewTagType::class,
                'allow_add' => true,
                'by_reference' => false,
                'entry_options' => ['label' => false],
            ])
            ->add('author_raiting', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 10,
                    'step' => 1,
                ]
            ])
            ->add('group', EntityType::class, [
                'class' => ReviewGroup::class,
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
