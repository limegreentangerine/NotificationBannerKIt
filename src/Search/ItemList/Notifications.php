<?php

namespace NotificationBannerKit\Search\ItemList;

use ClassKit\Search\ItemList\ListTrait;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\ItemList\Database\ItemList;
use NotificationBannerKit\Entity\Notification as CustomItemList;
use Concrete\Core\Application\{ApplicationAwareInterface, ApplicationAwareTrait};

class Notifications extends ItemList implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;
    use ListTrait;

    protected $autoSortColumns = [
        'n.name',
    ];

    protected function createPaginationObject()
    {
        $query = $this->deliverQueryObject();
        $prefetch = $this->prefetch(...);
        $adapter = new class($query, function (\Doctrine\DBAL\Query\QueryBuilder $query) {
            $query
                ->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct n.id)')
                ->setMaxResults(1);
        }, $prefetch) extends \Pagerfanta\Doctrine\DBAL\QueryAdapter {
            public function __construct($queryBuilder, $countQueryBuilderModifier, private \Closure $prefetch)
            {
                parent::__construct($queryBuilder, $countQueryBuilderModifier);
            }

            public function getSlice($offset, $length)
            {
                $rows = parent::getSlice($offset, $length);
                ($this->prefetch)($rows);

                return $rows;
            }
        };
        return new Pagination($this, $adapter);
    }

    /**
     * Load the notifications for these rows, with their contents, in a single query.
     * getResult() then finds them in the entity manager instead of querying once per row.
     */
    protected function prefetch(iterable $rows): void
    {
        $ids = array_column(is_array($rows) ? $rows : iterator_to_array($rows), 'id');

        if ($ids === []) {
            return;
        }

        app(EntityManagerInterface::class)
            ->createQuery('SELECT n, d FROM ' . CustomItemList::class . ' n LEFT JOIN n.data d WHERE n.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getResult();
    }

    public function executeGetResults()
    {
        $rows = parent::executeGetResults();
        $this->prefetch($rows);

        return $rows;
    }

    public function createQuery()
    {
        $this->query->select('n.id')
            ->from('nbNotifications', 'n')
        ;
    }

    public function filterByName($name)
    {
        $name = (string) $name;
        if ($name !== '') {
            $this->filterBy('n.name', $name, 'like');
        }
    }

    public function filterByStyle($style)
    {
        $style = (string) $style;
        if ($style !== '') {
            $this->filterBy('n.style', $style);
        }
    }

    public function excludeId($id)
    {
        $this->filterBy('n.id', (string) $id, '!=');
    }

    public function sortByDateUpdated($order = 'asc')
    {
        $this->query->orderBy('n.date_updated', $order);
    }

    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();
        $query
            ->resetQueryParts(['groupBy', 'orderBy'])
            ->select('count(distinct n.id)')
            ->setMaxResults(1);
        $result = $query->execute()->fetchOne();

        return (int) $result;
    }

    public function getResult($queryRow)
    {
        return app(EntityManagerInterface::class)->find(CustomItemList::class, $queryRow['id']);
    }
}
