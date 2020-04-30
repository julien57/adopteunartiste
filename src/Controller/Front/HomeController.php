<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\BackUserType;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="front_home_index")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em, AuthenticationUtils $authenticationUtils)
    {
        $user = new User();
        $form = $this->createForm(BackUserType::class, $user, ['isFront' => true])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(["ROLE_USER"]);
            $password = $user->getPassword();
            $passwordEncoded = $encoder->encodePassword($user, $password);
            $user->setPassword($passwordEncoded);

            $em->persist($user);
            $em->flush();
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('front/index.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="front_home_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/search", name="front_search_bar")
     */
    public function search(Request $request, UserRepository $userRepository, GroupRepository $groupRepository)
    {
        if ($request->get('data')) {
            $searchValue = json_decode($request->get('data'));
            $users = $userRepository->searchUser($searchValue->search);
            $groups = $groupRepository->searchGroup($searchValue->search);

            return $this->json([
                'users' => $this->render('front/search.html.twig', ['users' => $users]),
                'groups' => $this->render('front/search_group.html.twig', ['groups' => $groups]),
            ]);
        }

        return $this->render('', [
            'search' => $this->render('')
        ]);
    }
}
