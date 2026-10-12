<?php

namespace NotificationBannerKit\Search\Column\Set;

use Concrete\Core\Search\Column\{Column, Set};

class Notifications extends Set
{
    public function __construct()
    {
        $this->addColumn(new Column(
            'n.id',
            t('ID'),
            'getID',
            true,
        ));

        $this->addColumn(new Column(
            '',
            t('Name'),
            'getName',
            false,
        ));

        $this->addColumn(new Column(
            'n.style',
            t('Style'),
            'getStyleString',
            false,
        ));

        $this->addColumn(new Column(
            '',
            t('Content'),
            'getContent',
            false,
        ));

        $this->addColumn(new Column(
            'n.date_updated',
            t('Date Updated'),
            'getDateUpdatedString',
            true,
        ));

        $defaultSortColumn = $this->getColumnByKey('n.date_updated');
        $this->setDefaultSortColumn($defaultSortColumn, 'desc');
    }
}
