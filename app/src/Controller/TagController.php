<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Tag;

#[Route('/api', name: 'api_')]
class TagController extends AbstractController
{
    #[Route('/tags', name: 'tag_index', methods:['get'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $tags = $entityManager
            ->getRepository(Tag::class)
            ->createQueryBuilder('t')
            ->select('t.id, t.title')
            ->getQuery()
            ->getArrayResult();

        return $this->json($tags);
    }

    #[Route('/tags', name: 'tag_create', methods:['post'] )]
    public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $tag = new Tag();
        $tag->setTitle($request->request->get('title'));

        $entityManager->persist($tag);
        $entityManager->flush();

        $data =  [
            'id' => $tag->getId(),
            'title' => $tag->getTitle(),
        ];

        return $this->json($data);
    }


    #[Route('/tags/{id}', name: 'tag_show', methods:['get'] )]
    public function show(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $tag = $entityManager->getRepository(Tag::class)->find($id);

        if (!$tag) {

            return $this->json('No tag found for id ' . $id, 404);
        }

        $data =  [
            'id' => $tag->getId(),
            'title' => $tag->getTitle(),
        ];

        return $this->json($data);
    }

    #[Route('/tags/{id}', name: 'tag_update', methods:['put', 'patch'] )]
    public function update(EntityManagerInterface $entityManager, Request $request, int $id): JsonResponse
    {
        $tag = $entityManager->getRepository(Tag::class)->find($id);

        if (!$tag) {
            return $this->json('No tag found for id ' . $id, 404);
        }

        $tag->setName($request->request->get('title'));
        $entityManager->flush();

        $data =  [
            'id' => $tag->getId(),
            'title' => $tag->getName(),
        ];

        return $this->json($data);
    }

    #[Route('/tags/{id}', name: 'tag_delete', methods:['delete'] )]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $tag = $entityManager->getRepository(Tag::class)->find($id);

        if (!$tag) {
            return $this->json('No tag found for id ' . $id, 404);
        }

        $entityManager->remove($tag);
        $entityManager->flush();

        return $this->json('Deleted a tag successfully with id ' . $id);
    }
}
