<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping\PostRemove;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{
    
    public function __construct(private PostRepository $posts){}
    

    #[Route('/comment/{post}/{comment}', name: 'app_delete_comment')]
    public function deleteComment(Post $post,Comment $comment,Request $request)
    {
        $post->removeComment($comment);
        $this->posts->add($post);
        return $this->redirect($request->headers->get('referer'));
    }
}
