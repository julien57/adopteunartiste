<?php

namespace App\Controller\Front;

use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\React;
use App\Repository\PostRepository;
use App\Repository\ReactRepository;
use App\Repository\UserRepository;
use App\Services\File\UploadFile;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
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
     * @IsGranted("ROLE_USER")
     *
     * @Route("/ajouter", name="front_post_add")
     */
    public function addPost(Request $request)
    {
        if (!$request->get('user-profil') || !$request->get('quick-post-text')) {
            return $this->json([
                'message' => 'Informations manquantes ou erronées'
            ]);
        }

        $post = new Post();
        $post->setUser($this->getUser());
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
            'post' => $this->render('front/html/post.html.twig', ['user' => $this->getUser(), 'post' => $post])
        ]);
    }

    /**
     * @Route("react-post", name="front_react_post_add")
     */
    public function addReactPost(Request $request, PostRepository $postRepository, UserRepository $userRepository, ReactRepository $reactRepository)
    {
        if ($request->get('data')) {
            $data = json_decode($request->get('data'));
            if ($data->postId && $data->userId) {

                $post = $postRepository->find((string) $data->postId);
                $user = $userRepository->find((string) $data->userId);

                $reacts = $reactRepository->findBy(['post' => $post]);
                $reactExist = null;
                foreach ($reacts as $react) {
                    if ($react->getUsers()->contains($user)) {
                        $reactExist = $react;
                    }
                }
                if (!$reactExist) {
                    $react = new React();
                    $react->setPost($post);
                    $react->addUser($user);
                    $react->setType($data->react);

                    $this->em->persist($react);

                } else {
                    $reactExist->setType($data->react);
                }

                $this->em->flush();
            }
        }

        return $this->json([
            'react' => $this->render('front/html/react.html.twig', ['post' => $post])
        ]);
    }
}
