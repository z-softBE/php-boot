<?php

namespace App\Repository;

use App\Model\Entity\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PhpBoot\Data\Attribute\Repository;

#[Repository]
class ProductRepository extends EntityRepository
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata(Product::class));
    }

    public function getProductByName(string $name): Product|null
    {
        $dql = "SELECT p FROM App\Model\Entity\Product p WHERE p.name = ?1";

        return $this->getEntityManager()->createQuery($dql)
            ->setParameter(1, $name)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}