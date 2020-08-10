<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;

/**
 * @IsGranted("ROLE_USER")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        return $this->render('default/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/sendData/{user}", name="send_data")
     */
    public function __invoke(PublisherInterface $publisher,User $user): Response
    {
        $update = new Update(
            'http://primus-notif-hub.com/user/'.$user->getId(),
            json_encode(['status' => 'OutOfStock'])
        );

        // The Publisher service is an invokable object
        $publisher($update);

        return new Response('published!');
    }

    /**
     * @Route("/sendMessage/{user}/{message}", name="send_message")
     */
    public function sendMessage(PublisherInterface $publisher,User $user, $message): Response
    {
        $update = new Update(
            'http://primus-notif-hub.com/user/'.$user->getId(),
            json_encode(['status' => 'OutOfStock', 'message'=>$message])
        );

        // The Publisher service is an invokable object
        $publisher($update);

        return new Response('published!');
    }
}
