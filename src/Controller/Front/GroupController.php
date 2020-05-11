<?php

namespace App\Controller\Front;

use App\Entity\Group;
use App\Entity\Photo;
use App\Entity\Post;
use App\Repository\GroupRepository;
use App\Repository\PostRepository;
use App\Services\File\UploadFile;
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
     * @Route("/groupe/ajouter", name="front_group_add_post")
     */
    public function addPostGroup(Request $request, GroupRepository $groupRepository)
    {
        if (!$request->get('group-id') || !$request->get('quick-post-text')) {
            return $this->json([
                'message' => 'Informations manquantes ou erronées'
            ]);
        }

        $group = $groupRepository->find($request->get('group-id'));

        $post = new Post();
        $post->setUserGroup($group);
        $post->setType('post');
        $post->setText($request->get('quick-post-text'));

        if ($request->files->get('inpFile')) {
            $filename = UploadFile::uploadPhotoPost($request->files->get('inpFile'));

            $photo = new Photo();
            $photo->setUrl($filename);
            $photo->setPost($post);
            $post->addPhoto($photo);

            $this->em->persist($photo);
        }

        $this->em->persist($post);
        $this->em->flush();


        return $this->json([
            'post' => $this->render('front/html/post_group.html.twig', ['group' => $group, 'post' => $post])
        ]);
    }

    /**
     * @Route("/groupe/{slug}/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="front_group_single")
     */
    public function single(Group $group, PostRepository $postRepository, Request $request, PaginatorInterface $paginator)
    {
        if ($request->isXmlHttpRequest()) {
            $page = $request->request->get('page');

            $pagination = $paginator->paginate(
                $postRepository->findBy(['userGroup' => $group], ['id' => 'DESC']),
                $page, /*page number*/
                10 /*limit per page*/
            );

            return $this->json([
                'posts' => $this->render('front/html/posts.html.twig', ['posts' => $pagination->getItems(), 'last_page' => false])
            ]);

        } else {
            $posts = $postRepository->findBy(['userGroup' => $group], ['id' => 'DESC'], 10);
        }

        if ($group->getAuthor() !== $this->getUser()) {
            $group->setNbViews($group->getNbViews() + 1);

            $this->em->flush();
        }

        return $this->render('front/group/single.html.twig', [
            'group' => $group,
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/groupe/{slug}/rejoindre-group", name="front_group_leave_join_group")
     */
    public function leaveJoinGroup(Group $group)
    {
        if ($group->getMembers()->contains($this->getUser())) {
            $group->removeMember($this->getUser());
        } else {
            $group->addMember($this->getUser());
        }
        
        $this->em->flush();

        return $this->redirectToRoute('front_group_single', ['slug' => $group->getSlug()]);
    }
}
