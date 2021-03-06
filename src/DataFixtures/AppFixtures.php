<?php

namespace App\DataFixtures;

use Faker\Factory;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\Video;
use App\Entity\Course;
use App\Entity\Comment;
use App\Entity\History;
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

        //Gestion des rôles et creation d'un User ayant ROLE Admin et ROLE manager
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $managerRole = new Role();
        $managerRole->setTitle('ROLE_MANAGER');
        $manager->persist($managerRole);

        $adminUser = new User();

        $adminUser->setFirstName('Amaria')
            ->setLastName('Foukia')
            ->setEmail('amaria@symfony.com')
            ->setHash($this->encoder->encodePassword($adminUser, 'voyager'))
            ->setPicture('https://randomuser.me/api/portraits/women/42.jpg')
            ->setDescription($faker->sentence())
            ->addUserRole($adminRole)
            ->addUserRole($managerRole);

        $manager->persist($adminUser);

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
        for ($i = 1; $i <= 40; $i++) {
            $coverImage = "https://picsum.photos/1000/350?random=" . mt_rand(1, 500);
            $title = $faker->sentence();
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

            //Gestion des historiques 
            for ($j = 1; $j <= mt_rand(0, 10); $j++) {
                $history = new History();

                $createdAt = $faker->dateTimeBetween('-6 months');
                $student = $users[mt_rand(0, count($users) - 1)];

                $history->setStudent($student)
                    ->setCourse($course)
                    ->setCreatedAt($createdAt);

                $manager->persist($history);

                //Gestion des commentaires -> Un commentaire pour 1 annonce sur 2
                if (mt_rand(0, 1)) {
                    $comment = new Comment();
                    $comment->setRating(mt_rand(1, 5))
                        ->setContent($faker->paragraph())
                        ->setAuthorComment($student)
                        ->setCourse($course);

                    $manager->persist($comment);
                }
            }

            $manager->persist($course);
        }
        $manager->flush();
    }
}
