<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Book;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/api', name: 'api_')]
class BookController extends AbstractController
{
    #[Route('/books', name: 'book_index', methods:['get'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): JsonResponse
    {
        $booksQuery = $entityManager
            ->getRepository(Book::class)
            ->createQueryBuilder('b')
            ->getQuery();

        $page = $request->query->getInt('page', 1);

        $pagination = $paginator->paginate(
            $booksQuery,
            $page,
            5 /* кількість елементів на сторінці */
        );
dd($pagination);
        $data = [];

        foreach ($pagination as $book) {
            $data[] = [
                'id' => $book->getId(),
                'category' => $book->getCategoryTitle,
                'title' => $book->getTitle(),
                'description' => $book->getDescription(),
                'tags' => $book->getTagsAsString(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/books', name: 'book_create', methods:['post'] )]
    public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $book = new Book();
        $book->setTitle($request->request->get('title'));
        $book->setDescription($request->request->get('description'));

        $entityManager->persist($book);
        $entityManager->flush();

        $data =  [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'description' => $book->getDescription(),
        ];

        return $this->json($data);
    }

    #[Route('/books/{id}', name: 'book_show', methods:['get'] )]
    public function show(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {

            return $this->json('No book found for id ' . $id, 404);
        }

        $data =  [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'description' => $book->getDescription(),
        ];

        return $this->json($data);
    }

    #[Route('/books/{id}', name: 'book_update', methods:['put', 'patch'] )]
    public function update(EntityManagerInterface $entityManager, Request $request, int $id): JsonResponse
    {
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            return $this->json('No book found for id ' . $id, 404);
        }

        $book->setName($request->request->get('title'));
        $book->setDescription($request->request->get('description'));
        $entityManager->flush();

        $data =  [
            'id' => $book->getId(),
            'title' => $book->getName(),
            'description' => $book->getDescription(),
        ];

        return $this->json($data);
    }

    #[Route('/books/{id}', name: 'book_delete', methods:['delete'] )]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            return $this->json('No book found for id ' . $id, 404);
        }

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->json('Deleted a book successfully with id ' . $id);
    }
}
