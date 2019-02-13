<?php


namespace Lucid\Booking\Model\ResourceModel;

class CalendarDays extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('calendar_days_schedule', 'id');
    }
}
