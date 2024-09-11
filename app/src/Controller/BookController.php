<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Tag;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route(name: 'app_book_index', methods: ['get'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $booksQuery = $entityManager
            ->getRepository(Book::class)
            ->createQueryBuilder('b')
            ->select('b.id, c.title AS categoryTitle, b.title, b.description')
            ->leftJoin('b.category', 'c')
            ->groupBy('b.id, c.title')
            ->getQuery();

        $page = $request->query->getInt('page', 1);
        $pagination = $paginator->paginate($booksQuery, $page, 5);

        $temporaryData = [];
        foreach ($pagination->getItems() as $book) {
            $bookEntity = $entityManager->getRepository(Book::class)->find($book['id']);
            $book['tags'] = $bookEntity->getTagsAsString();
            $temporaryData[] = $book;
        }
        $pagination->setItems($temporaryData);

        return $this->render('book/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $tags = $book->getTags();
        $form = $this->createForm(BookType::class, $book, [
            'choices' => array_map(function (Tag $tag) {
                return $tag->getTitle();
            }, $tags),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
    }
}
