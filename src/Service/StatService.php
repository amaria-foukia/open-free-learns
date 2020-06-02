<?php

namespace App\Service;

use Doctrine\Persistence\ObjectManager;

class StatService
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        // initialisation
        $this->manager  = $manager;
    }

    public function getStats()
    {
        $users      = $this->getUsersCount();
        $courses    = $this->getCoursesCount();
        $histories   = $this->getHistoriesCount();
        $comments   = $this->getCommentsCount();

        return compact('users', 'courses', 'histories', 'comments');
    }

    public function getUsersCount()
    {
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getCoursesCount()
    {
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Course c')->getSingleScalarResult();
    }

    public function getHistoriesCount()
    {
        return $this->manager->createQuery('SELECT COUNT(h) FROM App\Entity\History h')->getSingleScalarResult();
    }


    public function getCommentsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(o) FROM App\Entity\Comment o')->getSingleScalarResult();
    }

    public function getAdsStats($direction)
    {
        return $this->manager->createQuery(
            'SELECT AVG(o.rating) as note, c.title, c.id, u.firstName, u.lastName, u.picture
        FROM App\Entity\Comment o
        JOIN o.course c
        JOIN c.author u
        GROUP BY c
        ORDER BY note ' . $direction
        )
            ->setMaxResults(5)
            ->getResult();
    }
}
