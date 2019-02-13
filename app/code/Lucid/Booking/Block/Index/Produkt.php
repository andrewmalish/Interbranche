<?php

namespace Lucid\Booking\Block\Index;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use \Magento\Catalog\Model\ProductFactory;
use Lucid\Booking\Model\CalendarRepository;
use Magento\Framework\View\Element\Template;
use Lucid\Booking\Model\Ep1DataFactory;
use Lucid\Booking\Api\Data\Ep1StepsSessionInterface;



class Produkt extends Template
{
    /**
     * @var Registry
     */
    protected $registry;
    protected $_dir;

    private $_sessionManager;
    protected $customerSession;
    protected $catalogSession;
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
    protected $calendarDaysFactory;
    protected $calendar;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        ProductFactory $productRepository,
        CalendarRepository $calendarRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        Ep1DataFactory $ep1DataFactory,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Lucid\Booking\Model\CalendarDaysFactory $calendarDaysFactory,
        \Interbranche\SmsSender\Helper\Data $smsDataHelper,
        \Interbranche\SmsSender\Helper\SMSCounter $smsCounter,
        Ep1StepsSessionInterface $ep1StepsSession,
        array $data = []





    )
    {
        $this->_dir = $dir;
        $this->registry = $registry;
        $this->productRepository = $productRepository;
        $this->calendarRepository = $calendarRepository;
        $this->customerSession = $customerSession;
        $this->catalogSession = $catalogSession;
        $this->_sessionManager = $sessionManager;
        $this->_ep1DataFactory = $ep1DataFactory;
        $this->calendarDaysFactory = $calendarDaysFactory;
        $this->_ep1StepsSession = $ep1StepsSession;
        $this->smsDataHelper = $smsDataHelper;
        $this->smsCounter = $smsCounter;
        parent::__construct($context, $data);

    }


    public function getCalendar(){

        return   $this->calendarRepository->getById(1);

    }


    public function getIsAuth(){
        return $this->getRequest()->getParam('auth');
    }

    public function getByDay($date =''){
        if($date == ''){
            $date = date('Y-m-d');
        }
        return   $this->calendar = $this->calendarRepository->getByDay($date);
    }


    public function getLastCalendar(){
        return   $this->calendar = $this->calendarRepository->getLastCalendar();
    }

    public function prepareEp1Data($source) {
        $source = (string)$source;
        $source = substr($source, 6, 1006);
        $source = htmlentities($source);

        return json_encode(array('ep1' =>$source));
    }

    public function getStartDay($dayInAdvance, $calendarDays) {

        $disabledDays = array(); //$this->getCalendarDisabledDaysArray();

        $startDate = (strtotime($calendarDays) > strtotime(date('m/d/Y', strtotime($dayInAdvance.' weekdays')))) ? strtotime($calendarDays) : strtotime($dayInAdvance.' weekdays');

        /***this means we got friday**/
//        if (date('N', $startDate) == 6 ){
//            $startDate = strtotime('next monday');
//        }

        /***this means we got saturday or sunday**/
//        if (date('N', time()) == 5 || date('N', time()) == 6  || date('N', time()) == 7) {
//            $dayInAdvance--;
//            $startDate = strtotime('next monday + '.$dayInAdvance.' day');
//        }
//
//        if (date('N', $startDate) == 6 ){
//            $strtDay = date('m/d/Y', $startDate);
//            $startDate = strtotime( $strtDay. ' + 3 day');
//        }
//        if (date('N', $startDate) == 7 ){
//            $strtDay = date('m/d/Y', $startDate);
//            $startDate = strtotime( $strtDay. ' + 2 day');
//
//        }


        return $startDate = date('m/d/Y', $startDate);
    }


    public function getCalendarDisabledDays() {

        $readyDays = "";
        $days = $this->getCalendarDisabledDaysTime();

        foreach ($days as $day) {
            $readyDays .= "[". date('m, d, Y',$day['day']) . "],";
        }

        return $readyDays;

    }


    protected function getCalendarDisabledDaysArray() {

        $readyDays = array();
        $days = $this->getCalendarDisabledDaysTime();

        foreach ($days as $day) {
            $readyDays[] = $day['day'] ;
        }

        return $readyDays;
    }

    public function currentDayPrice($day) {
        $day = strtotime(date($day));

        $price = array();

        $price = $this->calendarDaysFactory
            ->create()
            ->getCollection()
            ->addFieldToSelect('basis_price')
            ->addFieldToSelect('premium_price')
            ->addFieldToFilter('day', array('gteq' => $day))
            ->getLastItem()
//            ->addFieldToFilter('is_disabled', array('eq' => 1))
            ->getData();

        return $price;

    }

    protected function getCalendarDisabledDaysTime() {


        $today = strtotime(date('d-m-Y'));
        $disabledDays = array();

        $disabledDays = $this->calendarDaysFactory
            ->create()
            ->getCollection()
            ->addFieldToSelect('day')
            ->addFieldToFilter('day', array('gteq' => $today))
            ->addFieldToFilter('is_disabled', array('eq' => 1))
            ->getData();

        return $disabledDays;

    }


    public function getDynamicWebsites($day)
    {
        if (!$this->calendar)
            return false;

        return $this->calendarRepository->getDynamicWebsites($this->calendar, $day);

    }

    public function formatBlockDate($date)
    {
        return date('d.m.Y', strtotime($date) );
    }


    public function getCustomerData(){
        return $this->customerSession->getCustomer();
    }

    /**
     * @return Product
     */
    public function getProduct()
    {

        $proid = $this->getRequest()->getParam('id');


        if (is_null($this->product)) {

            $this->product = $this->productRepository->create()->load($proid);

            if (!$this->product->getId()) {
                throw new LocalizedException(__('Failed to initialize product'));
            }
        }

        return $this->product;
    }

    public function getProductName()
    {
        return $this->getProduct()->getName();
    }

    public function getCustomerSessionInfo()
    {
        $data = $this->_ep1StepsSession->getCustomer7pathInfo();

        if(is_array($data)) {
            if (isset($data['customer7pathInfo']) && isset($data['tokens'])) {
                return $data;
            }
        }
        return false;

    }

    public function getCustomerEp1Session()
    {
        $data = $this->_ep1StepsSession->getCustomerEp1Session();
        return is_array($data) ? $data : false;
    }

    public function getFilledSessionInfo()
    {
        $data = $this->_ep1StepsSession->getStep1();
        return (isset($data)) ? true : false;
    }

    public function getEp1Data(){
        return $this->_ep1DataFactory->create()->getCollection()->addFieldToFilter('product_id',$this->getProduct()->getId())->getLastItem();
    }

    public function getAbsoluteMediaPath($path)
    {
        $mediaUrl =  $this->getUrl('pub/media');
        return $mediaUrl. 'catalog/product/ep1' .$path;
    }

    /**
     * @return string,array
     */
    public function getDefaultSmsText($array = false){
        $result = '';
        $smsData = $this->smsDataHelper;
        if(!$smsData->isEnabled()){
            return $result;
        }
        $text = !empty($smsData->getSmsDefaultText()) ? $smsData->getSmsDefaultText() : "";
        $legal = !empty($smsData->getSmsLegalText()) ? $smsData->getSmsLegalText() : "";

//		$result = !empty($text) && !empty($legal) ? $text ."\r\n". $legal : "";
        $result = !empty($text) && !empty($legal) ? $text  : "";

        $result = empty($result) ? $text : $result;
        $result = empty($result) ? $legal : $result;

        if($array){
            $result = explode("\r\n",$result);
        }
        return $result;
    }

    /**
     * @return string,array
     */
    public function getDefaultEmailText($array = false){
        $result = '';
        $smsData = $this->smsDataHelper;
        if(!$smsData->isEnabled()){
            return $result;
        }
        $result = !empty($smsData->getEmailDefaultText()) ? $smsData->getEmailDefaultText() : "";



        if($array){
            $result = explode("\r\n",$result);
        }
        return $result;
    }

    /**
     * @return int
     */
    public function getSmsLength($sms){
        return $this->smsCounter->count($sms)->length;
    }

    /**
     * Debug customer session info
     * @return bool
     */
    public function showSessionData(){
        echo '<br />';
        echo '<pre>';
        echo '<h3>Customer ep1 session</h3>';
        var_dump($this->_ep1StepsSession->getCustomerEp1Session());
        echo '<br />';
        echo '<h3>Customer 7path session</h3>';
        var_dump($this->_ep1StepsSession->getCustomer7pathInfo());

        echo '<br />';
        echo '<h3>Customer magento</h3>';
        var_dump($this->customerSession->getSessionId());

        echo '<br />';
        echo '<h3>Customer magento</h3>';
        var_dump($this->catalogSession->getSessionId());
        echo '</pre>';
        return false;
    }

}
