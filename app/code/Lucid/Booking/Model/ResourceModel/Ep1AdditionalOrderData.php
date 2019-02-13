<?php


namespace Lucid\Booking\Model\ResourceModel;

class Ep1AdditionalOrderData extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ep1_order_data', 'id');
    }
}
