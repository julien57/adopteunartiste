<?php

namespace App\Controller\Front\General;

use App\Form\SearchGroup;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
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
     * @Route("/groupes/nouveaux/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="front_groups_groups_new")
     */
    public function groupsNew(int $page, Request $request, GroupRepository $groupRepository, PaginatorInterface $paginator)
    {
        $form = $this->createForm(SearchGroup::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groups = $groupRepository->searchGroupGroups($form->get('searchGroup')->getData());
        } else {
            $groups = $groupRepository->findBy([], ['createdAt' => 'DESC']);
        }

        $pagination = $paginator->paginate(
            $groups,
            $page,
            9
        );

        return $this->render('front/general/groups.html.twig', [
            'groups' => $pagination,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/groupes/populaires/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="front_groups_groups_popular")
     */
    public function groupsPopular(int $page, Request $request, GroupRepository $groupRepository, PaginatorInterface $paginator)
    {
        $form = $this->createForm(SearchGroup::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groups = $groupRepository->findByCountMembersSearch($form->get('searchGroup')->getData());
        } else {
            $groups = $groupRepository->findByCountMembers();
        }

        $pagination = $paginator->paginate(
            $groups,
            $page,
            9
        );

        return $this->render('front/general/groups.html.twig', [
            'groups' => $pagination,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/groupes/alphabetique/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="front_groups_groups_alphabetic")
     */
    public function groupsAlphabetic(int $page, Request $request, GroupRepository $groupRepository, PaginatorInterface $paginator)
    {
        $form = $this->createForm(SearchGroup::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groups = $groupRepository->searchGroupSearchByName($form->get('searchGroup')->getData());
        } else {
            $groups = $groupRepository->findBy([], ['name' => 'ASC']);
        }

        $pagination = $paginator->paginate(
            $groups,
            $page,
            9
        );

        return $this->render('front/general/groups.html.twig', [
            'groups' => $pagination,
            'form' => $form->createView()
        ]);
    }
}
