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
        return $this->redirectToRoute('my_order_list');
    }

    #[Route('my/order/list/{page}', name: 'my_order_list')]
    public function myOrder(ManagerRegistry $doctrine, $page = 1): Response
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
            'total_pages' => $total_pages
        ]);
    }
    #[Route('/my/order/details/{order_no}', name: 'my_order_details')]
    public function detailsOrder(Request $request, ManagerRegistry $doctrine, $order_no): Response
    {
        $em = $doctrine->getManager();
        $order = $doctrine->getRepository("App\Entity\BookOrder")->findOneBy(['orderNo' => $order_no]);
        $address = $doctrine->getRepository("App\Entity\ShippingAddress")->findOneBy(['id' => $order->getAddress()]);
        $order_items = $doctrine->getRepository("App\Entity\OrderItem")->findBy(['bookOrder' => $order]);
        // dd($order_items);
        return $this->render('order/my_order_details.html.twig', [
            'title' => "My Order",
            'address'=> $address,
            'order_items'=> $order_items,
            'order'=> $order

        ]);
    }
}
