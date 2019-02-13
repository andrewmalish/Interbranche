<?php


namespace Lucid\Booking\Model\ResourceModel\Ep1OrderData;

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
            'Lucid\Booking\Model\Ep1OrderData',
            'Lucid\Booking\Model\ResourceModel\Ep1OrderData'
        );
    }
}
