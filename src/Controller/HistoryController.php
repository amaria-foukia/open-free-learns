<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Course;
use App\Entity\History;
use App\Form\HistoryType;
use App\Repository\HistoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HistoryController extends AbstractController
{

    /**
     * Affiche le détail de l'historique 
     * 
     * @Route("/histories/{id}", name="history_show")
     * 
     * @return Response
     */
    public function show($id)
    {
        $history = $this->getDoctrine()

            ->getRepository(History::class)
            ->find($id);

        return $this->render('history/show.html.twig', [
            'history' => $history
        ]);
    }
}
