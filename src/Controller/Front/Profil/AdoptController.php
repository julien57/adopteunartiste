<?php

namespace App\Controller\Front\Profil;

use App\Entity\User;
use App\Form\SearchUserType;
use App\Repository\AdoptRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/adoptions")
 */
class AdoptController extends AbstractController
{
    /**
     * @Route("/adoptes/{pseudo}/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="front_profile_adopt_list")
     */
    public function list(User $user, int $page, AdoptRepository $adoptRepository, PaginatorInterface $paginator, Request $request)
    {
        $form = $this->createForm(SearchUserType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adopts = $adoptRepository->searchAdopts($user, $form->get('searchUser')->getData());
        } else {
            $adopts = $adoptRepository->getAdopts($user);
        }

        $pagination = $paginator->paginate(
            $adopts,
            $page,
            9
        );

        return $this->render('front/profil/adopt_list.html.twig', [
            'adopts' => $pagination,
            'countAdopt' => count($adopts),
            'userProfil' => $user,
            'form' => $form->createView()
        ]);
    }
}
