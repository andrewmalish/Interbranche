<?php


namespace Lucid\Booking\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CalendarRepositoryInterface
{


    /**
     * Save Calendar
     * @param \Lucid\Booking\Api\Data\CalendarInterface $calendar
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lucid\Booking\Api\Data\CalendarInterface $calendar
    );

    /**
     * Retrieve Calendar
     * @param string $id
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve Calendar matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lucid\Booking\Api\Data\CalendarSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Calendar
     * @param \Lucid\Booking\Api\Data\CalendarInterface $calendar
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lucid\Booking\Api\Data\CalendarInterface $calendar
    );

    /**
     * Delete Calendar by ID
     * @param string $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
