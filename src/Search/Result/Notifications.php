<?php

namespace NotificationBannerKit\Search\Result;

use ClassKit\Search\Result as SearchResult;

class Notifications extends SearchResult
{
    public function getItemDetails($result)
    {
        return new Item\Notifications($this, $this->listColumns, $result);
    }
}
