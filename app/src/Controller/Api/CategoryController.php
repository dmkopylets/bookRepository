<?php

namespace App\Controller\Api;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'category_index', methods:['get'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $categories = $entityManager
            ->getRepository(Category::class)
            ->createQueryBuilder('c')
            ->select('c.id, c.title')
            ->getQuery()
            ->getArrayResult();

        return $this->json($categories);
    }

    #[Route('/categories', name: 'category_create', methods:['post'] )]
    public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $category = new Category();
        $category->setTitle($request->request->get('title'));

        $entityManager->persist($category);
        $entityManager->flush();

        $data =  [
            'id' => $category->getId(),
            'title' => $category->getTitle(),
        ];

        return $this->json($data);
    }


    #[Route('/categories/{id}', name: 'category_show', methods:['get'] )]
    public function show(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $category = $entityManager->getRepository(Category::class)->find($id);

        if (!$category) {

            return $this->json('No category found for id ' . $id, 404);
        }

        $data =  [
            'id' => $category->getId(),
            'title' => $category->getTitle(),
        ];

        return $this->json($data);
    }

    #[Route('/categories/{id}', name: 'category_update', methods:['put', 'patch'] )]
    public function update(EntityManagerInterface $entityManager, Request $request, int $id): JsonResponse
    {
        $category = $entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            return $this->json('No category found for id ' . $id, 404);
        }

        $category->setName($request->request->get('title'));
        $entityManager->flush();

        $data =  [
            'id' => $category->getId(),
            'title' => $category->getName(),
        ];

        return $this->json($data);
    }

    #[Route('/categories/{id}', name: 'category_delete', methods:['delete'] )]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $category = $entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            return $this->json('No category found for id ' . $id, 404);
        }

        $entityManager->remove($category);
        $entityManager->flush();

        return $this->json('Deleted a category successfully with id ' . $id);
    }
}
