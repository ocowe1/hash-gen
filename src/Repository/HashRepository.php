<?php

namespace App\Repository;

use App\Entity\Hash;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Hash|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hash|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hash[]    findAll()
 * @method Hash[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HashRepository extends ServiceEntityRepository
{

    /**
     * @var Hash
     */
    protected Hash $hash;

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
        parent::__construct($registry, Hash::class);
    }

    /**
     * @param $params
     * @return void
     */
    public function insertHash($params)
    {
        $hash = new Hash();
        $hash->setBatch($params['batch']);
        $hash->setBlock($params['block']);
        $hash->setString($params['string']);
        $hash->setKey($params['key_string']);
        $hash->setHash($params['hash']);
        $hash->setAttempts($params['attempts']);

        $this->registry->getManager()->persist($hash);
        $this->registry->getManager()->flush();
    }

    /**
     * @return QueryBuilder
     */
    public function getAllList(): QueryBuilder
    {
        return $this->createQueryBuilder('h')->select('h.batch', 'h.string', 'h.block', 'h.attempts', 'h.key_string');
    }

}
