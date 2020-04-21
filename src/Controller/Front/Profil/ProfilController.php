<?php

namespace App\Controller\Front\Profil;

use App\Entity\Adopt;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\AdoptRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
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
     * @Route("/profil/add-new-adopt/{id}", name="front_profil_add_adopt", requirements={"id":"\d+"})
     */
    public function addAdopte(User $user)
    {
        $adopt = new Adopt();
        $adopt->setUserTo($this->getUser());
        $adopt->setUserFrom($user);

        $this->em->persist($adopt);
        $this->em->flush();

        return $this->json([
            'message' => 'ok'
        ]);
    }

    /**
     * @Route("/profil/remove-adopt/{id}", name="front_profil_remove_adopt", requirements={"id":"\d+"})
     */
    public function removeAdopt(User $user, AdoptRepository $adoptRepository)
    {
        $adopt = $adoptRepository->findOneBy(['userFrom' => $user, 'userTo' => $this->getUser()]);
        if (!$adopt) {
            $adopt = $adoptRepository->findOneBy(['userFrom' => $this->getUser(), 'userTo' => $user]);
        }
        if ($adopt) {
            $this->em->remove($adopt);
            $this->em->flush();
        }

        return $this->json([
            'message' => 'ok'
        ]);
    }

    /**
     * @Route("/profil/supprimer-post/{id}", name="front_profil_remove_post")
     */
    public function removePost(Post $post)
    {
        if ($post->getUser() === $this->getUser()) {
            foreach ($post->getReacts() as $react) {
                $post->removeReact($react);
            }
            $this->em->remove($post);
            $this->em->flush();
        }

        return $this->redirectToRoute('front_profil_user', ['pseudo' => $this->getUser()->getPseudo()]);
    }

    /**
     * @Route("/profil/{pseudo}/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="front_profil_user")
     */
    public function profilUser(User $user, int $page, Request $request, PostRepository $postRepository, PaginatorInterface $paginator)
    {
        if ($user !== $this->getUser()) {
            $user->setNbVisit($user->getNbVisit() + 1);

            $this->em->flush();
        }

        if ($request->isXmlHttpRequest()) {
            $page = $request->request->get('page');

            $pagination = $paginator->paginate(
                $postRepository->findBy(['user' => $user], ['publishedAt' => 'DESC']),
                $page, /*page number*/
                10 /*limit per page*/
            );

            return $this->json([
                'posts' => $this->render('front/html/posts.html.twig', ['posts' => $pagination->getItems(), 'last_page' => false])
            ]);

        } else {
            $posts = $postRepository->findBy(['user' => $user], ['publishedAt' => 'DESC'], 10);

            return $this->render('front/profil/profil_user.html.twig', [
                'posts' => $posts,
                'page' => $page,
                'userProfil' => $user
            ]);
        }
    }
}
