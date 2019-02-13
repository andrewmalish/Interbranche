<?php


namespace Lucid\Booking\Controller\Index;

use Lucid\Booking\Model\LucidBookedWebsiteFactory;


class SessionData extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $_assetRepo;
    protected $_filesystem;
    private $_ep1StepsSession;
    private $_lucidBookedWebsiteFactory;
    protected $_session;
    protected $checkoutSession;
    protected $checkoutCart;
    protected $customerSession;


    public function __construct(

        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        LucidBookedWebsiteFactory $lucidBookedWebsiteFactory,
        \Magento\Checkout\Model\Cart $checkoutCart,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Lucid\Booking\Api\Data\Ep1StepsSessionInterface $ep1StepsSession
    )
    {
        $this->_ep1StepsSession = $ep1StepsSession;
        $this->checkoutCart = $checkoutCart;
        $this->checkoutSession = $checkoutSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_lucidBookedWebsiteFactory = $lucidBookedWebsiteFactory;
        $this->customerSession = $customerSession;

        parent::__construct($context);
        $this->getFileSystem();


    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $params = $this->getRequest()->getParams();

        if (isset ($params['action'])) {
            $action = $params['action'];

            switch ($action) {


                case 'clearSessionData':
                    $this->clearCustomerSession();
                    echo 'ok';
                    break;

                case 'store':
                    if (isset($params['data'])) {

                        if (is_array($params['data'])) {
                            $this->storeCustomerSession($params['data']);
                        } else {
                            $serializeData = array();
                            parse_str($params['data'], $serializeData);

                            $this->storeCustomerSession($serializeData);
                        }

                    }
                    break;

                case 'final':

                    if (isset($params['data'])) {

                        if (is_array($params['data'])) {
                            $this->storeCustomerSession($params['data']);
                        } else {
                            $serializeData = array();
                            parse_str($params['data'], $serializeData);

                            $this->storeCustomerSession($serializeData);
                        }

                    }

                    if ($params['json'] == 'true') {

                        print_r(json_encode($this->collectSessionData()));

                    } else {
                        var_dump($this->collectSessionData());
                    }


                    break;

                default:

                    break;

            }


        }

        exit;
    }

    public function checkBadWords($words)
    {

        $path = $this->_filesystem->getDirectoryRead(
            'media'
        )->getAbsolutePath(
            'badwords/'
        );

        $path = $path . 'badwords.json';

        $handle = fopen($path, "r");
        $contents = fread($handle, filesize($path));
        fclose($handle);
        $badwordsList = json_decode($contents);

        foreach ($badwordsList as $badwords) {

            foreach ($badwords as $badword) {

                $allWords = preg_replace("/\r\n/", " ", $words['text']);
                $allWords = explode(' ', $allWords);

                foreach ($allWords as $word) {
                    $bad = strtolower($badword);
                    $needle = strtolower(trim($word));
                    if ($bad === $needle) {
                        return $badword;
                    }
                }

            }

        }

        return false;
    }


    public function storeCustomerSession(array $data)
    {
        $website = '';

        if (isset($data['text']) || isset($data['text2']) || isset($data['text3'])) {

            $words = array('text' => '');

            if (isset($data['text'])) {
                $words['text'] = $data['text'];
            }

            if (isset($data['text2'])) {
                if (!empty($words['text'])) {
                    $words['text'] .= ' ' . $data['text2'];
                } else {
                    $words['text'] = $data['text2'];
                }

            }

            if (isset($data['text3'])) {
                if (!empty($words['text'])) {
                    $words['text'] .= ' ' . $data['text3'];
                } else {
                    $words['text'] = $data['text3'];
                }
            }

            if (is_array($words)) {
                $isBadWord = $this->checkBadWords($words);
                if ($isBadWord !== false) {
                    print_r(json_encode(array('isBadWord' => true, 'badWord' => $isBadWord)));
                }
            }
        }

        if (isset ($data['website'])) {
            if ($website = $data['website']) {
                $website = explode('_', trim($website));
                $websiteTitle = $website[0];
                $subpage = $website[2];
                $website = $website[1];
                $day = '';
                if (isset($data['picked_date'])) {
                    $day = date('Y-m-d', strtotime($data['picked_date']));
                }

            }
        }

        foreach ($data as $key => $value) {
//            if ($key == 'address') {
//                $this->mapAccountData($data);
//            }
            if ($key == 'ep1modified') {
                $this->_ep1StepsSession->setStep1(array(
                    'ep1modified' => $value
                ));
            }
            if ($key == 'website') {
                $this->_ep1StepsSession->setCustomerEp1Session(array(
                    'websiteTitle' => $websiteTitle,
                    'website' => $website,
                    'subpage' => $subpage
                ));
                $_SESSION['customerEp1Session']['websiteTitle'] = $websiteTitle;
                $_SESSION['customerEp1Session']['website'] = $website;
                $_SESSION['customerEp1Session']['subpage'] = $subpage;
                $this->bookTemporaryWebsite($website, $subpage, $day);
            } elseif (!is_array($value) && $value != '') {
                $this->_ep1StepsSession->setCustomerEp1Session(array(
                    $key => $value
                ));
                $_SESSION['customerEp1Session'][$key] = $value;
            }
        }

        return $this->_ep1StepsSession->getCustomerEp1Session();
    }

    protected function collectSessionData()
    {

        $customerData = array();

        $customer7Path = $this->_ep1StepsSession->getCustomer7pathInfo();
        $customerEp1 = $this->_ep1StepsSession->getCustomerEp1Session();

        $customerData['oauth'] = is_array($customer7Path) ? $customer7Path['customer7pathInfo'] : '';
        $customerData['form'] = is_array($customerEp1) ? $customerEp1 : '';

        return $customerData;
    }

    protected function bookTemporaryWebsite($website, $subpage, $day)
    {

        $session = $this->customerSession->getSessionId() ? $this->customerSession->getSessionId() : '';
        try {
            $subpageDataModel = $this->_lucidBookedWebsiteFactory->create();
            $item = $subpageDataModel->getCollection()->addFieldToSelect('id')->addFieldToFilter('session_identificator', $session)->getLastItem();

            if ($id = $item->getId()) {
                $subpageDataModel->load($id);
            }

            $time = strtotime("+15 minutes", time());

            $subpageDataModel
                ->setWebsite($website)
                ->setSubpage($subpage)
                ->setBookedDate($day)
                ->setTimestamp($time)
                ->setSessionIdentificator($session)
                ->save();
        } catch (\Exception $e) {
            echo 'wrong data found ' . $e;
        }


        return true;

    }


    protected function getAssetsRepo()
    {
        return $this->_assetRepo = $this->_objectManager->create('Magento\Framework\View\Asset\Repository');
    }

    protected function getFileSystem()
    {
        return $this->_filesystem = $this->_objectManager->create('\Magento\Framework\Filesystem');
    }


    protected function mapAccountData($data)
    {

        $accountData = array();

        $userMap = array('salutation', 'username', 'first_name', 'last_name', 'nickname', 'gender', 'email', 'birthdate', 'preferred_language', 'phone_number');
        $addressMap = array('street_address', 'city', 'postal_code', 'state', 'country', 'company');

        foreach ($userMap as $single) {
            isset($data[$single]) ? $accountData[$single] = $data[$single] : '';
        }

        /*addressMap*/
        if (isset($data['address'])) {
            foreach ($addressMap as $singleAddress) {

                isset($data['address'][$singleAddress]) ? $accountData['address'][$singleAddress] = $data['address'][$singleAddress] : '';
            }
        }

        $customerObject = $this->_ep1StepsSession->getCustomer7pathInfo();

        if (is_array($customerObject['customer7pathInfo'])) {
            foreach ($accountData as $key => $value) {
                $customerObject['customer7pathInfo']->$key = $value;
//            $_SESSION['customer7pathInfo']->$key = $value;
            }
            $customerObject['customer7pathInfo']->address_set = true;
            $this->_ep1StepsSession->setCustomer7pathInfo(array('customer7pathInfo' => $customerObject));

        }

        return $accountData;
    }


    private function clearCustomerSession()
    {
        $this->_ep1StepsSession->destroyCustomerEp1Session();
        $this->_ep1StepsSession->destroyCustomerEp1();
        $this->_ep1StepsSession->destroyStep1();
//        $this->_ep1StepsSession->destroyCustomer7pathInfo();


        if (isset($_SESSION['customerEp1Session'])) {
            unset($_SESSION['customerEp1Session']);
        }

//        $quote = $this->checkoutSession->getQuote();
//        $this->checkoutSession->clearQuote();
//        $quote->removeAllItems();
//        $quote->save();
//        $this->checkoutCart->truncate()->saveQuote();

        $this->checkoutCart->truncate()->save();
        $this->checkoutCart->getQuote()->clearInstance();

        $quote = $this->checkoutCart->getQuote();
        $quoteItems = $quote->getItemsCollection();

        foreach($quoteItems as $item)
        {
            $this->checkoutCart->removeItem($item->getId())->save();
        }
        $this->checkoutCart->saveQuote();
        $this->checkoutCart->save();
        return;
    }

}
