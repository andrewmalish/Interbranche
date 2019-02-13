<?php


namespace Lucid\Booking\Model\ResourceModel\Produkt;

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
            'Lucid\Booking\Model\Produkt',
            'Lucid\Booking\Model\ResourceModel\Produkt'
        );
    }
}
