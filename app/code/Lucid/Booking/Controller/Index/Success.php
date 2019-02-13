<?php


namespace Lucid\Booking\Controller\Index;



class Success extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $formKeyValidator;
    protected $onlineForm;


    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;



    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Lucid\Booking\Model\OnlineForm $onlineForm,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator


    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->onlineForm = $onlineForm;

        $this->_messageManager = $messageManager;
        parent::__construct($context);

    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        return $this->resultPageFactory->create();

    }

}
