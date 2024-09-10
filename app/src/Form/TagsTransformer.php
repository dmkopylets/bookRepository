<?php

namespace App\Form;

use App\Entity\Tag;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;

class TagsTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $entityManager;

    public function transform($values): mixed
    {
        if (null === $values) {
            return '';
        }

        return implode(', ', array_map(fn (Tag $tag) => $tag->getTitle(), $values));
    }

    public function reverseTransform($string): mixed
    {
        if (null === $string) {
            return [];
        }

        // Логіка для перетворення рядка назад в масив об'єктів Tag
        // Наприклад, якщо у вас є метод для пошуку тегів за title:
        $tagRepository = $this->entityManager->getRepository(Tag::class);
        $tagTitles = explode(', ', $string);
        $tags = $tagRepository->findBy(['title' => $tagTitles]);

        return $tags;
    }
}
