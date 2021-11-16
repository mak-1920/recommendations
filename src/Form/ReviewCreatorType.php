<?php

namespace App\Form;

use App\Entity\Review;
use App\Entity\ReviewGroup;
use App\Entity\ReviewTags;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewCreatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('text')
            ->add('tags', ChoiceType::class, [
                'attr' => [
                    'class' => 'tags-data',
                ],
            ])
            // ->add('tags', options: [
            //     'class' => ReviewTags::class,
            //     'choice_label' => 'name',
            //     'attr' => [
            //         'class' => 'tags-input',
            //         // 'disabled' => 'disabled',
            //     ],
            // ])
            ->add('tags', CollectionType::class, [
                'entry_type' => ReviewTagType::class,
                'allow_add' => true,
                'label' => 'Tags',

            ])
            ->add('group', EntityType::class, [
                'class' => ReviewGroup::class,
            ])
            // ->add('tagsOfString', TextType::class, [
            //     'mapped' => false,
            //     'label' => 'Tags',
            //     'attr' => [
            //         'class' => 'tags-input',
            //     ],
            // ])
            ->add('save', SubmitType::class, ['label' => 'Create'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
