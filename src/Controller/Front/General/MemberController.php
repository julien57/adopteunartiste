<?php

namespace App\Controller\Front\General;

use App\Form\SearchUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/artistes/nouveaux/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="front_general_members_new")
     */
    public function userNew(int $page, Request $request, UserRepository $userRepository, PaginatorInterface $paginator)
    {
        $form = $this->createForm(SearchUserType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $users = $userRepository->searchGroupGroups($form->get('searchUser')->getData());
        } else {
            $users = $userRepository->findBy([], ['subscribedAt' => 'DESC']);
        }

        $pagination = $paginator->paginate(
            $users,
            $page,
            9
        );

        return $this->render('front/general/members.html.twig', [
            'users' => $pagination,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/artistes/actifs/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="front_groups_members_actifs")
     */
    public function groupsPopular(int $page, Request $request, UserRepository $userRepository, PaginatorInterface $paginator)
    {
        $form = $this->createForm(SearchUserType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $users = $userRepository->getMembersByActifWithSearch($form->get('searchUser')->getData());
        } else {
            $users = $userRepository->getMembersByActif();
        }

        $pagination = $paginator->paginate(
            $users,
            $page,
            9
        );

        return $this->render('front/general/members.html.twig', [
            'users' => $pagination,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/artistes/alphabetique/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="front_groups_members_alphabetic")
     */
    public function groupsAlphabetic(int $page, Request $request, UserRepository $userRepository, PaginatorInterface $paginator)
    {
        $form = $this->createForm(SearchUserType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $users = $userRepository->searchGroupGroups($form->get('searchUser')->getData());
        } else {
            $users = $userRepository->findBy([], ['pseudo' => 'ASC']);
        }

        $pagination = $paginator->paginate(
            $users,
            $page,
            9
        );

        return $this->render('front/general/members.html.twig', [
            'users' => $pagination,
            'form' => $form->createView()
        ]);
    }
}
