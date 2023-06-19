<?php

namespace App\Controller;

use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebsocketController extends AbstractController
{
    #[Route('/', name: 'app_websocket')]
    public function index(MessageRepository $messageRepository): Response
    {

        return $this->render('websocket/index.html.twig', [
            'controller_name' => 'WebsocketController',
            'messages' => $messageRepository->findAll()
        ]);
    }
}
