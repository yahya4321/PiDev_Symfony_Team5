<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Service>
 *
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    public function save(Service $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Service $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }




    //using query builder
    public function rechercheAvance($str) {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT P
                FROM App\Entity\Service P
                WHERE P.servlib LIKE :str'
            )
            ->setParameter('str', '%'.$str.'%')
            ->getResult();

    }
    //METIER 3
    public function  findByTopServiceEvalues($val) {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder(); // dql
        $query->select('p.id ,p.note')
            ->from('App\Entity\Service','p')
            ->orderBy('p.note','DESC')
            ->setMaxResults(3);
        $res = $query->getQuery();
        return $res->execute();
    }

}