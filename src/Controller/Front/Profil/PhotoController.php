<?php

namespace App\Controller\Front\Profil;

use App\Entity\User;
use App\Repository\PhotoRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PhotoController extends AbstractController
{
    /**
     * @Route("/photos/{pseudo}/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="front_profile_photos")
     */
    public function list(User $user, int $page, PhotoRepository $photoRepository, PaginatorInterface $paginator)
    {
        $photos = $photoRepository->findAllPhotosByUser($user);

        $pagination = $paginator->paginate(
            $photos,
            $page,
            18
        );

        return $this->render('front/profil/photo_list.html.twig', [
            'photos' => $pagination,
            'userProfil' => $user,
            'countPhotos' => count($photos)
        ]);
    }
}
