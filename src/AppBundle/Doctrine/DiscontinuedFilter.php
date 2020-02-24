<?php


namespace AppBundle\Doctrine;


use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class DiscontinuedFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias) // If this filter class is enabled, this method will be called on every query
    {
        if ($targetEntity->getReflectionClass()->name != 'AppBundle\Entity\FortuneCookie') { // If the class name does not equal FortuneCookie, then return an empty string because we do not want to apply this filter to it
            return ''; // It has to return an empty string otherwise Doctrine will add WHERE clauses which we don't want
        }

        return sprintf('%s.discontinued = %s', $targetTableAlias, $this->getParameter('discontinued')); // Returning what we want inside the WHERE clause
                                                                                                                        // %s is the table alias, it finds this because $targetTableAlias tells us what table is being used and its alias
    }

}