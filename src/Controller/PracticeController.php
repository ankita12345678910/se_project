<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PracticeController extends AbstractController
{
    #[Route('/practice', name: 'app_practice')]
    public function index(): Response
    {
        return $this->render('practice/index.html.twig', [
            'controller_name' => 'PracticeController',
        ]);
    }
    #[Route('/test/code', name: 'app_test_code')]
    public function test(): Response
    {
        return $this->render('practice/test.html.twig', [
            'controller_name' => 'PracticeController',
        ]);
    }

    #[Route('/image/upload', name: 'app_test_code')]
    public function imageUpload(): Response
    {
        return $this->render('practice/image_upload.html.twig', [
            'controller_name' => 'PracticeController',
        ]);
    }
    #[Route('/fetch', name: 'app_test_xyz')]
    public function xyz(): JsonResponse
    {
        header('Access-Control-Allow-Origin: http://localhost');
        header("Access-Control-Allow-Headers: *");
        header("Access-Control-Allow-Methods: *");
        header("Allow: *");
        $response = new JsonResponse();
        $html = $this->renderView('practice/fetch.html.twig', []);
        $response->setData($html);
        return $response;
    }
    #[Route('/profile/xyz', name: 'ajax_profile')]
    public function uploadProfilePic(Request $request, SluggerInterface $slugger): JsonResponse
    {
        $response = new JsonResponse();
        /** @var UploadedFile $upload **/
        $upload = $request->files->get('mango');
        foreach ($upload as $file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $NewFilename = $slugger->slug($originalFilename);
            $fileName = $NewFilename . uniqid() . '-' . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('upload_directory') . "/pictures/",
                $fileName
            );
        }

        $html = $this->renderView('practice/upload_new_profile_picture.html.twig', [
            'picture' => '3',
        ]);
        $response->setData($html);
        return $response;
    }
}
