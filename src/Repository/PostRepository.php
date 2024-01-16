<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }
    public function add(Post $entity)
    {
        $manager=$this->getEntityManager();
        $manager->persist($entity);
        $manager->flush();


    } 
    public function remove(Post $entity): void
    {
        $manager=$this->getEntityManager();
        $manager->remove($entity);
        $manager->flush();
    }
    public function findAllData(): array
    {
        return $this->findAllQuery(withComments:true,withLikes:true,withProfile:true)
                    ->getQuery()
                    ->getResult();
        
    }
    public function findByAuthor(int | User $author): array
    {
        return $this->findAllQuery(withComments:true,withLikes:true,withProfile:true)
                    ->where('p.author = :author')
                    ->setParameter('author',$author)
                    ->getQuery()
                    ->getResult();
        
    }
    public function findByID(int $id): array
    {
        return $this->findAllQuery(withComments:true,withLikes:true,withProfile:true)
                    ->leftJoin('c.author','u')
                    ->addSelect('u')
                    ->leftJoin('u.userProfile','Up')
                    ->addSelect('Up')
                    ->where('p.id = :id')
                    ->setParameter('id',$id)
                    ->getQuery()
                    ->getResult();
        
    }
    public function findByComments(int $id): array
    {
        return $this->findAllQuery(withComments:true)
                    ->leftJoin('c.author','a')
                    ->addSelect('a')
                    ->leftJoin('a.userProfile','up')
                    ->addSelect('up')
                    ->where('p.id = :id')
                    ->setParameter('id',$id)
                    ->getQuery()
                    ->getResult();
        
    }
    private function findAllQuery(
        bool $withComments=false,
        bool $withLikes=false,
        bool $withProfile=false,
        bool $withAuthor=false
    ):QueryBuilder
        {
            $query=$this->createQueryBuilder('p');
            if($withComments)
            {
               $query->leftJoin('p.comments','c')
                     ->addSelect('c');
            }
            if($withLikes)
            {
               $query->leftJoin('p.likedby','l')
                     ->addSelect('l');
            }
            if($withAuthor || $withProfile)
            {
               $query->leftJoin('p.author','a')
                     ->addSelect('a');
            }
            if($withProfile)
            {
               $query->leftJoin('a.userProfile','up')
                     ->addSelect('up');
            }
            return $query->orderBy('p.created', 'DESC');

        }


}
