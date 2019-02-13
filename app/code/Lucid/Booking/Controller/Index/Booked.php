<?php


namespace Lucid\Booking\Controller\Index;


use Lucid\Booking\Model\Ep1ToW3;

class Booked extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $_assetRepo;
    protected $_filesystem;
    protected $ep1ToW3;
    protected $cronSender;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(

        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Interbranche\SmsSender\Cron\Sender $cronSender,
        Ep1ToW3 $ep1ToW3

    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->ep1ToW3 = $ep1ToW3;
        $this->cronSender = $cronSender;

    }

    public function execute()
    {

        if($orderid = $this->getRequest()->getParam('orderid')){


        if($this->cronSender->sendTestEmail($orderid)) {

            var_dump('email was sent of order ' . $orderid);
        }
        else{
            var_dump('wrong order number #' . $orderid);
        }
            return;
        }


        $date = $this->getRequest()->getParam('date');

        $websites = $this->ep1ToW3->getBookedWebsite($date);

        echo '<pre>';
        var_dump($websites);
        echo '</pre>';
        die('suceed!');

    }
}
