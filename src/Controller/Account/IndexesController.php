<?php

namespace App\Controller\Account;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\IndexesType;
use App\Entity\Indexes;
use App\Repository\IndexesRepository;

#[Route('/account/indexes')]
class IndexesController extends AbstractController
{

    #[Route('/new', name: 'app_indexes_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        Security $security
    ): Response
    {
        $index = new Indexes();
        $form = $this->createForm(IndexesType::class, $index);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $index->setFkUser($security->getUser());
            $entityManager->persist($index);
            $entityManager->flush();

            $this->addFlash('success', ['text' => 'Index enregistrés', 'title' => 'Super !']);

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/indexes/new.html.twig', [
            'index' => $index,
            'form' => $form,
        ]);
    }

    #[Route('/edit', name: 'app_indexes_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        IndexesRepository $indexesRepository, 
        EntityManagerInterface $entityManager,
        Security $security
    ): Response
    {
        $index = $indexesRepository->findOneBy(['fk_user' => $security->getUser()]);
        $form = $this->createForm(IndexesType::class, $index);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {dd($index);
            $index->setFkUser($security->getUser());
            $entityManager->flush();

            return $this->redirectToRoute('app_indexes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/indexes/edit.html.twig', [
            'index' => $index,
            'form' => $form,
        ]);
    }
}
