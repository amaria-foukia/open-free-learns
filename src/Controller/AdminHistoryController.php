<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\History;
use App\Form\HistoryType;
use App\Form\AdminHistoryType;
use App\Repository\HistoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminHistoryController extends AbstractController
{

    /**
     * Affichage de tous les historiques 
     * 
     *@Route("/admin/histories", name="admin_histories_index")
     * 
     *@return Response
     * 
     */
    public function index(HistoryRepository $repo)
    {
        $histories = $repo->findAll();

        return $this->render('/admin/history/index.html.twig', [
            'histories' => $histories
        ]);
    }

    /**
     * Permet de modifier un historique
     *
     * @Route("/admin/histories/{id}/edit" , name="admin_histories_edit")
     * 
     * @param History $history
     * @param Request $request
     * @return Response
     */
    public function edit(History $history, Request $request)
    {
        $form = $this->createForm(AdminHistoryType::class, $history);

        $form->handleRequest($request);

        $manager = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($history);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'historique n°: {$history->getId()} a bien été modifié !"
            );

            return $this->redirectToRoute('admin_histories_index');
        }

        return $this->render('admin/history/edit.html.twig', [
            'form' => $form->createView(),
            'history' => $history
        ]);
    }

    /**
     * Supprimer un historique
     * 
     * @Route("/admin/histories/{id}/delete" , name="admin_histories_delete")
     *
     * @param History $history
     * @return Response
     */
    public function delete(History $history)
    {

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($history);
        $manager->flush();

        $this->addFlash(
            'success',
            'lhistorique a été supprimé avec succès !'
        );

        return $this->redirectToRoute('admin_histories_index');
    }
}
