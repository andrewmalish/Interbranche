<?php


namespace Lucid\Booking\Model\ResourceModel;

class LucidBookedWebsite extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lucid_booked_websites', 'id');
    }
}
