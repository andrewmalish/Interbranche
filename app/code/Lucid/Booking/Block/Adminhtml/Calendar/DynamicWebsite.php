<?php


namespace Lucid\Booking\Block\Adminhtml\Calendar;

use Magento\Framework\View\Element\Template;
use Lucid\Booking\Model\CalendarRepository;


class DynamicWebsite extends \Magento\Framework\View\Element\Template
{
    protected $calendarRepository;

    public function __construct(
        Template\Context $context,
        array $data = [],
        CalendarRepository $calendarRepository
    )
    {
        parent::__construct($context, $data);
        $this->calendarRepository = $calendarRepository;
    }


    public function getCalendar(){
        return   $this->calendarRepository->getById($this->getRequest()->getParam('id'));
    }


    public function getDynamicWebsites(){
        $calendar = $this->calendarRepository->getById($this->getRequest()->getParam('id'));
        $json = $calendar->getWebsitesJson();
        if ($json  == "{}"){
            return false;
        }
        elseif (strpos($json,'podcast') === false) {

            $json = $this->defaultJsonObject();
        }

        return   json_decode($json);
    }

    private function defaultJsonObject() {
        return '{"0":{"isActive":true,"title":" ProSieben","podcast":" zulch","subpages":{"6":{"isActive":true,"title":"100"},"7":{"isActive":true,"title":"66"},"8":{"isActive":false,"title":"77"},"9":{"isActive":true,"title":"99"}},"subpages_range":{"14":{"isActive":true,"subpageFrom":"10","subpageTo":"20"}}},"1":{"isActive":true,"title":" New Website 2","podcast":"  zulch 2","subpages":{"6":{"isActive":true,"title":"10"},"7":{"isActive":true,"title":"31"},"8":{"isActive":true,"title":"44"}},"subpages_range":{}},"2":{"isActive":true,"title":" ProSieben","podcast":" ","subpages":{"6":{"isActive":true,"title":"35"},"7":{"isActive":false,"title":"45"},"8":{"isActive":true,"title":"55"},"9":{"isActive":true,"title":"60"},"10":{"isActive":false,"title":"70"}},"subpages_range":{"15":{"isActive":true,"subpageFrom":"100","subpageTo":"200"},"16":{"isActive":true,"subpageFrom":"300","subpageTo":"400"}}},"3":{"isActive":true,"title":" 444","podcast":" ","subpages":{"6":{"isActive":true,"title":"22"}},"subpages_range":{"11":{"isActive":true,"subpageFrom":"123","subpageTo":"20"},"12":{"isActive":true,"subpageFrom":"123","subpageTo":"20"}}}}';
    }
}
