<?php

namespace App\Controller\Front\Profil;

use App\Entity\CommentPost;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\User;
use App\Form\ArticleUserType;
use App\Form\CommentArticleUserType;
use App\Repository\PostRepository;
use App\Services\File\UploadFile;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserBlogController extends AbstractController
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
     * @Route("/blog/article/{slug}", name="front_profil_blog_single")
     */
    public function single(Post $post, Request $request, PostRepository $postRepository, PaginatorInterface $paginator)
    {
        $comment = new CommentPost();
        $form = $this->createForm(CommentArticleUserType::class, $comment)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setPost($post);
            $comment->setUser($this->getUser());

            $this->em->persist($comment);
            $this->em->flush();

            return $this->redirectToRoute('front_profil_blog_single', ['slug' => $post->getSlug()]);
        }

        if ($request->get('data')) {
            $data = json_decode($request->get('data'));

            $pagination = $paginator->paginate(
                $post->getCommentPosts(),
                $data->pageId,
                6
            );

            return $this->json([
                'comments' => $this->render('front/html/comments.html.twig', ['comments' => $pagination]),
                'nbPage' => $pagination->count()
            ]);
        } else {
            $pagination = $paginator->paginate(
                $post->getCommentPosts(),
                1,
                6
            );
        }

        $lastArticles = $postRepository->findBy(['user' => $post->getUser()], ['publishedAt' => 'DESC'], 2);

        return $this->render('front/profil/blog_single.html.twig', [
            'userProfil' => $post->getUser(),
            'post' => $post,
            'form' => $form->createView(),
            'comments' => $pagination,
            'lastArticles' => $lastArticles
        ]);
    }

    /**
     * @Route("/blog/{pseudo}/ajouter", name="front_profil_blog_add")
     */
    public function add(User $user, Request $request, SluggerInterface $slugger)
    {
        $post = new Post();
        $form = $this->createForm(ArticleUserType::class, $post)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post->setText(str_replace('script', '', $post->getText()));
            if ($form->get('photo')->getData()) {
                $filename = UploadFile::uploadPhotoPost($form->get('photo')->getData());

                $photo = new Photo();
                $photo->setPost($post);
                $photo->setUrl($filename);

                $this->em->persist($photo);
                $post->addPhoto($photo);
            }

            $post->setType(Post::ARTICLE_POST_TYPE);
            $post->setUser($this->getUser());
            $post->setSlug($slugger->slug($post->getTitle())->lower());

            $this->em->persist($post);
            $this->em->flush();

            $this->addFlash('success', 'Article soumis avec succès !');
            return $this->redirectToRoute('front_profil_blog_list', ['pseudo' => $this->getUser()->getPseudo()]);
        }

        return $this->render('front/profil/blog_add.html.twig', [
            'form' => $form->createView(),
            'userProfil' => $user
        ]);
    }

    /**
     * @Route("/blog/{pseudo}/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="front_profil_blog_list")
     */
    public function list(User $user, int $page, PaginatorInterface $paginator, PostRepository $postRepository)
    {
        $articles = $postRepository->findBy(['user' => $user, 'type' => 'article']);

        $pagination = $paginator->paginate(
            $articles,
            $page,
            9
        );

        return $this->render('front/profil/blog_list.html.twig', [
            'countArticles' => count($articles),
            'articles' => $pagination,
            'userProfil' => $user
        ]);
    }
}
