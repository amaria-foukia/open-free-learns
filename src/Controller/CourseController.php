<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\Course;
use App\Entity\Comment;
use App\Entity\History;
use App\Form\CourseType;
use App\Form\CommentType;
use App\Service\Pagination;
use App\Repository\CourseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CourseController extends AbstractController
{
    /**
     * Permet d'afficher tous les cours 
     * 
     * @Route("/courses/{page<\d+>?1}", name="courses_index")
     */
    public function index($page, Pagination $pagination)
    {
        $pagination->setEntityClass(Course::class)
            ->setPage($page);

        return $this->render('course/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Création d'un cours
     * 
     * @Route("courses/new", name="courses_create")
     * 
     * @Security("is_granted('ROLE_ADMIN') and is_granted('ROLE_MANAGER')", message="Vous n'avez pas l'autorisation de créer un cours, contactez l'admistrateur ... ")
     * 
     * @return Response
     */
    public function create(Request $request)
    {
        $course = new Course();

        $form = $this->createForm(CourseType::class, $course);

        $form->handleRequest($request);

        $manager = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($course->getVideos() as $video) {
                $video->setCourse($course);
                $manager->persist($video);
            }

            $course->setAuthor($this->getUser());

            $manager->persist($course);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$course->getTitle()}</strong> a bien été enregistrée !"
            );

            return $this->redirectToRoute('courses_show', [
                'slug' => $course->getSlug()
            ]);
        }

        return $this->render('course/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route("/courses/{slug}/edit", name="courses_edit")
     * 
     * @Security("is_granted('ROLE_ADMIN') and is_granted('ROLE_MANAGER') and user === course.getAuthor()", message="Vous n'êtes pas autorisé à modifier cette annonce !")
     * 
     * @return Response
     */
    public function edit(Course $course, Request $request)
    {

        $form = $this->createForm(CourseType::class, $course);

        $form->handleRequest($request);

        $manager = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($course->getVideos() as $video) {
                $video->setCourse($course);
                $manager->persist($video);
            }

            $manager->persist($course);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'annonce <strong>{$course->getTitle()}</strong> ont bien été enregistrées !"
            );

            return $this->redirectToRoute('courses_show', [
                'slug' => $course->getSlug()
            ]);
        }

        return $this->render('course/edit.html.twig', [
            'form' => $form->createView(),
            'course' => $course
        ]);
    }

    /**
     * Permet de supprimer un cours
     * 
     * @Route("/courses/{slug}/delete", name="courses_delete")
     * @Security("is_granted('ROLE_USER') and user == course.getAuthor()", message="Vous n'avez pas le droit de supprimer ce cours")
     *
     * @param Course $course
     * 
     * @return Response
     */
    public function delete(Course $course)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($course);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$course->getTitle()}</strong> a bien été supprimée !"
        );

        return $this->redirectToRoute("courses_index");
    }


    /**
     * Permet d'afficher une seule annonce 
     * 
     * @Route("/courses/{slug}", name="courses_show")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function show(Course $course)
    {
        //Creation de l'historique 
        $manager = $this->getDoctrine()->getManager();

        $history = new History();

        $user = $this->getUser();

        $history->setStudent($user)
            ->setCourse($course);

        $manager->persist($history);

        $this->addFlash(
            'success',
            "L'historique a été enregistré avec succès !"
        );

        $manager->flush();

        return $this->render('course/show.html.twig', [
            'course' => $course,
            'history' => $history
        ]);
    }

    /**
     * Permet de noter et de commenter 
     * 
     * @Route("/courses/{slug}/comment", name="comment_create")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function comment(Course $course, Request $request)
    {

        //Creation d'un commentaire

        $comment = new Comment();

        $manager = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setCourse($course)
                ->setAuthorComment($user);

            $manager->persist($comment);

            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire a été enregistré avec succès !"
            );

            return $this->redirectToRoute('courses_show', ['slug' => $course->getSlug()]);
        }

        return $this->render('course/comment.html.twig', [
            'course' => $course,
            'form' => $form->createView()
        ]);
    }
}
