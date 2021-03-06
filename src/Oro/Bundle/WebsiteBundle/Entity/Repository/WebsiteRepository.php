<?php

namespace Oro\Bundle\WebsiteBundle\Entity\Repository;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;

use Oro\Bundle\EntityBundle\ORM\Repository\BatchIteratorInterface;
use Oro\Bundle\EntityBundle\ORM\Repository\BatchIteratorTrait;
use Oro\Component\PhpUtils\ArrayUtil;
use Oro\Bundle\WebsiteBundle\Entity\Website;

class WebsiteRepository extends EntityRepository implements BatchIteratorInterface
{
    use BatchIteratorTrait;

    /**
     * @return Website[]|Collection
     */
    public function getAllWebsites()
    {
        $websites = $this->createQueryBuilder('website')
            ->addOrderBy('website.id', 'ASC')
            ->getQuery()
            ->getResult();
        $result = [];
        foreach ($websites as $website) {
            $result[$website->getId()] = $website;
        }

        return $result;
    }

    /**
     * @return Website
     */
    public function getDefaultWebsite()
    {
        return $this->findOneBy(['default' => true]);
    }

    /**
     * @return array
     */
    public function getWebsiteIdentifiers()
    {
        $qb = $this->createQueryBuilder('website')
            ->select('website.id');

        return ArrayUtil::arrayColumn($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * @param int $websiteId
     * @return bool
     */
    public function checkWebsiteExists($websiteId)
    {
        $qb = $this->createQueryBuilder('website');

        $result = $qb->select('website.id')
            ->where($qb->expr()->eq('website.id', ':websiteId'))
            ->setParameter('websiteId', (int)$websiteId)
            ->getQuery()
            ->getArrayResult();

        return (bool)$result;
    }

    /**
     * @param int|null $id
     * @return Website[]
     */
    public function getByIdOrAll($id = null)
    {
        if ($id) {
            return ($website = $this->find($id)) ? [$website] : [];
        }

        return $this->findAll();
    }
}
