<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($pseudo, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    public function changeActiveStatus(User $user)
    {
        $this->createQueryBuilder('u')
            ->update('App:User', 'u')
            ->set('u.active', ':status')
            ->andWhere('u.pseudo = :p')
            ->setParameter('p', $user->getPseudo())
            ->setParameter('status', !$user->getActive())
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneBySomeFiled($value)
    {
        return $this->createQueryBuilder('u')
            ->setParameter('value', $value)
            ->select('u')
            ->from('App:User', 'u')
            ->orWhere('u.pseudo = :value')
            ->orWhere('u.active = :value')
            ->orWhere('u.mail = :value')
            ->orWhere('u.point = :value')
            ->orWhere('JSON_VALUE( u.roles, :value )')
            ->getQuery()
            ->getResult();
    }

    public function getAll()
    {
        return$this->createQueryBuilder('u')
            ->select('u.pseudo, u.mail')
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
