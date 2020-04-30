<?php

namespace App\Controller\Front\Space;

use App\Entity\Adopt;
use App\Entity\Competence;
use App\Entity\Group;
use App\Entity\Service;
use App\Entity\User;
use App\Form\CompetencesUserType;
use App\Form\GroupSocialType;
use App\Form\GroupType;
use App\Form\InfosUserType;
use App\Form\PasswordUserType;
use App\Form\ServicesUserType;
use App\Form\SocialUserType;
use App\Repository\AdoptRepository;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use App\Services\File\UploadFile;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/espace-perso")
 */
class PersonalSpace extends AbstractController
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
     * @Route("/mes-infos", name="front_space_infos")
     */
    public function infos(Request $request, UserRepository $userRepository)
    {
        $user = $userRepository->find($this->getUser()->getId());
        $oldAvatar = $user->getAvatar();
        $oldCover = $user->getCover();

        $form = $this->createForm(InfosUserType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('avatar')->getData()) {
                $filename = UploadFile::uploadAvatar($form->get('avatar')->getData());
                $user->setAvatar($filename);
            } else {
                $user->setAvatar($oldAvatar);
            }

            if ($form->get('cover')->getData()) {
                $filename = UploadFile::uploadCover($form->get('cover')->getData());
                $user->setCover($filename);
            } else {
                $user->setCover($oldCover);
            }

            $this->em->flush();

            $this->addFlash('success', 'Informations mises à jour !');
            return $this->redirectToRoute('front_space_infos');
        }

        return $this->render('front/space/infos.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     *
     * @Route("/reseaux-sociaux", name="front_space_social")
     */
    public function spaceSocial(Request $request, UserRepository $userRepository)
    {
        $user = $userRepository->find($this->getUser()->getId());
        $form = $this->createForm(SocialUserType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'Réseaux sociaux mis à jour !');
            return $this->redirectToRoute('front_space_social');
        }

        return $this->render('front/space/social.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     *
     * @Route("/mot-de-passe", name="front_space_password")
     */
    public function spacePassword(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $user = $userRepository->find($this->getUser()->getId());
        $form = $this->createForm(PasswordUserType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $passwordEncoded = $encoder->encodePassword($user, $form->get('password')->getData());
            $user->setPassword($passwordEncoded);
            $this->em->flush();

            $this->addFlash('success', 'Mot de passe mis à jour !');
            return $this->redirectToRoute('front_space_password');
        }

        return $this->render('front/space/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     *
     * @Route("/demandes-adoption", name="front_space_adoption_request")
     */
    public function friendsRequest(AdoptRepository $adoptRepository)
    {
        $adopts = $adoptRepository->findBy(['userFrom' => $this->getUser(), 'isValid' => false]);

        return $this->render('front/space/adopts.html.twig', [
            'adopts' => $adopts
        ]);
    }

    /**
     * @Route("/confirm-friend-request", name="front_space_confirm_adoption")
     */
    public function confirmFriendRequest(Request $request, AdoptRepository $adoptRepository)
    {
        if ($request->get('data')) {
            $adoptId = json_decode($request->get('data'));

            $adopt = $adoptRepository->find($adoptId->adoptId);
            $adopt->setIsValid(true);

            $this->em->flush();

            return $this->json([
                'message' => 'ok'
            ]);
        }

        return $this->json([
            'message' => 'Adopt non reconnu'
        ]);
    }

    /**
     * @Route("/decline-friend-request", name="front_space_decline_adoption")
     */
    public function declineFriendRequest(Request $request, AdoptRepository $adoptRepository)
    {
        if ($request->get('data')) {
            $adoptId = json_decode($request->get('data'));
            $adopt = $adoptRepository->find($adoptId->adoptId);

            $this->em->remove($adopt);

            $this->em->flush();

            return $this->json([
                'message' => 'ok'
            ]);
        }

        return $this->json([
            'message' => 'Adopt non reconnu'
        ]);
    }

    /**
     * @Route("/services", name="front_space_services")
     */
    public function services(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ServicesUserType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($user->getServices() as $service) {
                $service->setUser($user);
            }

            $this->em->flush();

            $this->addFlash('success', 'Vos services ont bien été mis à jour');
            return $this->redirectToRoute('front_space_competences');
        }

        return $this->render('front/space/services.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/suppression-service/{id}", name="front_space_service_remove")
     */
    public function removeService(Service $service)
    {
        if ($this->getUser()->getServices()->contains($service)) {
            $this->em->remove($service);

            $this->em->flush();
        }

        return $this->redirectToRoute('front_space_services');
    }

    /**
     * @Route("/competences", name="front_space_competences")
     */
    public function competences(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(CompetencesUserType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($user->getCompetences() as $competence) {
                $competence->setUser($user);
            }

            $this->em->flush();

            $this->addFlash('success', 'Vos compétences ont bien été mis à jour');
            return $this->redirectToRoute('front_space_competences');
        }

        return $this->render('front/space/competences.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/suppression-competence/{id}", name="front_space_competence_remove")
     */
    public function removeCompetence(Competence $competence)
    {
        if ($this->getUser()->getcompetences()->contains($competence)) {
            $this->em->remove($competence);

            $this->em->flush();
        }

        return $this->redirectToRoute('front_space_competences');
    }

    /**
     * @Route("/mes-groupes", name="front_space_groups")
     */
    public function groups(GroupRepository $groupRepository, Request $request, SluggerInterface $slugger)
    {
        $groups = $groupRepository->findBy(['author' => $this->getUser()]);

        $newGroup = new Group();
        $form = $this->createForm(GroupType::class, $newGroup)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('avatar')->getData()) {
                $filename = UploadFile::uploadAvatar($form->get('avatar')->getData());
                $newGroup->setAvatar($filename);
            }

            if ($form->get('cover')->getData()) {
                $filename = UploadFile::uploadCover($form->get('cover')->getData());
                $newGroup->setCover($filename);
            }

            $newGroup->setAuthor($this->getUser());
            $slug = $slugger->slug($newGroup->getName())->lower();
            $newGroup->setSlug($slug);

            $this->em->persist($newGroup);
            $this->em->flush();

            $this->addFlash('success', 'Groupe créé !');
            return $this->redirectToRoute('front_space_groups');
        }

        return $this->render('front/space/groups.html.twig', [
            'groups' => $groups,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/gestion-groupe/infos/{slug}", name="gront_space_manage_group")
     */
    public function manageGroup(Group $group, Request $request, SluggerInterface $slugger)
    {
        $oldAvatar = $group->getAvatar();
        $oldCover = $group->getCover();

        $form = $this->createForm(GroupType::class, $group)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('avatar')->getData()) {
                $filename = UploadFile::uploadAvatar($form->get('avatar')->getData());
                $group->setAvatar($filename);
            } else {
                $group->setAvatar($oldAvatar);
            }

            if ($form->get('cover')->getData()) {
                $filename = UploadFile::uploadCover($form->get('cover')->getData());
                $group->setCover($filename);
            } else {
                $group->setCover($oldCover);
            }

            $slug = $slugger->slug($group->getName())->lower();
            $group->setSlug($slug);

            $this->em->persist($group);
            $this->em->flush();

            $this->addFlash('success', 'Modifications du groupe '.$group->getName().' enregistrées !');
            return $this->redirectToRoute('front_space_groups');
        }

        return $this->render('front/space/manage_group.html.twig', [
            'form' => $form->createView(),
            'group' => $group
        ]);
    }

    /**
     * @Route("/gestion-groupe/membres/{slug}", name="gront_space_manage_group_members")
     */
    public function groupMembers(Group $group)
    {
        return $this->render('front/space/manage_group_members.html.twig', [
            'members' => $group->getMembers(),
            'group' => $group
        ]);
    }

    /**
     * @Route("/gestion-groupe/reseaux-sociaux/{slug}", name="front_space_manage_group_social")
     */
    public function groupSocial(Group $group, Request $request)
    {
        $form = $this->createForm(GroupSocialType::class, $group)->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->em->flush();

            $this->addFlash('success', 'Modifications du groupe '.$group->getName().' enregistrées !');
            return $this->redirectToRoute('front_space_groups');
        }

        return $this->render('front/space/manage_group_social.html.twig', [
            'form' => $form->createView(),
            'group' => $group
        ]);
    }
}
