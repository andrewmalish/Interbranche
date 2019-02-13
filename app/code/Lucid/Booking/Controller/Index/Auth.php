<?php



namespace Lucid\Booking\Controller\Index;
use Lucid\Booking\Api\Data\Ep1StepsSessionInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;



class Auth extends \Magento\Framework\App\Action\Action
{


    protected $resultPageFactory;

    protected $lucidCustomer;

    protected  $config = [
        'environment' => 'qa',
        'host' => 'https://op.qa.7pass.ctf.prosiebensat1.com',
        'api_host' => 'https://sso.qa.7pass.ctf.prosiebensat1.com',
        'use_form_params' => true,
        'client_id' => '5ad8a6dc9cd7d9c67ba11380',
        'client_secret' => 'gJZgD83oqQWjJi7BkEa55vEbzLSxnjatLfaSUt1MXTx5OpQva8/57L2XW/C5suYy',
        'post_logout_redirect_uri' => 'http://contribute.magento2.local/booking/index/auth/action/callback',
        'redirect_uri' => 'http://contribute.magento2.local/booking/index/auth/action/callback'
    ];

    protected $sso;
    protected $ssoConfig;
    protected $scopeConfig;
    protected $_storeManager;
    private   $_ep1StepsSession;
    private   $customer7pathSession;
    protected $customerSession;
    protected $_addressFactory;
    protected $_customerRepositoryInterface;
    protected $subscriberFactory;







    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\Session $customerSession,
        Ep1StepsSessionInterface $ep1StepsSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Lucid\Booking\Model\LucidCustomer $lucidCustomer,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory


    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->_ep1StepsSession = $ep1StepsSession;
        $this->lucidCustomer = $lucidCustomer;
        $this->subscriberFactory= $subscriberFactory;
        $this->_addressFactory = $addressFactory;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerSession = $customerSession;
        $this->_storeManager = $storeManager;


        $this->setConfig();
        $this->getSsoConfig();
        $this->getSso();

    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {


        set_exception_handler(function($e) {
            if($e instanceof \P7\SSO\Exception\ApiException && $e->getCode() === 401) {
                $this->_ep1StepsSession->destroyCustomer7pathInfo();
//                unset($_SESSION['tokens']);
            }

            $this->_redirect('/');

            exit;
        });

        $action = $this->getRequest()->getParam('action');

        switch ($action) {
            case 'login':

                $this->authenticatedRedirect();
                $this->_ep1StepsSession->setCustomer7pathInfo(array('back_refferer_url' => ''));
                $this->_ep1StepsSession->setCustomer7pathInfo(array('state' => md5(uniqid(rand(), true))));
                if(isset($_SERVER['HTTP_REFERER'])) {
                    $this->_ep1StepsSession->setCustomer7pathInfo(array('back_refferer_url' => $_SERVER['HTTP_REFERER']));
                }


                $uri = $this->sso->authorization()->authorizeUri([
                    'redirect_uri' => $this->config['redirect_uri'],
                    'state' => $this->getSessionData('state'),
                    'prompt' => 'login consent',
                    'scope' => $this->sso->authorization()->getConfig()->default_scopes . ' offline_access'
                ]);


                header('Location: ' . $uri);
                exit;

            case 'subscribe':
                $this->addCustomerToNewsletter();
                break;

            case 'callback':
                $this->authenticatedRedirect();

                try {
                    $tokens = $this->sso->authorization()->callback($this->config['redirect_uri'], $_GET, $this->getSessionData('state'));
                    $this->_ep1StepsSession->setCustomer7pathInfo(array('tokens' => $tokens->getArrayCopy()));
                } catch(AuthorizeCallbackException $e) {
                    $error = $e->getError();
                    $errorDescription = $e->getMessage();



                    $this->_redirect('/');
                    break;
                }

                $this->getAccount();
                break;

            case 'account':
                $this->getAccount();
                break;

            case 'update':

                $accountData = $this->getRequest()->getParam('data');

                parse_str($accountData,$serializeData);

                if(isset($serializeData) && is_array($serializeData)) {

                    $this->updateAccount($serializeData);

                }
                break;

        }

    }

    protected function mapAccountData ($data) {

        $accountData = array();

        $userMap = array('salutation','username','first_name', 'last_name', 'nickname','gender', 'email', 'birthdate','preferred_language','phone_number');
        $addressMap = array('street_address','city','postal_code','state','country');

        foreach($userMap as $single){
            isset($data[$single]) ? $accountData[$single] = $data[$single] : '' ;
        }

        /*addressMap*/
        if(isset($data['address'])){
            foreach ($addressMap as $singleAddress) {

                isset($data['address'][$singleAddress]) ? $accountData['address'][$singleAddress] = $data['address'][$singleAddress] : '' ;
            }
        }

        $customerObject = $this->_ep1StepsSession->getCustomer7pathInfo();

        if (isset($customerObject['customer7pathInfo'])) {
            foreach ($accountData as $key => $value) {
                $customerObject['customer7pathInfo']->$key = $value;
            }
            $customerObject['customer7pathInfo']->address_set = true;
            $this->_ep1StepsSession->setCustomer7pathInfo(array('customer7pathInfo' => $customerObject));

        }

        return $accountData;
    }


    public function updateAccount($updateData){


//        $tokens = $this->getSessionTokens(true);
//
//        if($tokens->isAccessTokenExpired()) {
//            $tokens = $this->sso->authorization()->refresh($tokens);
//            $this->_ep1StepsSession->setCustomer7pathInfo(array('tokens' => $tokens->getArrayCopy()));
//        }
//
//        $apiConfiguration = [
//            'environment' => $this->config['environment'],
//            'host' => $this->config['api_host'],
//            'client_id' => $this->config['client_id'],
//            'client_secret' => 'not really used'
//        ];
//
//        $apiConfig = new \P7\SSO\Configuration($apiConfiguration);
//        $api = new \P7\SSO($apiConfig);
//
//        $accountClient = $api->accountClient($tokens);
//


        if (isset($updateData['address'])) {
                $this->updateDefaultBillingAddress($updateData);
        }



//        $customer = array(
//            'email' =>$updateData['email'],
//            'first_name' =>$updateData['first_name'],
//            'last_name' =>$updateData['last_name'],
//        );

/*        if (!$this->lucidCustomer->loginCustomer($updateData['email'])){
            $this->lucidCustomer->createCustomer($customer);
        }
*/
//        $me = $accountClient->put('me/data', $updateData);

//        return $this->lucidCustomer->updateMagentoCustomer($updateData);
        return true;

        //echo $me;
    }


    private function updateDefaultBillingAddress($data)
    {

        try {
            $customerSession = $this->customerSession->getCustomer();
            $billingAddress = $customerSession->getDefaultBillingAddress();
            if (!$billingAddress) {
                $billingAddress =$this->_addressFactory->create()
                ->setIsDefaultShipping('1')
                ->setIsDefaultBilling('1')
                ->setCustomerId($customerSession->getId());
            }

            if(isset($data['first_name']) || isset($data['last_name'])) {
                $customer = $this->_customerRepositoryInterface->getById($customerSession->getId());
                if(isset($data['first_name'])) {
              //      $customerSession->setFirstname($data['first_name']); //set customer First Name
                    isset($data['first_name']) ? $customer->setFirstname($data['first_name']) : false;
                    isset($data['first_name']) ? $billingAddress->setFirstname($data['first_name']) : false;
                }

                if (isset($data['last_name'])) {
            //        $customerSession->setLastname($data['last_name']); // set customer Last Name
                    $customer->setLastname($data['last_name']);
                    $billingAddress->setLastname($data['last_name']);
                }
                $gender = '3';
                if(isset($data['gender'])) {
                    $gender = ($data['gender'] === 'herr') ? '1' : '2';
                }

                $customer->setGender($gender);
                $this->_customerRepositoryInterface->save($customer);
            }

            if($billingAddress) {

                isset($data['address']['country']) ? $billingAddress->setCountryId($data['address']['country']) : false;
                isset($data['address']['city']) ? $billingAddress->setCity($data['address']['city']) : false;
                isset($data['address']['street_address']) ? $billingAddress->setStreet($data['address']['street_address']) : false;
                isset($data['address']['postal_code']) ? $billingAddress->setPostcode($data['address']['postal_code']) : false;
                isset($data['address']['company']) ? $billingAddress->setCompany($data['address']['company']) : false;

                isset($data['phone_number']) ? $billingAddress->setTelephone($data['phone_number']) : $billingAddress->setTelephone('123456789');

            }

         //   $customerSession->save();
            $billingAddress->save();

        }

        catch (\Exception $e)
        {
            var_dump($e);
            return false;
        }

        return true;
    }

    public function addCustomerToNewsletter(){
        if($this->customerSession->getCustomer()) {
            try {
                $this->subscriberFactory->create()->subscribe($this->customerSession->getCustomer()->getEmail());
            }
            catch (\Exception $e) {

            }
        }
        return true;
    }



    public function getAccount(){
        $tokens = $this->getSessionTokens(true);

        if($tokens->isAccessTokenExpired()) {
            $tokens = $this->sso->authorization()->refresh($tokens);
            $this->_ep1StepsSession->setCustomer7pathInfo(array('tokens' => $tokens->getArrayCopy()));

        }

        $apiConfiguration = [
            'environment' => $this->config['environment'],
            'host' => $this->config['api_host'],
            'client_id' => $this->config['client_id'],
            'client_secret' => 'not really used'
        ];

        $apiConfig = new \P7\SSO\Configuration($apiConfiguration);
        $api = new \P7\SSO($apiConfig);

        $accountClient = $api->accountClient($tokens);

        $me = $accountClient->get('me');

     /*   $emails = $accountClient->get('me/emails');

        $batch = $accountClient->batch([
            'getUserInfo' => 'me',
            'getConsents' => 'me/consents'
        ]);

*/

      /*  $_SESSION['all_customer_info'] = $me;*/
        /*echo 'Info about user';
        var_dump($me);
        echo '<br /> Info about user emails';
        var_dump($emails);
        echo '<br /> Some additional ingfo';
        var_dump($batch);

        die('-----------------------------');
*/
        /*$ssoClient = $this->sso->accountClient($tokens);*/



        $this->_ep1StepsSession->setCustomer7pathInfo(array('customer7pathInfo' => $me));
//        $_SESSION['customer7pathInfo'] = $me;

        $customer = array(
            'email' =>$me->email,
            'first_name' =>$me->first_name,
            'last_name' =>$me->last_name,
        );

        if (!$this->lucidCustomer->loginCustomer($me->email)){
            $this->lucidCustomer->createCustomer($customer);
        }

        if($this->getSessionData('back_refferer_url')!='') {
            $this->_redirect($this->getSessionData('back_refferer_url').'?auth=true');
            return true;
        }
        else {

            $this->_storeManager->getStore()->getBaseUrl();

            $this->_redirect('/');
            return true;
        }

    }

    public function getSessionTokens($throws = false) {
        if($throws && empty($this->getSessionData('tokens'))) {
            throw new \Exception("No session tokens");
        }

        return empty($this->getSessionData('tokens')) ? null : new \P7\SSO\TokenSet($this->getSessionData('tokens'));
    }

    public function authenticatedRedirect() {
        $tokens = $this->getSessionTokens();

        if(!$tokens) {
            return false;
        }

        return true;

    }

    protected function getSsoConfig(){
        return  $this->ssoConfig = ($this->ssoConfig) ? $this->ssoConfig :  new \P7\SSO\Configuration($this->config);
    }

    protected function getSso(){
        return $this->sso = ($this->sso) ? $this->sso : new \P7\SSO($this->ssoConfig);
    }


    protected function setConfig(){
        $this->config = array('error'=>'config not Enabled');

        if($this->getAdminConfigSetting('enable')) {
            $this->config = [
                'environment' => $this->getEnvironment(),
                'host' => $this->getAdminConfigSetting('host'),
                'api_host' => $this->getAdminConfigSetting('api_host'),
                'use_form_params' => true,
                'client_id' => $this->getAdminConfigSetting('client_id'),
                'client_secret' => $this->getAdminConfigSetting('client_secret'),
                'post_logout_redirect_uri' => $this->getAdminConfigSetting('post_logout_redirect_uri'),
                'redirect_uri' => $this->getAdminConfigSetting('redirect_uri'),
            ];
        }
        return $this->config;
    }

    protected function getAdminConfigSetting($path) {
      return  $this->scopeConfig->getValue('epone/general/'.$path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }


    protected function getEnvironment(){
        return $this->scopeConfig->getValue('epone/general/qa_environment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) ? 'qa' : 'prod';
    }


 /*   protected function getStoreManager() {

        return $this->_storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');

    }

    protected function getLucidCustomer() {
        return $this->lucidCustomer = $this->_objectManager->get('\Lucid\Booking\Model\LucidCustomer');
    }
*/
    protected function getCustomer7PathSession(){
        $data = $this->_ep1StepsSession->getCustomer7pathInfo();
        return  $this->customer7pathSession = is_array($data) ? $data : array();
    }

    protected function getSessionData($name= '') {
        $var = $this->getCustomer7PathSession();
        return isset($var[$name]) ? $var[$name] : '';
    }

}