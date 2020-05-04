<?php

namespace App\Controller\Front\Space;

use App\Entity\Messaging;
use App\Entity\User;
use App\Repository\MessagingRepository;
use App\Repository\UserRepository;
use App\Services\File\UploadFile;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/espace-perso")
 */
class MessagingController extends AbstractController
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
     * @Route("/messagerie", name="front_space_messaging_list")
     */
    public function list(MessagingRepository $messagingRepository, UserRepository $userRepository)
    {
        $usersId = $messagingRepository->getMessages($this->getUser());

        $users = [];
        foreach ($usersId as $id) {
            $user = $userRepository->find($id['sendForId']);
            $users[] = $user;
        }
        $firstUser = $users[0];
        $messagings = $messagingRepository->getMessageChat($firstUser, $this->getUser());

        return $this->render('front/space/messaging.html.twig', [
            'users' => $users,
            'firstUser' => $users[0],
            'messagings' => $messagings
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     *
     * @Route("/messagerie/ajouter/{id}", name="front_space_messaging_create")
     */
    public function createMessaging(User $user, MessagingRepository $messagingRepository)
    {
        if(empty($messagingRepository->getIfMessagingExist($user, $this->getUser()))) {
            $messaging = new Messaging();
            $messaging->setSendTo($this->getUser());
            $messaging->setSendFor($user);

            $this->em->persist($messaging);
            $this->em->flush();
        }

        return $this->redirectToRoute('front_space_messaging_list');
    }

    /**
     * @Route("/display-messaging", name="front_space_display_messaging")
     */
    public function displayMessaging(Request $request, UserRepository $userRepository, MessagingRepository $messagingRepository)
    {
        if ($request->get('idUser')) {
            $user = $userRepository->find($request->get('idUser'));
            $newMessages = $messagingRepository->getNewMessageChat($user, $this->getUser());

            $messagings = $messagingRepository->getMessageChat($user, $this->getUser());

            if (!empty($newMessages)) {

                foreach ($messagings as $messaging) {
                    $messaging->setIsNew(false);
                }

                $this->em->flush();

                return $this->json([
                    'isNew' => 'ok',
                    'view_messaging' => $this->render('front/html/messaging.html.twig', [
                        'user' => $user,
                        'messagings' => $messagings,
                    ]),
                    'barUser' => $this->render('front/html/bar_user.html.twig', ['user' => $user])
                ]);
            } else {
                return $this->json([
                    'isNew' => 'ok',
                    'view_messaging' => $this->render('front/html/messaging.html.twig', [
                        'user' => $user,
                        'messagings' => $messagings,
                    ]),
                    'barUser' => $this->render('front/html/bar_user.html.twig', ['user' => $user])
                ]);
            }

        }
    }

    /**
     * @Route("/get-new-message", name="front_space_get_new_message")
     */
    public function getNewMessage(MessagingRepository $messagingRepository, UserRepository $userRepository, Request $request)
    {
        if ($request->get('idUser')) {
            $user = $userRepository->find($request->get('idUser'));
            $newMessages = $messagingRepository->getNewMessageChat($user, $this->getUser());

            if (!empty($newMessages)) {
                return $this->json(['message' => 'true']);
            } else {
                return $this->json(['message' => 'false']);
            }
        }
    }

    /**
     * @Route("/send-message", name="front_space_send_message")
     */
    public function sendMessage(Request $request, UserRepository $userRepository)
    {
        if (($request->get('chat_widget_message_text_2') || $request->files->get('sendPhoto')) && $request->get('userId')) {

            $user = $userRepository->find($request->get('userId'));

            $message = new Messaging();
            $message->setSendFor($user);
            $message->setSendTo($this->getUser());
            $message->setPublishedAt(new \DateTime());
            $message->setMessage($request->get('chat_widget_message_text_2'));

            if ($request->files->get('sendPhoto')) {
                $filename = UploadFile::uploadPhotoMessaging($request->files->get('sendPhoto'));
                $message->setPhoto($filename);
            }

            $this->em->persist($message);
            $this->em->flush();

            return $this->json([
                'message' => 'ok',
                'userId' => $request->get('userId')
            ]);
        }
    }
}
