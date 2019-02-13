<?php


namespace Lucid\Booking\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ProduktRepositoryInterface
{


    /**
     * Save Produkt
     * @param \Lucid\Booking\Api\Data\ProduktInterface $produkt
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lucid\Booking\Api\Data\ProduktInterface $produkt
    );

    /**
     * Retrieve Produkt
     * @param string $id
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve Produkt matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lucid\Booking\Api\Data\ProduktSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Produkt
     * @param \Lucid\Booking\Api\Data\ProduktInterface $produkt
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lucid\Booking\Api\Data\ProduktInterface $produkt
    );

    /**
     * Delete Produkt by ID
     * @param string $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
