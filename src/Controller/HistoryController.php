<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\History;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HistoryController extends AbstractController
{
    /**
     * @Route("/history", name="history_index")
     * 
     * Security("is_granted('ROLE_USER') and user == course.getAuthor()")
     * 
     *  @return Response
     * 
     */
    public function history()
    {
        $histories = $this->getDoctrine()
            ->getRepository(History::class)
            ->findAll();

        return $this->render('history/index.html.twig', [
            'histories' => $histories
        ]);
    }

    /**
     * @Route("/history/{id}", name="history_show")
     * 
     * @return Response
     */
    public function historyByUser($id)
    {
        $history = $this->getDoctrine()

            ->getRepository(History::class)
            ->find($id);

        return $this->render('history/show.html.twig', [
            'history' => $history
        ]);
    }
}
