<?php


namespace Lucid\Booking\Model;

use Lucid\Booking\Model\LucidBookedWebsiteFactory;


class RemoveTemporaryBooked
{

    public function __construct(
        LucidBookedWebsiteFactory $lucidBookedWebsiteFactory
    )
    {

        $this->_lucidBookedWebsiteFactory = $lucidBookedWebsiteFactory;

    }

    public function removeTemporary()
    {

        $subpageDataModel = $this->_lucidBookedWebsiteFactory->create();
        $items = $subpageDataModel
            ->getCollection()
            ->addFieldToSelect('id')
            ->addFieldToFilter('timestamp', ['lteq' => time()])
            ->addFieldToFilter('order_id', ['null'=> true] )
            ->getItems();


        foreach ($items as $item) {

            if ($id = $item->getId()) {
                $subpageDataModel->load($id);
                $subpageDataModel->delete();

            }
        }

    }


}