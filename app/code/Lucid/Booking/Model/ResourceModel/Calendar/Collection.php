<?php


namespace Lucid\Booking\Model\ResourceModel\Calendar;

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
            'Lucid\Booking\Model\Calendar',
            'Lucid\Booking\Model\ResourceModel\Calendar'
        );
    }
}
