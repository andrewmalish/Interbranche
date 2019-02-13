<?php


namespace Lucid\Booking\Model\ResourceModel\CalendarDays;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Lucid\Booking\Model\CalendarDays',
            'Lucid\Booking\Model\ResourceModel\CalendarDays'
        );
    }
}
