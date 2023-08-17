<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\Mapping\Id;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ShopkeeperController extends AbstractController
{
    #[Route('/manage/user', name: 'manage_user')]
    public function manageUser(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $em = $doctrine->getManager();
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($request->getMethod() == "POST") {

            if ($form->isSubmitted() and $form->isValid()) {

                $user->setPassword($passwordHasher->hashPassword($user, $request->get("pass")));
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'User created successfully');
                return $this->redirectToRoute('manage_user');
            }
        }
        return $this->render('shopkeeper/user_manage.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    #[Route('shopkeeper/user/manage/{id}', name: 'web_user_manage')]
    public function userManage(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, $id = -1): Response
    {

        $em = $doctrine->getManager();
        $user = $doctrine->getRepository("App\Entity\User")->findOneBy(["id" => $id]);

        $val = "edit user";

        $msg = "User updated successfully";
        if (!$user) {
            $user = new User();
            $msg = "User created successfully";
            $val = "new user";
        } else {
            $name = $user->getId();
            $query = $em->createQuery("SELECT u.id from App:User u where u.id = :dId");
            $query->setParameter('dId', $name);
            $path_id = $query->getResult();
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($request->getMethod() == "POST") {
            if ($form->isSubmitted() and $form->isValid()) {
                if ($val == "new user") {
                    $user->setPassword($passwordHasher->hashPassword($user, $request->get("pass")));
                }
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', $msg);
                return $this->redirectToRoute('web_user_manage', ['id' => $id]);
            }
        }
        return $this->render('shopkeeper/manage_user.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'path_id' => $path_id
        ]);
    }
    #[Route('shopkeeper/user/list', name: 'user_list')]
    public function userList(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $user = $doctrine->getRepository("App\Entity\User")->findAll();

        return $this->render('shopkeeper/list_user.html.twig', [
            'title' => "List User",
            'users' => $user,
        ]);
    }
}
