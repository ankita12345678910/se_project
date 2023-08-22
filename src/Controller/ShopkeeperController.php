<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Form\BookType;
use App\Form\UserType;
use Doctrine\ORM\Mapping\Id;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ShopkeeperController extends AbstractController
{
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
        return $this->render('shopkeeper/sign_up.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    #[Route('shopkeeper/user/manage/{id}', name: 'web_user_manage')]
    public function userManage(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, $id = -1): Response
    {

        $em = $doctrine->getManager();
        $user = $doctrine->getRepository("App\Entity\User")->findOneBy(["id" => $id]);
        $val = "edit user";
        $msg = "User updated successfully";

        if (!$user) {
            $user = new User();
            $msg = "User created successfully";
            $val = "new user";
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($request->getMethod() == "POST") {
            if ($form->isSubmitted() and $form->isValid()) {
                if ($val == "new user") {
                    $user->setPassword($passwordHasher->hashPassword($user, $request->get("pass")));
                }
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', $msg);
                return $this->redirectToRoute('web_user_manage', ['id' => $id]);
            }
        }
        return $this->render('shopkeeper/manage_user.html.twig', [
            'user' => $user,
            'form' => $form->createView(),

        ]);
    }
    #[Route('shopkeeper/user/list', name: 'user_list')]
    public function userList(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $user = $doctrine->getRepository("App\Entity\User")->findAll();

        return $this->render('shopkeeper/list_user.html.twig', [
            'title' => "List User",
            'users' => $user,
        ]);
    }

    #[Route('shopkeeper/book/manage/{id}', name: 'shopkeeper_book_manage')]
    public function manageBook(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger, $id = -1): Response
    {

        $em = $doctrine->getManager();
        $book = $doctrine->getRepository("App\Entity\Book")->findOneBy(["id" => $id]);
        $val = "edit Book";
        $msg = "Book updated successfully";

        if (!$book) {
            $book = new Book();
            $msg = "Book created successfully";
            $val = "new book";
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($request->getMethod() == "POST") {
            if ($form->isSubmitted() and $form->isValid()) {

                /** @var UploadedFile $upload */
                $upload = $form->get('file')->getData();

                if ($upload) {

                    /*original file name..if my file name is "my_first_notice"..then $originalfilename
                return the actual name of the file*/
                    $originalFilename = pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME);

                    //$filename returns the file name like that " my-first-notice " 
                    $Filename = $slugger->slug($originalFilename);

                    //$newFilename returns a unique file name with extension..like that" my-first-notice.pdf"
                    //guessExtension()is a method which returns the original extension of the file.
                    $newFilename = uniqid() . '_' . $Filename . '.' . $upload->guessExtension();

                    //move uploaded file....
                    try {
                        $upload->move(
                            $this->getParameter('upload_directory') . "/books/",
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $book->setFile($newFilename);
                    $em->persist($book);
                    $em->flush();
                    $this->addFlash('success', $msg);
                    return $this->redirectToRoute('shopkeeper_book_manage', ['id' => $id]);
                } elseif (!$upload) {
                    $em->persist($book);
                    $em->flush();
                    $this->addFlash('success', "updated successfully");
                    return $this->redirect($this->generateUrl('shopkeeper_book_manage', ['id' => $id]));
                }
            } else {
                $this->addFlash('error', "something went wrong");
                return $this->redirectToRoute('shopkeeper_book_manage', ['id' => $id]);
            }
        }
        return $this->render('shopkeeper/manage_book.html.twig', [
            'book' => $book,
            'form' => $form->createView(),

        ]);
    }
}
