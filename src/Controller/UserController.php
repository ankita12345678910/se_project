<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
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
    public function bookDetails(Request $request, ManagerRegistry $doctrine, $id): Response
    {

        $book = $doctrine->getRepository("App\Entity\Book")->findOneBy(["id" => $id]);

        return $this->render('user/book_details.html.twig', [
            'controller_name' => 'UserController',
            'book' => $book,
        ]);
    }
    #[Route('/ajax/book/details', name: 'web_ajax_book_details')]
    public function ajaxView(ManagerRegistry $mr, Request $request): JsonResponse
    {

        $book = $mr->getRepository("App\Entity\Book")->findOneBy(["id" => $request->get('id')]);
        $html = $this->renderView('user/ajaxView.html.twig', [
            'title' => "View User",
            'book' => $book,
        ]);
        $response = new JsonResponse();
        $response->setData($html);
        return $response;
    }

    #[Route('/book/cart/{id}', name: 'add_cart')]
    public function bookCart(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $cart = $doctrine->getRepository("App\Entity\Cart")->findOneBy(["user" => $this->getUser()]);
        $book = $doctrine->getRepository("App\Entity\Book")->findOneBy(["id" => $id]);
        if (!$cart) {
            // $cart = new Cart();
            // $cart_item=new CartItem();
            // $cart->setUser($this->getUser());

        }
        $cart_item=new CartItem();
        $cart_item->setCart($cart);
        $cart_item->setBook($book);
        $cart_item->setQuantity("1");
        $em->persist($cart_item);
        $em->flush();
        return $this->redirect($this->generateUrl('web_book_details', ['id' => $id]));
    }

    // #[Route('/view/cart/items', name: 'view_cart')]
    // public function viewCart(Request $request, ManagerRegistry $doctrine): Response
    // {
    //     $em = $doctrine->getManager();
    //     $cart = $doctrine->getRepository("App\Entity\Cart")->findOneBy(['user' => $this->getUser()]);
    //     $repository = $em->getRepository('App\Entity\Book');
    //     $books = $repository->createQueryBuilder('b')
    //         ->innerJoin('b.carts', 'c')
    //         ->where('c.id = :category_id')
    //         ->setParameter('category_id', $cart)
    //         ->getQuery()->getResult();

    //     if (!$books) {
    //         $cart_present = "no";
    //     } else {
    //         $cart_present = "yes";
    //     }

    //     return $this->render('user/view_cart_items.html.twig', [
    //         'existCart' => $cart_present,
    //         'books' => $books
    //     ]);
    // }
    // #[Route('/remove/item/{id}', name: 'remove_cart_item')]
    // public function RemoveItem(ManagerRegistry $doctrine, $id): Response
    // {
    //     $em = $doctrine->getManager();
    //     $cart = $doctrine->getRepository("App\Entity\Cart")->findOneBy(['user' => $this->getUser()]);
    //     $book = $doctrine->getRepository("App\Entity\Book")->findOneBy(['id' => $id]);
    //     $cart->removeBook($book);
    //     $em->persist($cart);
    //     $em->flush();
    //     return $this->redirect($this->generateUrl('view_cart'));
    // }
}
