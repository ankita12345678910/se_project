<?php

namespace App\Controller;

use App\Entity\BookOrder;
use App\Entity\OrderItem;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once __DIR__ . '/../../vendor/autoload.php';

class BookOrderController extends AbstractController
{
    #[Route('/book/order', name: 'app_book_order')]
    public function index(): Response
    {
        return $this->render('book_order/index.html.twig', [
            'controller_name' => 'BookOrderController',
        ]);
    }

    #[Route('/make/order/{address}', name: 'make_order')]
    public function orderBook(Request $request, ManagerRegistry $doctrine, $address): Response
    {
        ob_start();
        $em = $doctrine->getManager();
        $mail = new PHPMailer(true);
        $shipping_address = $doctrine->getRepository("App\Entity\ShippingAddress")->findOneBy(['id' => $request->get('address')]);
        $order = $doctrine->getRepository("App\Entity\BookOrder")->findOneBy(['user' => $this->getUser()]);
        $total_price = 0;
        foreach ($this->getUser()->getCart()->getCartItem() as $item) {

            $a[] = $item->getQuantity();
            $price = $item->getBook()->getPrice();
            $total_price = $total_price + $price;
        }

        $order = new BookOrder();
        $order->setUser($this->getUser());
        $order->setAddress($shipping_address);
        $order->setUser($this->getUser());
        $order->setTotalPrice($total_price + 70);
        $order->setPaymentMode('COD');
        $em->persist($order);
        $em->flush();

        foreach ($this->getUser()->getCart()->getCartItem() as $item) {
            if ($item->getStatus() == "Active") {
                $order_item = new OrderItem();
                $order_item->setBookOrder($order);
                $order_item->setBook($item->getBook());
                $order_item->setPrice($item->getBook()->getPrice());
                $order_item->setQuantity($item->getQuantity());
                $em->persist($order_item);
                $em->flush();
            }
        }
        foreach ($this->getUser()->getCart()->getCartItem() as $item) {

            if ($item->getStatus() == 'Active') {
                $book = $doctrine->getRepository("App\Entity\Book")->findOneBy(['id' => $item->getBook()->getId()]);
                $book->setAvailableBook($book->getAvailableBook() - $item->getQuantity());
                $em->persist($book);
                $em->flush();
            }
        }

        foreach ($this->getUser()->getCart()->getCartItem() as $item) {

            if ($item->getStatus() == 'Active') {
                $cart_item = $doctrine->getRepository("App\Entity\CartItem")->findOneBy(['id' => $item->getId()]);
                $em->remove($cart_item);
                // $em->persist($order);
                $em->flush();
            }
        }

        try {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wormb022@gmail.com';
            $mail->Password = 'qepp ibvk hjli purj';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('wormb022@gmail.com', 'BookWorm');
            $mail->addAddress('baidyaankita927@gmail.com');     //Add a recipient
            $mail->addReplyTo('wormb022@gmail.com', 'BookWorm');
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Order confirmation';
            $mail->msgHTML("<h1>Dear " . $this->getUser()->getFirstname() . ",</h1><h1>Thank you for your order.</h1><h3>Your order number is- " . $order->getOrderNo() . "</h3><h3>We truly value our loyal customers. Thanks for making us who we are!</h3><h3>Estimated time of delivery within 4-5 working days</h3><h3>If you have any questions, concerns, or want to share your thoughts, email us</h3>");
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        return $this->redirectToRoute('my_order_list', ['abc' => 'yes']);
    }

    #[Route('my/order/list/{page}/{abc}', name: 'my_order_list')]
    public function myOrder(ManagerRegistry $doctrine, $page = 1, $abc = 'no'): Response
    {

        $user = $this->getUser();
        $em = $doctrine->getManager();
        $limit = 6;
        $pg = $page;
        $offset = ($pg - 1) * $limit;
        $query = $em->createQuery("SELECT u from App:BookOrder u where  u.user = :us order by u.createdAt DESC")->setFirstResult($offset)->setMaxResults($limit);
        $query->setParameter('us', $user);
        $result = $query->getResult();
        $order = $doctrine->getRepository("App\Entity\BookOrder")->findBy(['user' => $this->getUser()]);

        if (count($order) > 0) {
            $total_records = count($order);
            $total_pages = ceil($total_records / $limit);
        }

        return $this->render('order/my_order_list.html.twig', [
            'title' => "My Order",
            'orders' => $result,
            'page' => $pg,
            'limit' => $limit,
            'total_pages' => $total_pages,
            'abc' => $abc
        ]);
    }
    #[Route('/my/order/details/{order_no}/{page}', name: 'my_order_details')]
    public function detailsOrder(Request $request, ManagerRegistry $doctrine, $order_no, $page): Response
    {
        $em = $doctrine->getManager();
        $order = $doctrine->getRepository("App\Entity\BookOrder")->findOneBy(['orderNo' => $order_no]);
        $address = $doctrine->getRepository("App\Entity\ShippingAddress")->findOneBy(['id' => $order->getAddress()]);
        $order_items = $doctrine->getRepository("App\Entity\OrderItem")->findBy(['bookOrder' => $order]);
        // dd($order_items);
        return $this->render('order/my_order_details.html.twig', [
            'title' => "My Order",
            'address' => $address,
            'order_items' => $order_items,
            'order' => $order,
            'page' => $page
        ]);
    }
}
