<?php

namespace App\Controller\Front;

use App\Entity\CommentPost;
use App\Entity\User;
use App\Repository\CommentPostRepository;
use App\Repository\PostRepository;
use App\Repository\ReactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FilController extends AbstractController
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
     * @Route("/fil-actu/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="front_fil_list")
     */
    public function filList(int $page, Request $request, PostRepository $postRepository, PaginatorInterface $paginator)
    {
        if ($request->isXmlHttpRequest()) {
            $page = $request->request->get('page');

            $pagination = $paginator->paginate(
                $postRepository->findBy([], ['publishedAt' => 'DESC']),
                $page, /*page number*/
                10 /*limit per page*/
            );

            return $this->json([
                'posts' => $this->render('front/html/posts.html.twig', ['posts' => $pagination->getItems() , 'last_page' => false])
            ]);

        } else {
            $posts = $postRepository->getLast10Posts();

            return $this->render('front/fil/list.html.twig', [
                'posts' => $posts,
                'page' => $page
            ]);
        }
    }

    /**
     * @Route("/more-posts/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="front_fil_more_posts")
     */
    public function getOtherPosts(int $page, PostRepository $postRepository, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $postRepository->findBy([], ['publishedAt' => 'DESC']),
            $page, /*page number*/
            10 /*limit per page*/
        );

        return $this->json([
            'posts' => $this->render('front/html/posts.html.twig', ['posts' => $pagination->getItems()])
        ]);
    }

    /**
     * @Route("/get-picture-post", name="front_fil_get_picture")
     */
    public function getPicturePost(Request $request, PostRepository $postRepository, ReactRepository $reactRepository, CommentPostRepository $commentPostRepository)
    {
        if ($request->get('data')) {
            $postId = json_decode($request->get('data'));

            $post = $postRepository->findPostById($postId->postId);
            $comments = $commentPostRepository->getCommentsByPost($postId->postId);

            return $this->json([
                'post' => $post,
                'reacts' => $reactRepository->getReactPerPostJS($postId->postId),
                'comments' => $comments
            ]);
        }
    }

    /**
     * @IsGranted("ROLE_USER")
     *
     * @Route("/post/add-comment", name="front_fil_add_comment")
     */
    public function addComment(Request $request, PostRepository $postRepository)
    {
        if ($this->getUser() && $request->get('popup_post_reply') && $request->get('postId')) {

            $post = $postRepository->find($request->get('postId'));
            if (!$post) {
                return $this->json([
                    'message' => 'Post not found'
                ]);
            }
            /** @var User $user */
            $user = $this->getUser();
            $comment = new CommentPost();
            $comment->setUser($user);
            $comment->setPost($post);
            $comment->setText($request->get('popup_post_reply'));
            $post->addCommentPost($comment);
            $user->addCommentPost($comment);

            $this->em->persist($comment);
            $this->em->flush();

            return $this->json([
                'message' => 'ok',
                'comment' => $this->render('front/html/comment.html.twig', ['comment' => $comment])
            ]);
        }

        return $this->json([
            'message' => 'Missing porameters'
        ]);
    }
}
