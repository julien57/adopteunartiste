<?php

namespace App\Controller\Back;

use App\Entity\DomainArtist;
use App\Form\DomainArtistType;
use App\Repository\DomainArtistRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/domain/artist")
 */
class DomainArtistController extends AbstractController
{
    /**
     * @Route("/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="back_domain_artist_index", methods={"GET"})
     */
    public function index(int $page, DomainArtistRepository $domainArtistRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $domainArtistRepository->findBy([], ['id' => 'DESC']),
            $page,
            25
        );

        return $this->render('back/domain_artist/index.html.twig', [
            'domain_artists' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="back_domain_artist_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $domainArtist = new DomainArtist();
        $form = $this->createForm(DomainArtistType::class, $domainArtist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($domainArtist);
            $entityManager->flush();

            return $this->redirectToRoute('back_domain_artist_index');
        }

        return $this->render('back/domain_artist/new.html.twig', [
            'domain_artist' => $domainArtist,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/voir/{id}", name="back_domain_artist_show", methods={"GET"})
     */
    public function show(DomainArtist $domainArtist): Response
    {
        return $this->render('back/domain_artist/show.html.twig', [
            'domain_artist' => $domainArtist,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="back_domain_artist_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DomainArtist $domainArtist): Response
    {
        $form = $this->createForm(DomainArtistType::class, $domainArtist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('back_domain_artist_index');
        }

        return $this->render('back/domain_artist/edit.html.twig', [
            'domain_artist' => $domainArtist,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="back_domain_artist_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DomainArtist $domainArtist): Response
    {
        if ($this->isCsrfTokenValid('delete'.$domainArtist->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($domainArtist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('back_domain_artist_index');
    }
}
