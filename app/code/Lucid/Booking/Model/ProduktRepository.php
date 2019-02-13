<?php


namespace Lucid\Booking\Model;

use Lucid\Booking\Api\ProduktRepositoryInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Lucid\Booking\Model\ResourceModel\Produkt as ResourceProdukt;
use Magento\Store\Model\StoreManagerInterface;
use Lucid\Booking\Model\ResourceModel\Produkt\CollectionFactory as ProduktCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\DataObjectHelper;
use Lucid\Booking\Api\Data\ProduktInterfaceFactory;
use Lucid\Booking\Api\Data\ProduktSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Reflection\DataObjectProcessor;

class ProduktRepository implements produktRepositoryInterface
{

    protected $produktFactory;

    protected $resource;

    private $storeManager;

    protected $dataObjectProcessor;

    protected $dataProduktFactory;

    protected $searchResultsFactory;

    protected $produktCollectionFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourceProdukt $resource
     * @param ProduktFactory $produktFactory
     * @param ProduktInterfaceFactory $dataProduktFactory
     * @param ProduktCollectionFactory $produktCollectionFactory
     * @param ProduktSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceProdukt $resource,
        ProduktFactory $produktFactory,
        ProduktInterfaceFactory $dataProduktFactory,
        ProduktCollectionFactory $produktCollectionFactory,
        ProduktSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->produktFactory = $produktFactory;
        $this->produktCollectionFactory = $produktCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataProduktFactory = $dataProduktFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lucid\Booking\Api\Data\ProduktInterface $produkt
    ) {
        /* if (empty($produkt->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $produkt->setStoreId($storeId);
        } */
        try {
            $produkt->getResource()->save($produkt);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the produkt: %1',
                $exception->getMessage()
            ));
        }
        return $produkt;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        $produkt = $this->produktFactory->create();
        $produkt->getResource()->load($produkt, $id);
        if (!$produkt->getId()) {
            throw new NoSuchEntityException(__('Produkt with id "%1" does not exist.', $id));
        }
        return $produkt;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->produktCollectionFactory->create();
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
        \Lucid\Booking\Api\Data\ProduktInterface $produkt
    ) {
        try {
            $produkt->getResource()->delete($produkt);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Produkt: %1',
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
