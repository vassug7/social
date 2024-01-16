<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserRepository $users,private UserPasswordHasherInterface $userPasswordHasher) {
        
    }
    public function load(ObjectManager $manager): void
    {
        $user1=new User();
        $user1->setEmail('user1@email.com');
        $roles=$user1->getRoles() ?? [];
        $roles[]='ROLE_ADMIN';
        $user1->setRoles($roles);
        $user1->setPassword(
           $this->userPasswordHasher->hashPassword($user1,'12345678')     
        );
        $manager->persist($user1);
        $manager->flush();
        
        $user=new User();
        $user->setEmail('user2@email.com');
        $user->setPassword(
           $this->userPasswordHasher->hashPassword($user,'12345678')     
        );
        $manager->persist($user);
        $manager->flush();
        
        $user2=new User();
        $user2->setEmail('user3@email.com');
        $user2->setPassword(
           $this->userPasswordHasher->hashPassword($user2,'12345678')     
        );
        $manager->persist($user2);
        $manager->flush();
        
        // $post=new Post();
        // $post->setText("Welcome To Poland");
        // $post->setTitle("Welcome To Germany");
        // $post->setCreated(new DateTime());
        // $post->setAuthor($user);
        // $manager->persist($post);
        // $manager->flush();
       
        // $post1=new Post();
        // $post1->setText("Welcome To US");
        // $post1->setTitle("Welcome To US");
        // $post1->setCreated(new DateTime());
        // $post1->setAuthor($user);
        // $manager->persist($post1);
        // $manager->flush();
        // // $userprofile=new UserProfile();
        // $userprofile->setName('ram');
        // $users->add();    
    }
}   
