<?php


namespace Lucid\Booking\Block\Adminhtml\Calendar\Edit;

use function GuzzleHttp\Psr7\str;
use Magento\Framework\View\Element\Template;

class Urls extends \Magento\Framework\View\Element\Template
{
    protected $_urlBuider;
    protected $calendarDaysFactory;

    public function __construct(
        Template\Context $context,
        array $data = [],
        \Magento\Framework\UrlInterface $_urlBuider,
        \Lucid\Booking\Model\CalendarDaysFactory $calendarDaysFactory
    )
    {



        parent::__construct($context, $data);
        $this->_urlBuilder = $_urlBuider;
        $this->calendarDaysFactory = $calendarDaysFactory;
    }


    public function getSaveUrl(){


        $url = $this->_urlBuilder->getUrl('lucid_booking/calendar/save', $paramsHere = array());

        return  $url;
    }

    public function getCalendarSaveDataUrl(){

        $url = $this->_urlBuilder->getUrl('lucid_booking/calendardays/', $paramsHere = array());

        return  $url;
    }

    public function getCalendarId(){

        return  $this->getRequest()->getParam('id');
    }

    public function getCalendarDays() {

        $today = strtotime(date('d-m-Y'));
        $readyDays = "";
        $days = $this->calendarDaysFactory->create()->getCollection()->addFieldToSelect('day')->addFieldToFilter('day', array('gteq' => $today))->getData();


        foreach ($days as $day) {
            $readyDays .= "'". date('m-d-Y',$day['day']) . "',";
        }

        return $readyDays;


    }
}
