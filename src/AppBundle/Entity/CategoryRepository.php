<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{
    public function findAllOrdered()
    {
       // $dql = 'SELECT cat FROM AppBundle\Entity\Category cat ORDER BY cat.name DESC'; // The biggest difference between DQL and SQL is that instead of working with database tables and columns, you work with normal PHP classes and properties
       // $query = $this->getEntityManager()->createQuery($dql);

        // This query below, using the Doctrine query builder is the same as the query above.
        $qb = $this->createQueryBuilder('cat') // Creating a query builder object and passing 'cat' as the argument, this will be the alias to category
            ->addOrderBy('cat.name', 'DESC'); // Since we are in the CategoryRepository, the query builder knows to use category entity class, using 'cat' as the alias

        $this->addFortuneCookiesJoinAndSelect($qb);

        $query = $qb->getQuery();


        return $query->execute(); // This will return an array of category objects, doctrine normal mode is always to return objects and not an array of data.
    }

    public function search($term)
    {
        $qb = $this->createQueryBuilder('cat') // Using the cat alias so it knows to query the category table
            ->andWhere('cat.name LIKE :searchTerm 
             OR cat.iconKey LIKE :searchTerm
             OR fc.fortune LIKE :searchTerm') // Searching through the category table and its column name for a search term that is like the one that is being used on the website search bar. I am adding a bunch more searchTerms and using LIKE so that the search bar can search for more things.
            ->setParameter('searchTerm', '%'.$term.'%'); // the searchTerm is defined here, this is to avoid SQL injection. It is defined as the $term variable which will be whatever is put into the search bar on the website an we use that to check the database to see if there is anything like the term there

        $this->addFortuneCookiesJoinAndSelect($qb);

            return $qb
            ->getQuery()
            ->execute();
    }

    public function findWithFortunesJoin($id)
    {
        $qb = $this->createQueryBuilder('cat')
            ->andWhere('cat.id = :id')
            ->setParameter('id', $id);

            $this->addFortuneCookiesJoinAndSelect($qb);

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    private function addFortuneCookiesJoinAndSelect(QueryBuilder $qb) // This function allows us to remove duplicated code by add the leftJoin and addSelect functions here, so in our queries we can just call this method
    {
       return $qb->leftJoin('cat.fortuneCookies', 'fc') // This join only works because we have a $fortuneCookies property that is annotated with the OneToMany relationship with the category class
            ->addSelect('fc'); // Adding this means that it selects data from both tables.
                                    // Even though we are selecting more data, this function still returns the same amount of data as it did before, an array of category objects.
                                    // Here, addSelect tells Doctrine to fetch the data that is in the fortune cookie table, but store it internally
    }
}
