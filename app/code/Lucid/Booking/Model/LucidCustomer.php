<?php

namespace Lucid\Booking\Model;

use Lucid\Booking\Api\Data\Ep1StepsSessionInterface;

class LucidCustomer
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory    $customerFactory
     */

    protected $_customer;
    protected $_customerSession;
    private   $_ep1StepsSession;




    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Ep1StepsSessionInterface $ep1StepsSession,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->storeManager     = $storeManager;
        $this->customerFactory  = $customerFactory;
        $this->_ep1StepsSession = $ep1StepsSession;
        $this->_customer = $customer;
        $this->_customerSession = $customerSession;
    }

    public function createCustomer(array $data)
    {
        // Get Website ID
        $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();

        // Instantiate object (this is the most important part)
        $customer   = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);

        $firstname = !empty($data['first_name']) ? $data['first_name'] : 'DeinGruss';
        $lastname = !empty($data['last_name']) ? $data['last_name'] : 'P7S1';

        // Preparing data for new customer
        $customer->setEmail($data['email']);
        $customer->setFirstname($firstname);
        $customer->setLastname($lastname);
//        $customer->setPassword($data['email']"password");

        // Save data
        $customer->save();
        //$customer->sendNewAccountEmail();
        $this->loginCustomer($data['email']);
    }


    public function updateCustomer($customer)
    {

        $cusomer7PathData = $this->_ep1StepsSession->getCustomer7pathInfo();

        if(is_array($cusomer7PathData)){
            $cusomer7PathData =$cusomer7PathData['customer7pathInfo'];
        }
        else{
            return;
        }

        $firstname = !empty($cusomer7PathData->first_name) ? $cusomer7PathData->first_name : 'InterBranch(default)';
        $lastname = !empty($cusomer7PathData->last_name) ? $cusomer7PathData->last_name : 'Dein(default)';
        $gender =  $cusomer7PathData->gender == 'male' ? 1 : 2 ;

   //     $customer
   //         ->setFirstname($firstname)
   //         ->setLastname($lastname)
   //         ->setGender($gender);

        // Save data
        $customer->save();


    }

    public function updateMagentoCustomer($data)
    {

        $cusomer7PathData = $this->_ep1StepsSession->getCustomer7pathInfo();

        if(is_array($cusomer7PathData)){
            $cusomer7PathData = $cusomer7PathData['customer7pathInfo'];
        }
        else{
            return;
        }

        $gender =  $data['gender'] == 'male' ? 1 : 2 ;
        $customer = $this->_customerSession->getCustomer();

        $customer
            ->setFirstname($data['first_name'])
            ->setLastname($data['last_name'])
            ->setGender($gender);
        // Save data
        $customer->save();


    }

    public function loginCustomer($email) {

        try {

            $this->_customer->setWebsiteId($this->storeManager->getWebsite()->getWebsiteId());
            $customer = $this->_customer->loadByEmail($email);
            if($customer->getId()) {
                $this->_customerSession->setCustomerAsLoggedIn($customer);
                $this->updateCustomer($customer);
                return true;
            }
            else{
                return false;
            }
        }
        catch (\Exception $e) {
            echo $e;
        }
        return false;

    }

}