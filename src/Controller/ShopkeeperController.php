<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use App\Entity\User;
use App\Form\AuthorType;
use App\Form\BookType;
use App\Form\GenreType;
use App\Form\UserShopkeeperType;
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
    #[Route('shopkeeper/user/manage/{id}', name: 'web_user_manage')]
    public function manageUser(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, $id = -1): Response
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
        $form = $this->createForm(UserShopkeeperType::class, $user);
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
            'form' => $form->createView()
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
        $book = $doctrine->getRepository("App\Entity\Book")->findOneBy(["id" => $id, "status" => 'Active']);
        $msg = "Book updated successfully";

        if (!$book) {
            $book = new Book();
            $msg = "Book created successfully";
            $query = $em->createQuery("SELECT u from App:Genre u where  u.status ='Active'");
            $result = $query->getResult();
            $update_book = "add";
            $select = "";
        } else {
            $select = $book->getGenre();
            $query = $em->createQuery("SELECT u from App:Genre u where  u.name NOT IN (:genre) and  u.status ='Active'");
            $query->setParameter('genre', $select);
            $result = $query->getResult();
            $update_book = "update";
        }
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($request->getMethod() == "POST") {
            if ($form->isSubmitted() and $form->isValid()) {

                $genres = $doctrine->getRepository("App\Entity\Genre")->findBy(['name' => $request->get("genre")]);
                foreach ($genres as $genre) {

                    $a[] = $genre->getName();
                }

                if ($update_book == "update") {
                    $a = $request->get('genre');
                }


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
                    $book->setGenre($a);
                    $em->persist($book);
                    $em->flush();
                    $this->addFlash('success', $msg);
                    return $this->redirectToRoute('shopkeeper_book_manage', ['id' => $id]);
                } elseif (!$upload) {
                    $book->setGenre($a);
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
            'value' => $result,
            'sel' =>  $select,

        ]);
    }

    #[Route('shopkeeper/book/list/', name: 'book_list')]
    public function bookList(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $book = $doctrine->getRepository("App\Entity\Book")->findAll();

        return $this->render('shopkeeper/list_book.html.twig', [
            'title' => "List Book",
            'books' => $book,
        ]);
    }

    #[Route('shopkeeper/manage/genre/{id}', name: 'manage_genre')]
    public function manageGenre(Request $request, ManagerRegistry $doctrine, $id = -1): Response
    {
        $em = $doctrine->getManager();
        $genre = $doctrine->getRepository("App\Entity\Genre")->findOneBy(["id" => $id]);
        $btn = "Update";
        $msg = "Genre updated successfully";
        if (!$genre) {
            $genre = new Genre();
            $msg = "Genre created successfully";
            $btn = "Add";
        }

        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);
        if ($request->getMethod() == "POST") {
            if ($form->isSubmitted() and $form->isValid()) {
                $em->persist($genre);
                $em->flush();
                $this->addFlash('success', $msg);
                return $this->redirectToRoute('manage_genre');
            }
        }

        return $this->render('shopkeeper/manage_genre.html.twig', [
            'form' => $form->createView(),
            'btn' => $btn
        ]);
    }

    #[Route('shopkeeper/genre/list/', name: 'genre_list')]
    public function genreList(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $genre = $doctrine->getRepository("App\Entity\Genre")->findAll();

        return $this->render('shopkeeper/list_genre.html.twig', [
            'title' => "List Genre",
            'genres' => $genre,
        ]);
    }

    #[Route('shopkeeper/manage/author/{id}', name: 'manage_author')]
    public function manageAuthor(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger, $id = -1): Response
    {
        $em = $doctrine->getManager();
        $author = $doctrine->getRepository("App\Entity\Author")->findOneBy(["id" => $id]);
        $btn = "Update Author";
        $msg = "Author updated successfully";
        if (!$author) {
            $author = new Author();
            $msg = "Author created successfully";
            $btn = "Add Author";
        }

        $form = $this->createForm(AuthorType::class, $author);
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
                            $this->getParameter('upload_directory') . "/authors/",
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $author->setFile($newFilename);
                    $em->persist($author);
                    $em->flush();
                    $this->addFlash('success', $msg);
                    return $this->redirectToRoute('manage_author');
                } elseif (!$upload) {
                   
                    $em->persist($author);
                    $em->flush();
                    $this->addFlash('success', "updated successfully");
                    return $this->redirect($this->generateUrl('manage_author', ['id' => $id]));
                }
            }
        }

        return $this->render('shopkeeper/manage_author.html.twig', [
            'form' => $form->createView(),
            'btn' => $btn
        ]);
    }

    #[Route('shopkeeper/author/list/', name: 'author_list')]
    public function authorList(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $author = $doctrine->getRepository("App\Entity\Author")->findAll();

        return $this->render('shopkeeper/list_author.html.twig', [
            'title' => "List author",
            'authors' => $author,
        ]);
    }

    #[Route('shopkeeper/order/list/', name: 'order_list')]
    public function orderList(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $order = $doctrine->getRepository("App\Entity\BookOrder")->findAll();

        return $this->render('shopkeeper/list_order.html.twig', [
            'title' => "List order",
            'orders' => $order,
        ]);
    }
    
}
