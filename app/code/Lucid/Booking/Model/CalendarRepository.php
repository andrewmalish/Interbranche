<?php


namespace Lucid\Booking\Model;

use Lucid\Booking\Api\Data\CalendarInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Lucid\Booking\Model\ResourceModel\Calendar\CollectionFactory as CalendarCollectionFactory;
use Lucid\Booking\Api\CalendarRepositoryInterface;
use Lucid\Booking\Api\Data\CalendarSearchResultsInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lucid\Booking\Model\ResourceModel\Calendar as ResourceCalendar;

class CalendarRepository implements calendarRepositoryInterface
{

    protected $calendarFactory;

    protected $resource;

    private $storeManager;

    protected $calendarCollectionFactory;

    protected $dataObjectProcessor;

    protected $searchResultsFactory;

    protected $dataCalendarFactory;

    protected $dataObjectHelper;

    private $bookedWebsiteFactory;

    private $bookedSubpages;

    private $currentDay;

    protected $removeTemporary;

    /**
     * @param ResourceCalendar $resource
     * @param CalendarFactory $calendarFactory
     * @param CalendarInterfaceFactory $dataCalendarFactory
     * @param CalendarCollectionFactory $calendarCollectionFactory
     * @param CalendarSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceCalendar $resource,
        CalendarFactory $calendarFactory,
        CalendarInterfaceFactory $dataCalendarFactory,
        CalendarCollectionFactory $calendarCollectionFactory,
        CalendarSearchResultsInterfaceFactory $searchResultsFactory,
        \Lucid\Booking\Model\LucidBookedWebsiteFactory $bookedWebsiteFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        \Lucid\Booking\Model\RemoveTemporaryBooked $removeTemporary

    ) {
        $this->resource = $resource;
        $this->calendarFactory = $calendarFactory;
        $this->calendarCollectionFactory = $calendarCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCalendarFactory = $dataCalendarFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->bookedWebsiteFactory = $bookedWebsiteFactory;
        $this->removeTemporary = $removeTemporary;

        $this->bookedSubpages = array();

    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lucid\Booking\Api\Data\CalendarInterface $calendar
    ) {
        /* if (empty($calendar->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $calendar->setStoreId($storeId);
        } */
        try {
            $calendar->getResource()->save($calendar);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the calendar: %1',
                $exception->getMessage()
            ));
        }
        return $calendar;
    }



    /**
     * @return CalendarCollectionFactory
     */public function getCalendarCollectionFactory()
{
    return $this->calendarCollectionFactory;
}

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        $calendar = $this->calendarFactory->create();
        $calendar->getResource()->load($calendar, $id);
        if (!$calendar->getId()) {
            throw new NoSuchEntityException(__('Calendar with id "%1" does not exist.', $id));
        }
        return $calendar;
    }

    /**
     * {@inheritdoc}
     */
    public function getByDay($date)
    {

   //     $date = str_replace('/','',$date);

        $date = date('Y-m-d', strtotime($date));

        $collection = $this->calendarCollectionFactory->create();

        $collection->addFieldToSelect('*')
            ->addFieldToFilter('Days', ['lteq' => $date])
            ->addFieldToFilter('DaysTo', ['gteq' => $date]);


        $calendar = $collection->getLastItem();

        if (!$calendar->getId()) {
            throw new NoSuchEntityException(__('Calendar matching this date is not found'));
        }

        return $calendar;
    }


    /**
     * {@inheritdoc}
     */
    public function getLastCalendar()
    {

        $collection = $this->calendarCollectionFactory->create();
        $collection->addFieldToSelect('*');
        $calendar = $collection->getLastItem();

        if (!$calendar->getId()) {
            throw new NoSuchEntityException(__('Calendar matching this date is not found'));
        }

        return $calendar;
    }




    public function getDynamicWebsites($calendar,$currentDate){

        if (!$calendar)
            return false;

        $this->removeTemporary->removeTemporary();
        $this->getCurrentDay($currentDate);
        $this->getSubpagesArray();

        $websites = json_decode($calendar->getWebsitesJson());

        $workedWebsites = array();
        foreach($websites as $website){
            if($website->isActive) {
                if($subpage = $this->getSubpage($website)){
                    $wtitle = trim($website->title);
                    $workedWebsites[$wtitle] = trim($website->podcast)."_".$subpage;
                }
                else {
                    $wtitle = trim($website->title);
                    $workedWebsites[$wtitle] = 'disabled';
                }
            }
        }

        return $workedWebsites;
    }


    /** Collect subpages info */
    public function getSubpage($website){

        $subpagesList = array();

        /** collect single subpages */
        if (count(get_object_vars($website->subpages)) > 0) {
            foreach($website->subpages as $subpage){
                if ($subpage->isActive) {
                    if(!$this->checkSubpageBooked($website->podcast . '_' . $subpage->title))
                        $subpagesList[] = trim($subpage->title);
                }
            }
        }

        /** collect single subpages range */
        if (count(get_object_vars($website->subpages_range)) > 0) {
            foreach($website->subpages_range as $subpage){
                if ($subpage->isActive) {

                    $subpageFrom = $subpage->subpageFrom;

                    while ($subpageFrom <= $subpage->subpageTo) {
                        if (!$this->checkSubpageBooked($website->podcast . '_' . $subpageFrom)) {
                            $subpagesList[] = trim($subpageFrom);
                        }
                        $subpageFrom++;
                    }
                }
            }
        }

        /** sort subpages by value */
        sort($subpagesList);

        $subpageResult = array_shift($subpagesList);
        return $subpageResult;

    }

    /**
     * @param $day 'Y-m-d' format
     * @return false|string
     */
    private function getCurrentDay($day) {

        if ($day){
            $this->currentDay = date('Y-m-d', strtotime($day));
        }
        else {
            $this->currentDay = date('Y-m-d');
        }

        return $this->currentDay;

    }

    protected function checkSubpageBooked($title){

        return in_array(trim($title), $this->bookedSubpages);

    }


    protected function getSubpagesArray(){

        if (!empty($this->bookedSubpages)){
            return $this->bookedSubpages;
        }


        if(!$this->currentDay) {
            $this->getCurrentDay(false);
        }

        $data = $this->bookedWebsiteFactory->create()
            ->getCollection()
            ->addFieldToSelect('website')
            ->addFieldToSelect('subpage')
            ->addFieldToFilter('booked_date', $this->currentDay)
            ->getData();

        if (!empty($data)){
            foreach ($data as $value){
                $this->bookedSubpages[] = $value['website'] . '_' . $value['subpage'];
            }
        }

        return $this->bookedSubpages;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->calendarCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Lucid\Booking\Api\Data\CalendarInterface $calendar
    ) {
        try {
            $calendar->getResource()->delete($calendar);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Calendar: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
