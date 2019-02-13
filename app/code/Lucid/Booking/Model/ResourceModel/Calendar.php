<?php


namespace Lucid\Booking\Model\ResourceModel;

class Calendar extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lucid_booking_calendar', 'id');
    }
}
