<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\EntityManagerInterface;

class BookType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)

    {
        $this->entityManager = $entityManager;
    }

public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tags = $options['data']->getTags()->toArray();
        $entityManager = $options['entity_manager'];

        $builder
            ->add('title', TextType::class, [
                'label' => 'Назва',
                'attr' => [
                    'placeholder' => 'Title'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Опис'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'label' => 'Select Tags',
                'choice_label' => 'title',
                'multiple' => true,
//                'expanded' => true,
                'attr' => [
                    'class' => 'custom-select-multiple',
                    'size' => 6,
                    'style' => 'overflow-y: scroll;',
                    ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
            'entity_manager' => null,
            'tags' => [],
        ]);
    }
}
