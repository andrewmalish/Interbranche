<?php

namespace Lucid\Booking\Model;

use Magento\Framework\Event\ObserverInterface;
use Lucid\Booking\Model\Ep1OrderDataFactory;
use Lucid\Booking\Model\LucidBookedWebsiteFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Lucid\Booking\Api\Data\Ep1StepsSessionInterface;




class OnlineForm
{


    private $connector;
    private $_ep1OrderDataFactory;
    private $_lucidBookedWebsiteFactory;
    protected $scopeConfig;
    protected $_ep1StepsSession;



    /**
     * @var  \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $_transportBuilder;

    private $_storeManager;


    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig

    )
    {

        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager=$storeManager;
        $this->scopeConfig = $scopeConfig;

    }



    public function sendOnlineFormEmail ($request) {

        $data = $request->getParams();
        $this->sendEmailNew( $data );

    }


    public function sendEmailNew($data)
    {
        $emailTempVariables = array();
        foreach($data as $name => $val) {
            $emailTempVariables[$name] = $val;
        }

        $templateId = $this->scopeConfig->getValue('sms_sender/general/email_template_online',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $senderEmail = $this->scopeConfig->getValue('trans_email/ident_general/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $senderName  = $this->scopeConfig->getValue('trans_email/ident_general/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);


        $email = empty($data['your-email']) ? 'test@mail.com' : $data['your-email'];
        $email2 = $this->scopeConfig->getValue('trans_email/ident_sales/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);



        $sender = ['name' => $senderName,'email' => $senderEmail];

        $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)->setTemplateOptions(['area' =>\Magento\Framework\App\Area::AREA_FRONTEND,'store'=> \Magento\Store\Model\Store::DEFAULT_STORE_ID])
            ->setTemplateVars($emailTempVariables)
            ->setFrom($sender)
            ->addTo($email)
            ->addCc($email2)
            ->setReplyTo($senderEmail)
            ->getTransport();
        $transport->sendMessage();

    }

}