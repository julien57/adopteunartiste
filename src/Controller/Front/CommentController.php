<?php

namespace App\Controller\Front;

use App\Entity\CommentPost;
use App\Entity\Notification;
use App\Form\CommentArticleUserType;
use App\Repository\CommentPostRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
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
     * @Route("/comments/get-comments", name="front_comment_comments")
     */
    public function getComments(Request $request, PaginatorInterface $paginator, CommentPostRepository $commentPostRepository, PostRepository $postRepository)
    {
        $comment = new CommentPost();
        $form = $this->createForm(CommentArticleUserType::class, $comment)->handleRequest($request);
        $idForm = 'form-'.uniqid();

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$request->get('postId')) {
                return $this->json([
                    'message' => 'Missing parameters'
                ]);
            }

            $post = $postRepository->find($request->get('postId'));

            $comment->setPost($post);
            $comment->setUser($this->getUser());

            $notification = new Notification();
            $notification->setUserFrom($this->getUser());
            $notification->setUserTo($post->getUser());
            $notification->setType(Notification::NOTIF_COMMENT_TYPE);
            $notification->setPost($post);

            $this->em->persist($comment);
            $this->em->persist($notification);
            $this->em->flush();

            return $this->json([
                'comment' => $this->render('front/html/comment_fil.html.twig', ['comment' => $comment])
            ]);
        }

        if ($request->get('data')) {
            $data = json_decode($request->get('data'));

            if (!$data->pageId || !$data->dataPost) {
                return $this->json([
                    'message' => 'Missing parameters'
                ]);
            }

            $post = $postRepository->find($data->dataPost);

            if (!$post) {
                return $this->json([
                    'message' => 'Post not found'
                ]);
            }

            $pagination = $paginator->paginate(
                $commentPostRepository->findBy(['post' => $post], ['publishedAt' => 'DESC']),
                $data->pageId,
                4
            );

            return $this->json([
                'comments' => $this->render('front/html/comments.html.twig', ['comments' => $pagination]),
                'nbPage' => $pagination->count(),
                'form' => $this->render('front/html/form_comment.html.twig', ['form' => $form->createView(), 'idForm' => $idForm]),
                'idForm' => $idForm
            ]);
        }
    }
}
