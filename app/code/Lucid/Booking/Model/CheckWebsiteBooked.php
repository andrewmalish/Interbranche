<?php

namespace Lucid\Booking\Model;

use Lucid\Booking\Model\LucidBookedWebsiteFactory;
use Lucid\Booking\Api\Data\Ep1StepsSessionInterface;



class CheckWebsiteBooked
{

    protected $bookedSubpages;
    protected $currentDay;

    public function __construct(

        Ep1StepsSessionInterface $ep1StepsSession,
        LucidBookedWebsiteFactory $lucidBookedWebsiteFactory

    ) {
        $this->_lucidBookedWebsiteFactory = $lucidBookedWebsiteFactory;
        $this->_ep1StepsSession = $ep1StepsSession;
    }


    public function checkSessionWebsite()
    {

        $CustomerEp1Session = $this->_ep1StepsSession->getCustomerEp1Session();

        if ($CustomerEp1Session['picked_date'] && $CustomerEp1Session['picked_date'] != '') {

            $this->getCurrentDay($CustomerEp1Session['picked_date']);

        }

        if ($CustomerEp1Session['website'] != '' && $CustomerEp1Session['subpage'] != '') {
            $this->getSubpagesArray();
            $title = $CustomerEp1Session['website'] . '_' . $CustomerEp1Session['subpage'];

            return $this->checkSubpageBooked($title);
        }

        return false;
    }


    protected function checkSubpageBooked($title)
    {

        return in_array(trim($title), $this->bookedSubpages);

    }


    protected function getSubpagesArray()
    {

        if (!empty($this->bookedSubpages)) {
            return $this->bookedSubpages;
        }

        $this->bookedSubpages = array();

        $data = $this->_lucidBookedWebsiteFactory->create()
            ->getCollection()
            ->addFieldToSelect('order_id')
            ->addFieldToSelect('website')
            ->addFieldToSelect('subpage')
            ->addFieldToFilter('booked_date', $this->currentDay)
            ->getData();

        if (!empty($data)) {
            foreach ($data as $value) {
                if ($value['order_id'] !== null) {
                    $this->bookedSubpages[] = $value['website'] . '_' . $value['subpage'];
                }
            }
        }

        return $this->bookedSubpages;
    }

    private function getCurrentDay($day)
    {

        if ($day) {
            $this->currentDay = date('Y-m-d', strtotime($day));
        } else {
            $this->currentDay = date('Y-m-d');
        }

        return $this->currentDay;

    }


}