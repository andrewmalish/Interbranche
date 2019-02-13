<?php


namespace Lucid\Booking\Controller\Adminhtml;

abstract class Calendar extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Lucid_Booking::top_level';
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Lucid'), __('Lucid'))
            ->addBreadcrumb(__('Calendar'), __('Calendar'));
        return $resultPage;
    }
}
