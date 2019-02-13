<?php

namespace Lucid\Booking\Plugin\Checkout\Model\Checkout;

class LayoutProcessor
{

    protected $customerData;

    public function __construct()
    {
        $this->getCustomerData();
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    )
    {

        if (isset($this->customerData)) {

            if(isset($this->customerData->email)) {
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['customer-email']['email'] = $this->customerData->email;
            }

            /* config: checkout/options/display_billing_address_on = payment_method */
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children']
            )) {
                foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                         ['payment']['children']['payments-list']['children'] as $key => $payment) {

                    if(isset($this->customerData->first_name)) {
                        /* First Name */
                        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                        ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                        ['firstname']['value'] = $this->customerData->first_name;
                    }

                    if(isset($this->customerData->last_name)) {
                        /* Last Name */
                        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                        ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                        ['lastname']['value'] = $this->customerData->last_name;
                    }

                    if(isset($this->customerData->address)) {
                        /* Country */
                        if(isset($this->customerData->address['country'])) {
                            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                            ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                            ['country_id']['value'] = $this->customerData->address['country'];
                        }

                        if(isset($this->customerData->address['postal_code'])) {
                            /* Postal Code */
                            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                            ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                            ['postcode']['value'] = $this->customerData->address['postal_code'];
                        }

                        if(isset($this->customerData->address['street_address'])) {
                            /* Street */
                            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                            ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                            ['street']['children'][0]['value'] = $this->customerData->address['street_address'];
                        }

                        if(isset($this->customerData->address['city'])) {
                            /* City */
                            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                            ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                            ['city']['value'] = $this->customerData->address['city'];
                        }

                    }
                    if(isset($this->customerData->phone_number)) {
                        /* Phone */
                        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                        ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                        ['telephone']['value'] = $this->customerData->phone_number;
                    }



                }
            }

        }
        return $jsLayout;
    }


    protected function getCustomerData()
    {
        return  $this->customerData = isset($_SESSION['customer7pathInfo']) ? $_SESSION['customer7pathInfo'] : '';
    }

}