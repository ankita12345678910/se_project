<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/redirect', name: 'web_redirect')]
    public function redirectPage(): Response
    {

        $url = 'home';
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            $url = 'web_admin_dashboard';
        } elseif ($this->isGranted('ROLE_SHOPKEEPER')) {
            $url = 'web_shopkeeper_dashboard';
        } elseif ($this->isGranted('ROLE_VENDOR')) {
            $url = 'web_vendor_dashboard';
        } elseif ($this->isGranted('ROLE_CUSTOMER')) {
            $url = 'web_customer_dashboard';
        } else{

        }
            //$url = 'web_lecturer_dashboard';


            return $this->redirect($this->generateUrl($url));
        
    }
}
