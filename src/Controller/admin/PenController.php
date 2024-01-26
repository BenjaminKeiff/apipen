<?php

namespace App\Controller\admin;

use App\Entity\Pen;
use App\Form\PenType;
use App\Repository\PenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted("ROLE_ADMIN")]
#[Route('/pen')]
class PenController extends AbstractController
{
    #[Route('/', name: 'app_pen_admin', methods: ['GET'])]
    public function index(PenRepository $penRepository): Response
    {
        return $this->render('pen/index.html.twig', [
            'pens' => $penRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_pen_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pen = new Pen();
        $form = $this->createForm(PenType::class, $pen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pen);
            $entityManager->flush();

            return $this->redirectToRoute('app_pen_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pen/new.html.twig', [
            'pen' => $pen,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pen_show', methods: ['GET'])]
    public function show(Pen $pen): Response
    {
        return $this->render('pen/show.html.twig', [
            'pen' => $pen,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pen_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pen $pen, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PenType::class, $pen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pen_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pen/edit.html.twig', [
            'pen' => $pen,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pen_delete', methods: ['POST'])]
    public function delete(Request $request, Pen $pen, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pen->getId(), $request->request->get('_token'))) {
            $entityManager->remove($pen);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pen_admin', [], Response::HTTP_SEE_OTHER);
    }
}
