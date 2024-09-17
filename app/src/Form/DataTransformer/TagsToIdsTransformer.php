<?php

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class TagsToIdsTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Перетворює об'єкти Tag в їх ID для відображення у формі
    public function transform($tags): mixed
    {
        if (!$tags) {
            return [];
        }

        return array_map(function ($tag) {
            return $tag->getId();
        }, $tags->toArray());
    }

    // Перетворює ID тегів назад у об'єкти Tag після відправки форми
    public function reverseTransform($tagIds): mixed
    {
        if (!$tagIds) {
            return [];
        }

        return $this->entityManager->getRepository(Tag::class)->findBy(['id' => $tagIds]);
    }
}