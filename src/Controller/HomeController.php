<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(CourseRepository $courseRepo)
    {
        return $this->render(
            'home.html.twig',
            [
                'courses' => $courseRepo->getBestCourses(4)
            ]
        );
    }
}
