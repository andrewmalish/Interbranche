<?php

namespace Lucid\Booking\Block\Index;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Lucid\Booking\Model\ProduktRepository;
use Lucid\Booking\Model\CalendarRepository;
use Magento\Framework\View\Element\Template;
use Lucid\Booking\Model\Ep1DataFactory;
use Lucid\Booking\Api\Data\Ep1StepsSessionInterface;



class Index extends Template
{
	/**
	 * @var Registry
	 */
	protected $registry;

	/**
	 * @var Product
	 */
	private $product;

    protected $productRepository;
    protected $calendarRepository;
    protected $_ep1DataFactory;
    private $_ep1StepsSession;



    public function __construct(
        Template\Context $context,
        Registry $registry,
        ProduktRepository $productRepository,
        CalendarRepository $calendarRepository,
        Ep1DataFactory $ep1DataFactory,
        Ep1StepsSessionInterface $ep1StepsSession,
        array $data = []

    )
    {
	    $this->registry = $registry;
        $this->productRepository = $productRepository;
        $this->calendarRepository = $calendarRepository;
        $this->_ep1DataFactory = $ep1DataFactory;
        $this->_ep1StepsSession =$ep1StepsSession;
        parent::__construct($context, $data);
    }


    public function getCalendar(){

     return   $this->calendarRepository->getById(1);

    }

    public function getDynamicWebsites(){

        $calendar = $this->calendarRepository->getById(1);
        return   json_decode($calendar->getWebsitesJson());

    }

	/**
	 * @return Product
	 */
	private function getProduct()
	{
		if (is_null($this->product)) {
			$this->product = $this->registry->registry('product');

			if (!$this->product->getId()) {
				throw new LocalizedException(__('Failed to initialize product'));
			}
		}

		return $this->product;
	}

	public function getEp1Data(){
        return $this->_ep1DataFactory->create()->getCollection()->addFieldToFilter('product_id',$this->getProduct()->getEntityId())->getLastItem();

    }

	public function getProductName()
	{
		return $this->getProduct()->getName();
	}

}
