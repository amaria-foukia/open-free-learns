<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Video;
use App\Form\CourseType;
use App\Repository\CourseRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CourseController extends AbstractController
{
    /**
     * @Route("/courses", name="courses_index")
     */
    public function index(CourseRepository $repo)
    {
        $courses = $repo->findAll();
        return $this->render('course/index.html.twig', [
            'courses' => $courses
        ]);
    }

    /**
     * creation d'un cours
     * 
     * @Route("courses/new", name="courses_create")
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
     * Permet d'afficher une seule annonce 
     * 
     * @Route("/courses/{slug}", name="courses_show")
     * 
     * @return Response
     */
    public function show(Course $course)
    {

        return $this->render('course/show.html.twig', [
            'course' => $course
        ]);
    }
}
