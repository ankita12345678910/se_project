<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
 
    #[Route('/shopkeeper/dashboard', name: 'web_shopkeeper_dashboard')]
    public function shopkeeperDashboard(): Response
    {
        return $this->render('shopkeeper/shopkeeper_dashboard.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/feedback', name: 'feedback')]
    public function feedback(): Response
    {
        return $this->render('user/feedback.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/vendor/dashboard', name: 'web_vendor_dashboard')]
    public function vendorDashboard(): Response
    {
        return $this->render('user/vendor_dashboard.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/customer/dashboard', name: 'web_customer_dashboard')]
    public function customerDashboard(): Response
    {
        return $this->render('user/customer_dashboard.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/sign/up', name: 'web_sign_up')]
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
                return $this->redirectToRoute('web_sign_up');
            }
        }
        return $this->render('user/sign_up.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/book/details/{id}', name: 'web_book_details')]
    public function bookDetails(Request $request, ManagerRegistry $doctrine,$id): Response
    {
        
        $book = $doctrine->getRepository("App\Entity\Book")->findOneBy(["id" => $id]);
        
        return $this->render('user/book_details.html.twig', [
            'controller_name' => 'UserController',
            'book'=>$book,
        ]);
        
    }
    #[Route('/ajax/book/details', name: 'web_ajax_book_details')]
    public function ajaxView(ManagerRegistry $mr,Request $request): JsonResponse
    {
        $book = $mr->getRepository("App\Entity\Book")->findOneBy(["id" => $request->get('id')]);
        $html= $this->renderView('user/ajaxView.html.twig', [
            'title' => "View User",
            'book' => $book,
        ]);
        $response = new JsonResponse();
        $response->setData($html);
        return $response;
    }
}
