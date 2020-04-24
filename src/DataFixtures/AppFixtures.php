<?php

namespace App\DataFixtures;

use Faker\Factory;

use App\Entity\User;
use App\Entity\Video;
use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        //gestion des utilisateurs
        $users = [];
        $genres = ['male', 'female'];

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstname($genre))
                ->setLastName($faker->lastname)
                ->setEmail($faker->email)
                ->setDescription($faker->sentence())
                ->setHash($hash)
                ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }

        //gestion des cours
        for ($i = 1; $i <= 12; $i++) {
            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('<p></p>', $faker->paragraphs(2)) . '</p>';

            $user = $users[mt_rand(0, count($users) - 1)];


            $course = new Course();
            $course->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setLevel("Debutant")
                ->setAuthor($user);

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
