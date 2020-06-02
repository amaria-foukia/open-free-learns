<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Service\Pagination;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     */
    public function index($page, Pagination $pagination)
    {
        $pagination->setEntityClass(Comment::class)
            ->setPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet de modifier un commentaire
     * 
     * @Route("/admin/comments/{id}/edit" , name="admin_comments_edit") 
     *
     * @param Comment $comment
     * @return Response
     */
    public function edit(Comment $comment, Request $request)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);

        $manager = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire numéro {$comment->getId()} a bien été modifié !"
            );

            return $this->redirectToRoute('admin_comments_index');
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     * 
     * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")
     *
     * @param Comment $comment
     * @return Response
     */
    public function delete(Comment $comment)
    {
        $manager = $this->getDoctrine()->getManager();

        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le commentaire de {$comment->getAuthorComment()->getFullName()} a bien été supprimé !"
        );

        return $this->redirectToRoute('admin_comments_index');
    }
}
