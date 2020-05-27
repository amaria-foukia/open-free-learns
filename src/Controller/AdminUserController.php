<?php

namespace App\Controller;

use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/users", name="admin_users_index")
     */
    public function index(UserRepository $repo)
    {
        $users = $repo->findAll();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * Permet d'apporter des modifications à un utilisateur 
     * 
     * @Route("/admin/users/{slug}/edit" , name="admin_users_edit")
     * 
     * @param User $user
     * @param Role $role
     * @param Request $request
     * @return Response
     */
    public function edit(User $user, Request $request)
    {
        $form = $this->createForm(AdminUserType::class, $user);

        $form->handleRequest($request, []);

        $manager = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($user->getUserRoles() as $role) {
                $role->setUsers($user->getRoles());
                $manager->persist($role);
            }

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'utilisateur: {$user->getFullName()} a bien été modifié !"
            );

            return $this->redirectToRoute('admin_users_index', [
                'slug' => $user->getSlug()
            ]);
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Suppression d'un utilisateur
     * 
     * @Route("/admin/users/{slug}/delete" , name="admin_users_delete")
     *
     * @param User $user
     * @return Response
     */
    public function delete(User $user)
    {
        $manager = $this->getDoctrine()->getManager();

        $manager->remove($user);

        $manager->flush();

        $this->addFlash(
            'success',
            "L'utilisateur <strong>{$user->getFullName()}</strong> a bien été supprimée !"
        );

        return $this->redirectToRoute('admin_users_index', [
            'slug' => $user->getSlug()
        ]);
    }
}
