<?php

namespace NotificationBannerKit\Search\ItemList;

use ClassKit\Search\ItemList\ListTrait;
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
        $adapter = new \Pagerfanta\Doctrine\DBAL\QueryAdapter(
            $query,
            function () {
                return $this->getTotalResults();
            },
        );
        return new Pagination($this, $adapter);
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
        return CustomItemList::getByID($queryRow['id']);
    }
}
