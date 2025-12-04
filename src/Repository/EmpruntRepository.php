<?php

namespace App\Repository;

use App\Entity\Emprunt;
use App\Entity\Livre;   // ✅ IMPORTANT : ajout du bon namespace
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emprunt>
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    /**
     * Trouve les emprunts qui arrivent à échéance dans les 2 jours et n'ont pas encore été notifiés
     *
     * @param \DateTime $dateLimite Date limite pour le retour
     * @return Emprunt[]
     */
    public function findEmpruntsARappeler(\DateTime $dateLimite): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.dateRetourPrevue <= :dateLimite')
            ->andWhere('e.statut = :statut')
            ->andWhere('e.dateNotificationRappel IS NULL')
            ->setParameter('dateLimite', $dateLimite)
            ->setParameter('statut', 'en_cours')
            ->getQuery()
            ->getResult();
    }

}
