<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\BackUserType;
use App\Repository\UserRepository;
use App\Services\Mail\SendMail;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/{page}", defaults={"page"=1}, requirements={"page"="\d+"}, name="user_index", methods={"GET"})
     */
    public function index(int $page, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $userRepository->findBy([], ['id' => 'DESC']),
            $page,
            25
        );

        return $this->render('back/user/index.html.twig', [
            'users' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder, SendMail $sendMail): Response
    {
        $user = new User();
        $form = $this->createForm(BackUserType::class, $user, ['isFront' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = uniqid();

            $passwordEncoded = $encoder->encodePassword($user, $password);
            $user->setPassword($passwordEncoded);
            $user->setRoles(["ROLE_USER"]);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $sendMail->newPasswordUser($password, $user);

            return $this->redirectToRoute('user_index');
        }

        return $this->render('back/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/voir/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('back/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(BackUserType::class, $user, ['isFront' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('back/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
