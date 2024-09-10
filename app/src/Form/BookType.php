<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\TagsTransformer;

class BookType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tagRepository = new TagRepository();
        $tags = $tagRepository->findAll();
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
                'label' => 'Select Tags',
                //'choices' => Tag::class,
                'choices' => array_map(function (Tag $tag) {
                    return $tag->getTitle();
                }, $tags),
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('selectedTags', TextType::class, [
                'label' => 'Selected Tags',
                'model_transformer' => new TagsTransformer(),
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-primary mt-4'
                ]
            ])
        ;
    }


//    css
//
//.scrollable-checkboxes {
//height: 150px;
//overflow-y: scroll;
//border: 1px solid #595572;
//padding: 10px;
//background-color: transparent;
//border-radius: 0.375rem;
//}
//
//.scrollable-checkboxes input {
//    margin-right: 10px;



//->add('options', ChoiceType::class, [
//'choices' => $this->payWaysService->getCashinoutSpbBanks(),
//'multiple' => true,
//'expanded' => true,
//'label' => 'Select Banks',
//'attr' => [
//'class' => 'scrollable-checkboxes',
//],
//])->add('type', HiddenType::class, [
//'empty_data' => 'select',
//'data' => 'select'
//]);


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
