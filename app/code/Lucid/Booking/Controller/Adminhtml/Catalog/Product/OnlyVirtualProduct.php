<?php

namespace Lucid\Booking\Controller\Adminhtml\Catalog\Product;



class OnlyVirtualProduct extends \Magento\Catalog\Controller\Adminhtml\Product\NewAction
{

    public function execute()
    {
        if($this->getRequest()->getParam('type')){
            $this->getRequest()->setParams(array('type'=>'virtual'));
        }

        return parent::execute();
    }
}