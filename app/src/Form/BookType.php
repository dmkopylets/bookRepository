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
use App\Form\DataTransformer\TagsToIdsTransformer;
//use Symfony\Component\Form\FormEvents;
//use Symfony\Component\Form\FormEvent;

class BookType extends AbstractType
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tagsArray = [];
        foreach ($options['tags'] as $tag) {
            $tagsArray[$tag->getTitle()] = $tag->getId();
        }

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
            ->add('tags', ChoiceType::class, [
                'choices' => $tagsArray,  // Масив 'Назва' => 'ID'
                'label' => 'Select Tags',
//                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => false,  // Виводимо у вигляді select
                'autocomplete' => true,
                'attr' => [
                    'class' => 'custom-select-multiple',
                    'size' => 6,
                    'style' => 'overflow-y: scroll;',
                ],
            ]);
        $builder->get('tags')->addModelTransformer(new TagsToIdsTransformer($this->entityManager));

    }

//        $builder->addEventListener(
//            FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//            $form = $event->getForm();
//
//            //$tags = $form->getParent()->getParent()->getData();
//            $tags = $form->get('tags')->getData();
//
//            if ($tags) {
//                $parameters = json_decode($tags->getParameters(), true);

//                $bankFormData = $parameters['form']['spb-banks'] ?? null;

//                if ($bankFormData) {
//                    $event->setData([
//                        'label' => $bankFormData['label'],
//                        'options' => array_values($bankFormData['options'])
//                    ]);

//            }
//        });
//    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
            'tags' => [],
        ]);
    }
}
