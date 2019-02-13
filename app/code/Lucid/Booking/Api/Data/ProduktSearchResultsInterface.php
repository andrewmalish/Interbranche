<?php


namespace Lucid\Booking\Api\Data;

interface ProduktSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Produkt list.
     * @return \Lucid\Booking\Api\Data\ProduktInterface[]
     */
    public function getItems();

    /**
     * Set Id list.
     * @param \Lucid\Booking\Api\Data\ProduktInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
