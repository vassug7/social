<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping\PostRemove;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LikeController extends AbstractController
{
    
    public function __construct(private PostRepository $posts){}
    #[Route('/like/{post}', name: 'app_like')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function like(Post $post,Request $request):Response

    {
        $currentUser=$this->getUser();
        $post->addLikedby($currentUser);
        $this->posts->add($post);
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/unlike/{post}', name: 'app_unlike')]
    public function unlike(Post $post,Request $request)
    {
        $currentUser=$this->getUser();
        $post->removeLikedby($currentUser);
        $this->posts->add($post);
        return $this->redirect($request->headers->get('referer'));
    }
}
