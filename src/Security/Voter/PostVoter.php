<?php

namespace App\Security\Voter;

use App\Entity\Post;
use PhpParser\Node\Stmt\Const_;
// use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PostVoter extends Voter
{
    public function __construct(private Security $security) {}


    protected function supports(string $attribute,  $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [POST::EDIT, POST::VIEW])
            && $subject instanceof Post;
    }
    /** @param Post $subject */
    protected function voteOnAttribute(string $attribute,  $subject, TokenInterface $token): bool
    {
        /** @var User $user */        
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        // if (!$user instanceof UserInterface) {
        //     return false;
        // }
        $isAuth= $user instanceof UserInterface;
        // ... (check conditions and return true to grant permission) ...
        if($this->security->isGranted('ROLE_ADMIN'))
        {
            return true;
        }
        switch ($attribute) 
        {
            case POST::VIEW:
                return true;
                
            case POST::EDIT:
                return $isAuth &&( 
                ($subject->getAuthor()->getId() == $user->getId())||
                $this->security->isGranted('ROLE_EDITOR'));
    
        }

        return false;
    }
}
