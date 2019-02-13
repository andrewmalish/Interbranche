<?php


namespace Lucid\Booking\Api\Data;

interface CalendarSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Calendar list.
     * @return \Lucid\Booking\Api\Data\CalendarInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Lucid\Booking\Api\Data\CalendarInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
