<?php

namespace App\Controller;

use App\Entity\BookOrder;
use App\Entity\OrderItem;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

        $em = $doctrine->getManager();
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

            $order_item = new OrderItem();
            $order_item->setBookOrder($order);
            $order_item->setBook($item->getBook());
            $order_item->setPrice($item->getBook()->getPrice());
            $order_item->setQuantity($item->getQuantity());
            $em->persist($order_item);
            $em->flush();
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


        return $this->redirectToRoute('app_home');
    }
}
