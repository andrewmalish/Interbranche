<?php
namespace Lucid\Booking\Block\Forms;


use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use \Magento\Catalog\Model\ProductFactory;
use Lucid\Booking\Model\CalendarRepository;
use Magento\Framework\View\Element\Template;
use Lucid\Booking\Model\Ep1DataFactory;
use Magento\CheckoutAgreements\Model\AgreementFactory;
use Lucid\Booking\Api\Data\Ep1StepsSessionInterface;



class Online extends Template
{
    /**
     * @var Registry
     */
    protected $registry;
    protected $_dir;

    protected $customerSession;
    protected $_ep1DataFactory;
    protected $storeManager;
    protected $agreementFactory;
    protected $_responseFactory;
    protected $_url;

    protected $productRepository;
    protected $magentoCart;
    protected $calendarRepository;
    protected $checkoutSession;
    protected $calendar;
    private   $_ep1StepsSession;



    public function __construct(
        Template\Context $context,
        Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        AgreementFactory $agreementFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        Ep1StepsSessionInterface $ep1StepsSession,
//        \Magento\Checkout\Model\Cart $magentoCart,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        array $data = []

    )
    {

        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->_ep1StepsSession = $ep1StepsSession;
//        $this->magentoCart = $magentoCart;
        $this->agreementFactory = $agreementFactory;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
    }


    public function getCustomer7pathInfo()
    {
        $cusomer7PathData = $this->_ep1StepsSession->getCustomer7pathInfo();

        if(is_array($cusomer7PathData)){
            $cusomer7PathData = $cusomer7PathData['customer7pathInfo'];
        }
        else{
            return false;
        }
        return $cusomer7PathData;
    }

    public function getCustomerSessionInfo()
    {
        $cusomer7PathData = $this->_ep1StepsSession->getCustomer7pathInfo();
        return is_array($cusomer7PathData) ? $cusomer7PathData : false;
    }

    public function getCustomerEp1Data()
    {
        $data = $this->_ep1StepsSession->getCustomerEp1Session();
        return is_array($data) ? $data : false;
    }

    public function getPaypalExpressUrl(){
        $baseurl = $this->storeManager->getStore()->getBaseUrl();
        return $baseurl.'paypal/express/start/button/1/';
    }


    public function getItems(){
        if ($quote = $this->getCurrentQuote()){
            if(is_array($quote->getItems())){
                return $quote->getItems();
            }
            else {
                $this->_redirectToLaststep();
            }

        }
    }


    private function _redirectToLaststep(){

        if($productId = $this->getProductId()) {
            $redirectionUrl = $this->_url->getUrl('booking/index/newcheckout/product/'.$productId);
        }
        else {
            $redirectionUrl = $this->_url->getUrl('motive.html');
        }
        $this->_responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
        return $this;
    }

    public function getCurrentQuote(){

//        return $this->magentoCart->getQuote();

        return $this->checkoutSession->getQuote();
    }

    public function formatPrice($value)
    {
        $price = sprintf('%.2F', $value);
        $price = str_replace('.',',',$price);
        return $price;
    }

    public function formatBlockDate($date)
    {
        return date('d.m.Y', strtotime($date) );
    }

    public function getTerms (){
        $agreements = $this->agreementFactory->create()
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_active', true)
            ->getItems();

        return (count($agreements) > 0) ? $agreements : false;
    }

    private function getProductId() {
        $sessionEp1 = $this->_ep1StepsSession->getCustomerEp1();
        $id = $this->getRequest()->getParam('product') ? $this->getRequest()->getParam('product') : $sessionEp1['product_id'];
        return $id;
    }

}
