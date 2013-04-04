<?php

namespace Iti\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository {
    
    /**
     * this function used to get category posts
     * @author ahmed
     * @param integer $id
     * @param integer $page
     * @param integer $itemsPerPage
     * @return type
     */
    public function getTagPosts($id, $page, $itemsPerPage) {
        if ($page < 1) {
            return array();
        }
        $page--;

        $query = $this->getEntityManager()->createQuery('
            SELECT p
            FROM ItiBlogBundle:Post p
            JOIN p.tags t
            where t.id = :id
            order by p.createdAt desc
            ')->setParameter('id', $id);

        if ($itemsPerPage) {
            $query->setFirstResult($page * $itemsPerPage);
            $query->setMaxResults($itemsPerPage);
        }
        return $query->getResult();
    }
    
    /**
     * this function used to count tag posts
     * @author ahmed
     * @param integer $id
     * @return type
     */
    public function countTagPosts($id) {
        $query = $this->getEntityManager()->createQuery('
            SELECT count(p.id) as postsCount
            FROM ItiBlogBundle:Post p
            JOIN p.tags t
            where t.id = :id
            ')->setParameter('id', $id);
        return $query->getResult();
    }

    /**
     * this function used to get category posts
     * @author ahmed
     * @param integer $id
     * @param integer $page
     * @param integer $itemsPerPage
     * @return type
     */
    public function getCategoryPosts($id, $page, $itemsPerPage) {
        if ($page < 1) {
            return array();
        }
        $page--;

        $query = $this->getEntityManager()->createQuery('
            SELECT p
            FROM ItiBlogBundle:Post p
            JOIN p.category c
            where c.id = :id
            order by p.createdAt desc
            ')->setParameter('id', $id);

        if ($itemsPerPage) {
            $query->setFirstResult($page * $itemsPerPage);
            $query->setMaxResults($itemsPerPage);
        }
        return $query->getResult();
    }
    
    /**
     * this function used to count category posts
     * @author ahmed
     * @param integer $id
     * @return type
     */
    public function countCategoryPosts($id) {
        $query = $this->getEntityManager()->createQuery('
            SELECT count(p.id) as postsCount
            FROM ItiBlogBundle:Post p
            JOIN p.category c
            where c.id = :id
            ')->setParameter('id', $id);
        return $query->getResult();
    }

    /**
     * this function used to get latest posts
     * @author ahmed
     * use BlogController:index
     * @param integer $limit
     */
    public function getLatestPosts($limit) {
        $query = $this->getEntityManager()->createQuery('
            SELECT p
            FROM ItiBlogBundle:Post p
            order by p.createdAt desc
            ')->setMaxResults($limit);
        return $query->getResult();
    }

}
