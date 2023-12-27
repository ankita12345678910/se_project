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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
        $msg = '0';
        $icon = '0';
        if ($request->getMethod() == "POST") {

            if ($form->isSubmitted() and $form->isValid()) {
                $user->setPassword($passwordHasher->hashPassword($user, $request->get("pass")));
                $em->persist($user);
                $em->flush();
                $msg = "you have registered successfully";
                $icon = "success";
            }
        }
        return $this->render('user/sign_up.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'msg' => $msg,
            'cv' => $icon
        ]);
    }

    #[Route('/user/verify/email/', name: 'verify_email')]
    public function verifyEmail(Request $request, ManagerRegistry $doctrine): Response
    {
        $error = 'false';
        if ($request->getMethod() == "POST") {

            $email = $request->get('email_id');
            $user = $doctrine->getRepository("App\Entity\User")->findOneBy(["email" => $email]);
            if ($user) {
                return $this->redirect($this->generateUrl('reset_password', ['id' => $user->getId()]));
            } else {
                $error = true;
            }
        }
        return $this->render('user/verify_email.html.twig', [
            'controller_name' => 'UserController',
            'error' => $error
        ]);
    }
    #[Route('/user/reset/password/{id}', name: 'reset_password')]
    public function resetPassword(Request $request, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine, $id = -1): Response
    {
        $em = $doctrine->getManager();
        $user = $doctrine->getRepository("App\Entity\User")->findOneBy(["id" => $id]);
        $error = 'false';
        $is_changed = 'no';

        if ($request->getMethod() == "POST") {
            if ($user->getId()) {
                $new = $request->get('new_password');
                $con = $request->get('con_password');
                if ($new == $con) {
                    $user->setPassword($passwordHasher->hashPassword($user, $request->get('con_password')));
                    $em->persist($user);
                    $em->flush();
                    $is_changed = 'yes';
                } else {
                    $error = 'true';
                }
            }
            // $email = $request->get('email_id');
            // if ($user) {
            //     return $this->redirect($this->generateUrl('reset_password', ['id' => $user->getId()]));
            // } else {
            //     $error=true;
            // }

        }
        return $this->render('user/reset_password.html.twig', [
            'controller_name' => 'UserController',
            'error' => $error,
            'is_changed' => $is_changed
        ]);
    }

    #[Route('/book/details/{id}/{item}', name: 'web_book_details')]
    public function bookDetails(Request $request, ManagerRegistry $doctrine, $id, $item = 'no'): Response
    {
        $cart = $doctrine->getRepository("App\Entity\Cart")->findOneBy(["user" => $this->getUser()]);
        $book = $doctrine->getRepository("App\Entity\Book")->findOneBy(["id" => $id]);
        $cart_item = $doctrine->getRepository("App\Entity\CartItem")->findOneBy(["cart" => $cart, 'book' => $book]);
        if (!$cart_item) {
            $cart_present = '0';
        } elseif ($cart_item->getStatus() != "Active") {
            $cart_present = '0';
        } else {
            $cart_present = '1';
            $item = 'yes';
        }
        return $this->render('user/book_details.html.twig', [
            'controller_name' => 'UserController',
            'book' => $book,
            'item_present' => $item,
            'cart_item' => $cart_present
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
            $em->persist($cart_item);
            // dd($cart_item);
            $em->flush();
            $item_present = 'yes';
        } elseif ($cart_item->getStatus() != "Active") {
            $cart_item->setStatus('Active');
            $cart_item->setQuantity('1');
            $em->persist($cart_item);
            $em->flush();
            $item_present = 'yes';
        } else {
            $item_present = 'yes';
        }


        return $this->redirect($this->generateUrl('web_book_details', ['id' => $id, 'item' => $item_present]));
    }

    #[Route('/view/cart/items/{shipping}', name: 'view_cart')]
    public function viewCart(Request $request, ManagerRegistry $doctrine, $shipping = -1): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($shipping > 0) {
            $address = $shipping;
        } else {
            $address = '-1';
        }
        $count = 0;
        foreach ($this->getUser()->getCart()->getCartItem() as $item) {

            if ($item->getStatus() == 'Active') {
                $count = $count + 1;
            }
        }
        if ($count == 0) {
            $cart_present = "no";
        } else {
            $cart_present = "yes";
        }

        return $this->render('user/view_cart_items.html.twig', [
            'existCart' => $cart_present,
            'address' => $address
        ]);
    }
    #[Route('/remove/item/{id}/{shipping}', name: 'remove_cart_item')]
    public function RemoveItem(ManagerRegistry $doctrine, $id, $shipping = -1): Response
    {
        if ($shipping > 0) {
            $address = $shipping;
        } else {
            $address = '-1';
        }
        $em = $doctrine->getManager();
        $cart = $doctrine->getRepository("App\Entity\Cart")->findOneBy(['user' => $this->getUser()]);
        $book = $doctrine->getRepository("App\Entity\Book")->findOneBy(['id' => $id]);
        $a = $book->getId();
        $cart_item = $doctrine->getRepository("App\Entity\CartItem")->findOneBy(['cart' => $cart, 'book' => $a]);
        $cart_item->setStatus("Deleted");
        $em->persist($cart_item);
        $em->flush();
        return $this->redirect($this->generateUrl('view_cart', ['shipping' => $address]));
    }

    #[Route('/ajax/quantity', name: 'web_ajax_book_quantity')]
    public function updateQuantity(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $em = $doctrine->getManager();
        $cart = $doctrine->getRepository("App\Entity\Cart")->findOneBy(['user' => $this->getUser()]);
        $book = $doctrine->getRepository("App\Entity\Book")->findOneBy(['id' => $request->get('book_id')]);
        $bk = $request->get('book_id');
        $price = $book->getPrice();
        $a = $book->getId();
        $cart_item = $doctrine->getRepository("App\Entity\CartItem")->findOneBy(['cart' => $cart, 'book' => $a]);
        $quantity = $request->get('value');
        $address = $request->get('address');
        $cart_item->setQuantity($quantity);
        $q = $cart_item->getQuantity();
        $em->persist($cart_item);
        $em->flush();

        $html = $this->renderView('user/ajax_quantity.html.twig', [
            'quantity' => $q,
            'address' => $address,
            'price' => $price,
            'bk' => $bk,

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
        $address = $mr->getRepository("App\Entity\ShippingAddress")->findOneBy(["id" => $request->get('uid')]);
        if (!$address) {
            $address = new ShippingAddress();
            $address_id = '-1';
        }

        $form = $this->createForm(ShippingAddressType::class, $address);
        $form->handleRequest($request);

        if ($request->getMethod() == "POST") {
            if ($form->isSubmitted() and $form->isValid()) {
                $address_id = $request->get('shipping');
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
        $id = $request->get('uid');
        $address = $mr->getRepository('App\Entity\ShippingAddress')->findOneBy(["id" => $id]);
        $form = $this->createForm(ShippingAddressType::class, $address);

        $html = $this->renderView('user/get_address_form.html.twig', [
            'title' => "Edit User",
            'form' => $form->createView(),
            'uid' => $id,
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
        $response_code = http_response_code();
        $response->setData($response_code);
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

    #[Route('fetch/pin/code', name: 'fetch_pin_code')]
    public function pinCode(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $response = new JsonResponse();
        $abc = $request->get('pin');
        $data = file_get_contents('http://www.postalpincode.in/api/pincode/' . $abc);
        $data = json_decode($data);
        dump($data);
        if (isset($data->PostOffice['0'])) {
            $a = [
                $arr['city'] = $data->PostOffice['0']->Taluk,
                $arr['state'] = $data->PostOffice['0']->State
            ];


            $print = json_encode($a);
        } else {
            $print = 'no';
        }
        $html = $this->renderView('user/fetch_pin_code.html.twig', [
            'print' => $print

        ]);
        $response->setData($html);
        return $response;
    }
    #[Route('profile/pic/upload', name: 'ajax_profile_pic_upload')]
    public function uploadProfilePic(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): JsonResponse
    {
        $response = new JsonResponse();
        $em = $doctrine->getManager();
        $user = $doctrine->getRepository("App\Entity\User")->findOneBy(['id' => $this->getUser()]);
        $user_profile = $user->getProfile();

        //another way without using sluggerInterface

        // $file = $request->files->get('pro');
        // // If a file was uploaded
        // if (!is_null($file)) {

        //     $newFilename=$file->getClientOriginalName();
        //     $filename =$user->getEmail().'-'.uniqid().'-'.$newFilename."." . $file->getClientOriginalExtension();
        //     $path = $this->getParameter('upload_directory') . "/profiles/";
        //     $file->move($path, $filename); // move the file to a path
        //     //    $status = array('status' => "success","fileUploaded" => true);
        //     $user->setProfile($filename);
        //     $em->persist($user);
        //     $em->flush();
        // }


        //using sluggerInterface

        /** @var UploadedFile $upload */
        $upload = $request->files->get('profile_pic');

        if ($upload) {
            // if($user_profile!=''){
            //     // explode('-',$user_profile);
            //     $fileSystem = new Filesystem();
            //     $fileSystem->remove($this->getParameter('upload_directory') . "/profiles/",
            //     $user_profile);
            // }
            $originalFilename = pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME);
            $Filename = $slugger->slug($originalFilename);
            $newFilename = $user->getEmail() . '-' . uniqid() . '_' . $Filename . '.' . $upload->guessExtension();
            try {
                $upload->move(
                    $this->getParameter('upload_directory') . "/profiles/",
                    $newFilename
                );
            } catch (FileException $e) {
            }
            $user->setProfile($newFilename);
            $em->persist($user);
            $em->flush();
        }

        $html = $this->renderView('user/upload_new_profile_picture.html.twig', [
            'picture' => $newFilename,
            'user' => $user
        ]);
        $response->setData($html);
        return $response;
    }
    #[Route('/my/account', name: 'my_account')]
    public function myAccount(ManagerRegistry $doctrine): Response
    {
        $mr = $doctrine->getManager();
        $user = $doctrine->getRepository("App\Entity\User")->findOneBy(['id' => $this->getUser()]);
        return $this->render('user/my_account.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user
        ]);
    }
}
