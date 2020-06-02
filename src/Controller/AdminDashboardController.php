<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\StatService;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ObjectManager $manager, StatService $statService)
    {
        $stats          = $statService->getStats();
        $bestCourses    = $statService->getAdsStats('DESC');
        $worstCourses   = $statService->getAdsStats('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
            'bestCourses' => $bestCourses,
            'worstCourses' => $worstCourses,
        ]);
    }
}
