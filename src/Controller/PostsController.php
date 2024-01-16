<?php

namespace App\Controller;

use DateTime;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\PostdataType;
use App\Entity\UserProfile;
use App\Form\AddCommentType;
use App\Security\Voter\PostVoter;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserProfileRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// #[IsGranted('IS_AUTHENTICATED_FULLY')]
class PostsController extends AbstractController
{

    // Render all Posts
    #[Route('/', name: 'app_posts')]
    public function index(PostRepository $posts): Response
    {
        return $this->render('posts/index.html.twig', 
        [
            
            'posts' => $posts->findAllData(),
        ]);
    }
    // Render Partiular Post 
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/post/{post}', name: 'app_post_show')]
    #[IsGranted(POST::VIEW, 'post')]
    public function showOne(Post $post,PostRepository $posts,Request $request, CommentRepository $comments): Response
    {
        $form =$this->createForm(AddCommentType::class,new Comment());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $comment = $form->getData();
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());
            $comments->add($comment, true);
            

            $this->addFlash('success', 'Your  Comment have been updated.');

            return $this->redirectToRoute(
                'app_post_show',
            ['post'=>$post->getId()]);
        }
        return $this->render('posts/show.html.twig', 
        [
            'form'  => $form->createView(),
            'posts' => $posts->findByID($post->getId()),
        ]);
    }
    // Add Post
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/add', name: 'app_add')]
    public function add(Request $request,PostRepository $posts)
    {
        $form =$this->createForm(PostDataType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $post = $form->getData();
            $postImage=$form->get('postImageFile')->getData();
            $post->setPostImageFile($postImage);  
            $post->setAuthor($this->getUser());
            $posts->add($post);                
            $this->addFlash('success', 'Your  post have been addded.');
            return $this->redirectToRoute('app_posts');
        }
        return $this->render('posts/addform.html.twig', 
        [
            'form' => $form->createView()
        ]);
    }
    // Edit Post
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/edit{post}', name: 'app_edit')]
    public function edit(Post $post,Request $request,PostRepository $posts)
    {
        if(!$this->isGranted(POST::EDIT, $post)) 
        {
            $this->addFlash('error', 'You are not allowed to edit this post.');
            return $this->redirectToRoute('app_posts');
        }
        $form =$this->createForm(PostDataType::class,$post );
        $form->handleRequest($request);
        $currentUser = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) 
        {   
            $post = $form->getData();
            $postImage=$form->get('postImageFile')->getData();
            $post->setPostImageFile($postImage);
            $posts->add($post, true);
            $this->addFlash('success', 'Your  post have been updated.');
            return $this->redirectToRoute('app_posts');
        }
        return $this->render('posts/edit.html.twig', 
        [
            'form' => $form->createView(),
            'post' => $post
        ]);
    }
    // Delete Post
    #[Route('/post/delete/{post}', name: 'app_post_delete')]
    public function deletePost(Post $post,ManagerRegistry $doctrine): Response
    {
    $user = $this->getUser();
    if ($user !== $post->getAuthor() ) {
        throw $this->createAccessDeniedException('You are not allowed to delete this post.');
    }

    $entityManager=$doctrine->getManager();
    $entityManager->remove($post);
    $entityManager->flush();

    $this->addFlash('success', 'Post deleted successfully.');

    return $this->redirectToRoute('app_posts');
   }
    // #[Route('/post/{post}/comment', name: 'app_post_comment')]
    // #[IsGranted('IS_AUTHENTICATED_FULLY')]
    // public function addComment(Post $post,Request $request,CommentRepository $comments)
    // {
    //     $form =$this->createForm(AddCommentType::class,new Comment());
    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $comment = $form->getData();
    //         $comment->setPost($post);
    //         $comment->setAuthor($this->getUser());
    //         $comments->add($comment, true);
            

    //         $this->addFlash('success', 'Your  Comment have been updated.');

    //         return $this->redirectToRoute(
    //             'app_post_show',
    //         ['post'=>$post->getId()]);
    //     }
    //     return $this->render('posts/addComment.html.twig', 
    //     [
    //         'form' => $form->createView(),
    //         'post' => $post
    //     ]);
    // }
   
}

