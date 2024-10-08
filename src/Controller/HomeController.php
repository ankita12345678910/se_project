<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $books = $doctrine->getRepository("App\Entity\Book")->findBy(['language'=>'English']);
        $b = $doctrine->getRepository("App\Entity\Book")->findBy(['language'=>'Bengali']);
        $author = $doctrine->getRepository("App\Entity\Author")->findAll();
        foreach ($books as $book) {
            foreach ($book as $boo) {

                $name[] = $boo->getGenre();
                
            }
            
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'book' => $books,
            'mango'=>$b,
            'authors'=>$author
        ]);
    }
    #[Route(path: 'image/{path}/{file}', name: 'image_show')]
    public function image($path, $file): Response
    {
        // .. add some security checks here
        $response = new Response();
        $filename = $this->getParameter('upload_directory') . "/" . $path . "/" . $file;
        if (file_exists($filename)) {
            // Set headers
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', mime_content_type($filename));

            // Send headers before outputting anything
            $response->sendHeaders();
            $response->setContent(file_get_contents($filename));
        }
        return $response;
    }
}
