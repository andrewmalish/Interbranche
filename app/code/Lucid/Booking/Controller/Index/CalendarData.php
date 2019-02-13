<?php


namespace Lucid\Booking\Controller\Index;

use Lucid\Booking\Model\CalendarRepository;


class CalendarData extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $_assetRepo;
    protected $_filesystem;
    protected $calendarRepository;
    protected $_calendarDaysFactory;
    protected $calendar;
//    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        CalendarRepository $calendarRepository


    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);

        $this->calendarRepository = $calendarRepository;



    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $params = $this->getRequest()->getParams();

        if (isset ($params['day'])) {

            $date = $params['day'];
            try{
                if($this->getLastCalendar()) {
                    $day = $this->getDayInfo($date);
                    print_r(json_encode($day));
                    die;
                }

            }
            catch (\Exception $e) {
                print_r(json_encode(array('result' => 'false')));
            }

        }

        return ;
    }



    public function getLastCalendar(){
        return   $this->calendar = $this->calendarRepository->getLastCalendar();
    }



    private function getDayInfo($day){

        $dayItem = array();

        $calendarDays = $this->getCalendarDaysRepository()->create()->getCollection();
        $dayTime = strtotime($day);
        $dayItem = $calendarDays->addFieldToSelect('*')->addFieldToFilter('day', $dayTime)->getLastItem()->getData();
        $dayItem['website_json_data'] = $this->updateCurrentDayWebsites($day);
        return $dayItem;
    }


    public function updateCurrentDayWebsites($day){
        return $workedWebsites =  $this->calendarRepository->getDynamicWebsites($this->calendar, $day);
    }

    protected function getAssetsRepo()
    {
        return $this->_assetRepo = $this->_objectManager->create('Magento\Framework\View\Asset\Repository');
    }

    protected function getFileSystem()
    {
        return $this->_filesystem = $this->_objectManager->create('\Magento\Framework\Filesystem');
    }

    protected function getCalendarDaysRepository()
    {
        return  $this->_calendarDaysFactory = ($this->_calendarDaysFactory) ? $this->_calendarDaysFactory :  $this->_objectManager->create('\Lucid\Booking\Model\CalendarDaysFactory');
    }

}
