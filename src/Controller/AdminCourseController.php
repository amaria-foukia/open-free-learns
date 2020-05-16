<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\Course;
use App\Form\VideoType;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCourseController extends AbstractController
{
    /**
     * @Route("/admin/courses", name="admin_courses_index")
     */
    public function index(CourseRepository $repo)
    {
        $courses = $repo->findAll();

        return $this->render('admin/course/index.html.twig', [
            'courses' => $courses
        ]);
    }

    /**
     * Permet de modifier un cours
     * 
     * @Route("/admin/courses/{slug}/edit" , name="admin_courses_edit") 
     *
     * @param Course $course
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
                "L'annonce <strong>{$course->getTitle()}</strong> a bien été modifiée !"
            );

            return $this->redirectToRoute('admin_courses_index', [
                'slug' => $course->getSlug()
            ]);
        }

        return $this->render('admin/course/edit.html.twig', [
            'course' => $course,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce qui n'a pas d'historique 
     * 
     * @Route("/admin/courses/{slug}/delete" , name="admin_courses_delete")  
     *
     * @param Course $course
     * @return Response
     */
    public function delete(Course $course)
    {
        $manager = $this->getDoctrine()->getManager();

        /*    if (count($course->getHistories()) > 0) {
            $this->addFlash(
                'warning',
                "Vous devez d'abord supprimer l'historique du cours : <strong>{$course->getTitle()}</strong>!"
            );
        } else { */

        $manager->remove($course);

        $manager->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$course->getTitle()}</strong> a bien été supprimée !"
        );

        /*  } */

        return $this->redirectToRoute('admin_courses_index', [
            'slug' => $course->getSlug()
        ]);
    }
}
