<?php

namespace App\Repository;

use App\Entity\Employment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Employment>
 *
 * @method Employment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employment[]    findAll()
 * @method Employment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmploymentRepository extends ServiceEntityRepository
{

	public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employment::class);
    }

    public function findByCompany($company)
    {
        $query = $this->createQueryBuilder('c')
            ->addSelect('p')
            ->leftJoin('c.person', 'p')
            ->andWhere('c.companyName like :val')
            ->setParameter('val', '%'.$company.'%')
            ->orderBy('c.companyName', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    public function findEmploymentsByDates($personId, $dateStart, $dateEnd)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.person', 'p')
            ->where('p.id = :personId')
            ->setParameter('personId', $personId);

        /*if(!empty($dateStart) && !empty($dateEnd)){
            $qb->andWhere('c.start BETWEEN :dateStart AND :dateEnd')
                ->setParameter('dateStart', $dateStart->format('Y-m-d'))
                ->setParameter('dateEnd', $dateEnd->format('Y-m-d'));
        }*/

        if(!empty($dateStart)){
            $qb->andWhere('c.start >= :dateStart')
                ->setParameter('dateStart', $dateStart->format('Y-m-d'));
        }

        if(!empty($dateEnd)){
            $qb->andWhere('c.end <= :dateEnd')
                ->setParameter('dateEnd', $dateEnd->format('Y-m-d'));
        }

        $query =
        $qb
        ->orderBy('c.companyName', 'ASC')
        ->getQuery();



        return $query->getResult();
    }

}
















