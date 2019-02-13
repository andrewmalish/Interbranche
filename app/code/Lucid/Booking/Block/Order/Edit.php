<?php

namespace Lucid\Booking\Block\Order;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use \Magento\Catalog\Model\ProductFactory;
use Lucid\Booking\Model\Ep1AdditionalOrderDataFactory;
use Magento\Framework\View\Element\Template;
use Lucid\Booking\Model\Ep1DataFactory;
use Lucid\Booking\Api\Data\Ep1StepsSessionInterface;


class Edit extends Template
{
    /**
     * @var Registry
     */
    protected $registry;
    protected $_dir;

    private $_sessionManager;
    protected $customerSession;
    protected $order;
    protected $_ep1DataFactory;

    protected $smsDataHelper;
    protected $smsCounter;

    private $_ep1StepsSession;


    /**
     * @var Product
     */
    private $product;

    protected $productRepository;
    protected $calendarRepository;
    protected $_ep1OrderDataFactory;
    protected $ep1Data;
    protected $_orderFactory;


    public function __construct(
        Template\Context $context,
        Registry $registry,
        ProductFactory $productRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        Ep1DataFactory $ep1DataFactory,
        Ep1AdditionalOrderDataFactory $ep1OrderDataFactory,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Lucid\Booking\Model\CalendarDaysFactory $calendarDaysFactory,
        \Interbranche\SmsSender\Helper\Data $smsDataHelper,
        \Interbranche\SmsSender\Helper\SMSCounter $smsCounter,
        Ep1StepsSessionInterface $ep1StepsSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    )
    {
        $this->_dir = $dir;
        $this->registry = $registry;
        $this->productRepository = $productRepository;
        $this->customerSession = $customerSession;
        $this->_sessionManager = $sessionManager;
        $this->_ep1DataFactory = $ep1DataFactory;
        $this->_ep1OrderDataFactory = $ep1OrderDataFactory;
        $this->_ep1StepsSession = $ep1StepsSession;
        $this->smsDataHelper = $smsDataHelper;
        $this->_orderFactory = $orderFactory;
        $this->smsCounter = $smsCounter;
        parent::__construct($context, $data);
        $this->getOrderEp1Data();

    }


    public function getIsAuth()
    {
        return $this->getRequest()->getParam('auth');
    }


    public function prepareEp1Data($source)
    {
        $source = (string)$source;
        $source = substr($source, 6, 1006);
        $source = htmlentities($source);

        return json_encode(array('ep1' => $source));
    }


    public function getCustomerData()
    {
        return $this->customerSession->getCustomer();
    }

    /**
     * @return Product
     */
    public function getProduct()
    {

        if ($order = $this->getCustomerOrder()) {

            $item = '';
            $_items = $order->getAllItems();

            if (is_array($_items)) {
                $item = array_shift($_items);
            }

            if (is_null($this->product)) {

                $this->product = $this->productRepository->create()->load($item->getProductId());

                if (!$this->product->getId()) {
                    print_r('error - no product found!');
                }
            }

            return $this->product;
        }
        else {
            print_r('error - no product found!');
        }
        return false;
    }

    public function getCustomerOrder()
    {

        if($this->order) {
            return $this->order;
        }

        $orderId = $this->getRequest()->getParam('orderid');
        $order = $this->_orderFactory->create()->load($orderId);

        if ($customer = $this->getCustomerData() && $order) {
            if ($this->getCustomerData()->getId() === $order->getCustomerId()) {
                return $this->order = $order;
            }
        }

        return false;
    }

    public function getProductName()
    {
        return $this->getProduct()->getName();
    }


    public function getEp1Data()
    {
        return $this->_ep1DataFactory->create()->getCollection()->addFieldToFilter('product_id', $this->getProduct()->getId())->getLastItem();
    }

    private function getOrderEp1Data() {

        if(!$this->ep1Data) {
            try {

                $order =$this->getCustomerOrder();

                $this->ep1Data = $this->_ep1OrderDataFactory
                    ->create()
                    ->getCollection()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('order_id', $order->getIncrementId())
                    ->getLastItem();
            }
            catch(\Exception $e){
                echo 'error ep1data block';
                var_dump($e);
            }
        }

        return $this->ep1Data;

    }

    public function getStep1()
    {
        if (!empty($this->ep1Data)) {

            $data = $this->ep1Data->getData('step_1_json');
            $data = json_decode($data);
            return $data;

        }
        return '';
    }

}
