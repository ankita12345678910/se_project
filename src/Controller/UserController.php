<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\ShippingAddress;
use App\Entity\User;
use App\Form\ShippingAddressType;
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $em = $doctrine->getManager();
        $cart = $doctrine->getRepository("App\Entity\Cart")->findOneBy(["user" => $this->getUser()]);
        $book = $doctrine->getRepository("App\Entity\Book")->findOneBy(["id" => $id]);
        $cart_item = $doctrine->getRepository("App\Entity\CartItem")->findOneBy(["cart" => $cart, 'book' => $book]);

        if (!$cart_item) {
            $cart_item = new CartItem();
            $cart_item->setCart($cart);
            $cart_item->setBook($book);
            $cart_item->setQuantity('1');
        } else {
            $cart_item->setQuantity($cart_item->getQuantity() + 1);
        }
        $em->persist($cart_item);
        $em->flush();
        return $this->redirect($this->generateUrl('web_book_details', ['id' => $id]));
    }

    #[Route('/view/cart/items', name: 'view_cart')]
    public function viewCart(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $cart = $doctrine->getRepository("App\Entity\Cart")->findOneBy(['user' => $this->getUser()]);
        $cart_item = $doctrine->getRepository("App\Entity\CartItem")->findBy(['cart' => $cart]);
        $repository = $em->getRepository('App\Entity\Book');
        $books = $repository->createQueryBuilder('c')
            ->innerJoin('c.cartItem', 'b')
            ->where('b.id IN (:category_id)')
            ->setParameter('category_id', $cart_item)
            ->getQuery()->getResult();
        if (!$books) {
            $cart_present = "no";
        } else {
            $cart_present = "yes";
        }

        return $this->render('user/view_cart_items.html.twig', [
            'existCart' => $cart_present,
        ]);
    }
    #[Route('/remove/item/{id}', name: 'remove_cart_item')]
    public function RemoveItem(ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $cart = $doctrine->getRepository("App\Entity\Cart")->findOneBy(['user' => $this->getUser()]);
        $book = $doctrine->getRepository("App\Entity\Book")->findOneBy(['id' => $id]);
        $a = $book->getId();
        $cart_item = $doctrine->getRepository("App\Entity\CartItem")->findOneBy(['cart' => $cart, 'book' => $a]);
        $em->remove($cart_item);
        $em->flush();
        return $this->redirect($this->generateUrl('view_cart'));
    }
    // #[Route('/update/quantity/{id}', name: 'quantity_update')]
    // public function updateQuantity(ManagerRegistry $doctrine, $id): Response
    // {
    //     $em = $doctrine->getManager();
    //     $cart = $doctrine->getRepository("App\Entity\Cart")->findOneBy(['user' => $this->getUser()]);
    //     $cart_item = $doctrine->getRepository("App\Entity\CartItem")->findOneBy(['cart' => $cart]);
    //     // dd($cart_item);
    //     $book = $doctrine->getRepository("App\Entity\Book")->findOneBy(['id' => $id]);
    //     $a = $book->getId();
    //     $bc = $doctrine->getRepository("App\Entity\CartItem")->findOneBy(['book' => $a]);
    //     $cart_item->setQuantity();  
    //     $em->remove($bc);
    //     $em->flush();
    //     return $this->redirect($this->generateUrl('view_cart'));
    // }

    #[Route('/ajax/quantity', name: 'web_ajax_book_quantity')]
    public function updateQuantity(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $em = $doctrine->getManager();
        $cart = $doctrine->getRepository("App\Entity\Cart")->findOneBy(['user' => $this->getUser()]);
        $book = $doctrine->getRepository("App\Entity\Book")->findOneBy(['id' => $request->get('id')]);
        $a = $book->getId();
        $cart_item = $doctrine->getRepository("App\Entity\CartItem")->findOneBy(['cart' => $cart, 'book' => $a]);
        $quantity = $request->get('a');
        $cart_item->setQuantity($quantity);
        $em->persist($cart_item);
        $em->flush();
        $html = $this->renderView('user/ajax_quantity.html.twig', [
            'title' => "View User",
            'quantity' => $quantity

        ]);
        $response = new JsonResponse();
        $response->setData($html);
        return $response;
    }

    #[Route('/add/shipping/address/{id}', name: 'add_shipping_address')]
    public function addShippingAddress(Request $request, ManagerRegistry $doctrine, $id = -1): Response
    {
        $em = $doctrine->getManager();
        if ($id > 0) {
            $address_id = $id;
        } else {
            $address_id = -1;
        }

        $shippingAddress = new ShippingAddress();
        $msg = "User created successfully";


        $form = $this->createForm(ShippingAddressType::class, $shippingAddress);
        $form->handleRequest($request);
        if ($request->getMethod() == "POST") {
            if ($form->isSubmitted() and $form->isValid()) {

                $em->persist($shippingAddress);
                $em->flush();
                $this->addFlash('success', $msg);
                return $this->redirectToRoute('add_shipping_address');
            }
        }
        return $this->render('user/add_shipping_address.html.twig', [
            'user' => $shippingAddress,
            'form' => $form->createView(),
            'address_id' => $address_id
        ]);
    }

    #[Route('/ajax/add/shipping/address', name: 'ajax_add_shipping_address')]
    public function addAddress(Request $request, ManagerRegistry $mr): JsonResponse
    {
        $em = $mr->getManager();
        $address = $mr->getRepository("App\Entity\ShippingAddress")->findOneBy(["id" => $request->get('id')]);
        if (!$address) {
            $address = new ShippingAddress();
            $address_id='-1';
           
        }
   
        $form = $this->createForm(ShippingAddressType::class, $address);
        $form->handleRequest($request);

        if ($request->getMethod() == "POST") {
            if ($form->isSubmitted() and $form->isValid()) {
                $address_id=$request->get('shipping');
                $address->setUser($this->getUser());
                $em->persist($address);
                $em->flush();
            }

            $html = $this->renderView('user/view_address.html.twig', [
                'title' => "Add Address",
                'form' => $form->createView(),
                // 'id' => $request->get('id'),
                'address_id' => $address_id

            ]);
        }
        $response = new JsonResponse();
        $response->setData($html);
        return $response;
    }
    #[Route('/ajax/get/address/form', name: 'ajax_get_address_form')]
    public function getUserForm(Request $request, ManagerRegistry $mr): JsonResponse
    {

        $address_id = $request->get('shipping');
        $response = new JsonResponse();
        $id = $request->get('id');
        $address = $mr->getRepository('App\Entity\ShippingAddress')->findOneBy(["id" => $id]);
        $form = $this->createForm(ShippingAddressType::class, $address);

        $html = $this->renderView('user/get_address_form.html.twig', [
            'title' => "Edit User",
            'form' => $form->createView(),
            'id' => $id,
            'shipping' => $address_id
        ]);
        $response->setData($html);
        return $response;
    }

    #[Route('/ajax/delete/address/', name: 'ajax_delete_shipping_address')]
    public function deleteAddress(Request $request, ManagerRegistry $mr): JsonResponse
    {
        $em = $mr->getManager();
        $response = new JsonResponse();
        $id = $request->get('id');
        $address_id = $request->get('abc');
        $address = $mr->getRepository('App\Entity\ShippingAddress')->findOneBy(["id" => $id]);
        $form = $this->createForm(ShippingAddressType::class, $address);
        $address->setStatus("Deleted");
        $em->persist($address);
        $em->flush();
        $html = $this->renderView('user/view_address.html.twig', [
            'form' => $form->createView(),
            'address_id' => $address_id
        ]);
        $response->setData($html);
        return $response;
    }

    #[Route('make/payment/{id}', name: 'make_payment')]
    public function bookPayment(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $address = $doctrine->getRepository("App\Entity\ShippingAddress")->findOneBy(["id" => $id]);
        return $this->render('user/make_payment.html.twig', [
            'address' => $address

        ]);
    }
}
