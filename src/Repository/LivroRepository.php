<?php

namespace App\Repository;

use App\Entity\Livro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livro>
 */
class LivroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livro::class);
    }

    //    /**
    //     * @return Livro[] Returns an array of Livro objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Livro
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

     public function contarEValorTotal(): array
    {
        $resultado = $this->createQueryBuilder('l')
            ->select('COUNT(l.id) as total, COALESCE(SUM(l.valor), 0) as valorTotal')
            ->getQuery()
            ->getSingleResult();

        return [
            'total' => (int) $resultado['total'],
            'valorTotal' => (float) $resultado['valorTotal'],
        ];
    }

    public function findAllComAutoresEAssuntos(): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.autores', 'a')
            ->addSelect('a') //removendo o lazy loading visando otimizar
            ->leftJoin('l.assuntos', 's')
            ->addSelect('s')
            ->getQuery()
            ->getResult();
    }
}
