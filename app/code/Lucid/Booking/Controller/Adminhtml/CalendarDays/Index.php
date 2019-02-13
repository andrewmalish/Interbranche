<?php


namespace Lucid\Booking\Controller\Adminhtml\CalendarDays;

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;
    protected $_calendarDaysFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
//        CalendarDaysFactory $calendarDaysFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->getCalendarDaysFactory();
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        if($this->getRequest()->getParam('action')=='saveday'){

            $this->updateDay();
            print_r(json_encode(array('result'=>'save Day')));
        }
        else {
            return $this->getDays();
        }
    }

    public function getDays(){

        $day = $this->getRequest()->getParam('day');
        $day = strtotime($day);
        $calendarDays = $this->_calendarDaysFactory->create()->getCollection();
        try {
            $day = $calendarDays->addFieldToSelect('*')->addFieldToFilter('day', $day)->getLastItem();

            if(!empty($day->getData())) {
                print_r(json_encode($day->getData()));
                exit;
            }
            else {
                print_r(json_encode(array('result'=>'no day found')));
                exit;
            }
        }
        catch (\Exception $e){
            print_r($e);

        }
        return ;
    }

    public function updateDay() {

        $day = $this->getRequest()->getParam('day');
        if($day) {
            $day_enable = ($this->getRequest()->getParam('day_enable') == 'true')? 1 : 0;
            $basis_price = $this->getRequest()->getParam('basis_price');
            $premium_price = $this->getRequest()->getParam('premium_price');
            $calendarDays = $this->_calendarDaysFactory->create()->getCollection();
            $day = strtotime($day);
            $dayItem = $calendarDays->addFieldToSelect('*')->addFieldToFilter('day', $day)->getLastItem();

            if ($day) {

                if($day && $day_enable == 0 && $basis_price == '' && $premium_price == ''){
                    try {
                        $dayItem->delete();
                        print_r(json_encode(array('result' => 'day succesfully deleted')));
                        exit;
                }
                catch (\Exception $e) {
                    print_r(json_encode(array('cant delete day '=> $e)));
                    }
                }
                try {
                    $dayItem->setDay($day)
                        ->setIsDisabled($day_enable)
                        ->setBasisPrice($basis_price)
                        ->setPremiumPrice($premium_price)
                        ->save();
                }
                catch(\Exception $e) {
                    print_r(json_encode(array('fail day saving'=> $e)));
                }
            }
        }

    }

    protected function getCalendarDaysFactory(){
        return  $this->_calendarDaysFactory = ($this->_calendarDaysFactory) ? $this->_calendarDaysFactory :  $this->_objectManager->create('\Lucid\Booking\Model\CalendarDaysFactory');
    }

}
