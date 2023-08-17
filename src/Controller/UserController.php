<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
