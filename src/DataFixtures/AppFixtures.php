<?php

namespace App\DataFixtures;

use Faker\Factory;

use App\Entity\Video;
use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        for ($i = 1; $i <= 12; $i++) {
            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('<p></p>', $faker->paragraphs(3)) . '</p>';

            $course = new Course();
            $course->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content);

            for ($j = 1; $j <= mt_rand(1, 3); $j++) {
                $video = new Video();

                $video->setIframe('<iframe width="560" height="315" src="https://www.youtube.com/embed/HWxBtxPBCAc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>')
                    ->setTitle($faker->sentence())
                    ->setCourse($course);

                $manager->persist($video);
            }

            $manager->persist($course);
        }
        $manager->flush();
    }
}
