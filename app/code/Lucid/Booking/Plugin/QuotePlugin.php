<?php
/**
 *
 *  Fisha
 *
 *  This source file is proprietary and protected by international
 *  copyright and trade secret laws. No part of this source file may
 *  be reproduced, copied, adapted, modified, distributed, transferred,
 *  translated, disclosed, displayed or otherwise used by anyone in any
 *  form or by any means without the express written authorization of
 *
 * @category    Magento2
 * @package     Fisha
 * @subpackage  PriceChecker
 * @author      Andrey Rozumny <raa@magecom.net>
 * @version     1.0.0
 *
 */

namespace Lucid\Booking\Plugin;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Braintree\Gateway\Config\PayPal\Config;
use \Magento\Customer\Model\AddressFactory;

use \Magento\Framework\Message\ManagerInterface;



/**
 * Class QuotePlugin -
 * @package Riff\ConfigurableProductStock\Plugin
 * @author  akozyr
 */
class QuotePlugin
{

    /** @var ManagerInterface */
    protected $_messageManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $_addressFactory;


    public function __construct(
        ManagerInterface $messageManager,
        Config $config,
        CartRepositoryInterface $quoteRepository

    )
    {
        $this->_messageManager = $messageManager;
        $this->config = $config;
        $this->quoteRepository = $quoteRepository;


    }


    public function afterGetBillingAddress(\Magento\Quote\Model\Quote $quote, $result) {

        $address = array('firstName' =>'11',
            'lastName' =>'12',
            'email' => 'test@mail.com',
            'billingAddress'=> array(
                'address_id' =>'55',
                'quote_id' =>'19',
                'address_type' =>'billing',
                'street' => 'test341',
                'city' => 'someCIty',
                'postcode' => '78941',
                'country_id' => 'UA',
                'telephone' => '4654',
                'same_as_billing' => '0',
                'collect_shipping_rates' => '0',
                'company' => 'somecomp')
        );


       /* if(!$result->getAddressId()) {
            $this->updateBillingAddress($result,$address);

         //   $quote->setBillingAddress($result);
            $quote->save();
            $this->quoteRepository->save($quote);
        }
*/

       return $result;
    }


    /**
     * @param \Magento\Quote\Model\Quote $subject
     * @param $result
     * @return mixed
     */



    public function afterGetItems(\Magento\Quote\Model\Quote $quote, $result)
    {
//
//        $address = array('firstName' =>'11',
//            'lastName' =>'12',
//            'email' => 'test@mail.com',
//            'billingAddress'=> array(
//            'address_id' =>'55',
//            'quote_id' =>'19',
//            'address_type' =>'billing',
//
//            'street' => 'test341',
//            'city' => 'someCIty',
//            'postcode' => '78941',
//            'country_id' => 'UA',
//            'telephone' => '4654',
//            'same_as_billing' => '0',
//            'collect_shipping_rates' => '0',
//                        'company' => 'somecomp')
//    );
//
//
////        if(!$quote->getBillingAddress()->getAddressId()) {
////            $this->updateQuote($quote, $address);
////        }


        return $result;

    }





    /**
     * Update quote data
     *
     * @param Quote $quote
     * @param array $details
     * @return void
     */
    private function updateQuote(Quote $quote, array $details)
    {
        $quote->setMayEditShippingAddress(false);
        $quote->setMayEditShippingMethod(true);

        $this->updateQuoteAddress($quote, $details);
        $this->disabledQuoteAddressValidation($quote);

        $quote->collectTotals();

        /**
         * Unset shipping assignment to prevent from saving / applying outdated data
         * @see \Magento\Quote\Model\QuoteRepository\SaveHandler::processShippingAssignment
         */
        if ($quote->getExtensionAttributes()) {
            $quote->getExtensionAttributes()->setShippingAssignments(null);
        }
        $quote->save();

        $this->quoteRepository->save($quote);
    }

    /**
     * Update quote address
     *
     * @param Quote $quote
     * @param array $details
     * @return void
     */
    private function updateQuoteAddress(Quote $quote, array $details)
    {
         $this->updateBillingAddress($quote, $details);
    }

    /**
     * Update shipping address
     * (PayPal doesn't provide detailed shipping info: prefix, suffix)
     *
     * @param Quote $quote
     * @param array $details
     * @return void
     */
    private function updateShippingAddress(Quote $quote, array $details)
    {
        $shippingAddress = $quote->getShippingAddress();

        $shippingAddress->setLastname($details['lastName']);
        $shippingAddress->setFirstname($details['firstName']);
        $shippingAddress->setEmail($details['email']);

        $shippingAddress->setCollectShippingRates(true);

        $this->updateAddressData($shippingAddress, $details['shippingAddress']);

        // PayPal's address supposes not saving against customer account
        $shippingAddress->setSaveInAddressBook(false);
        $shippingAddress->setSameAsBilling(false);
        $shippingAddress->unsCustomerAddressId();
    }

    /**
     * Update billing address
     *
     * @param Quote $quote
     * @param array $details
     * @return void
     */
    private function updateBillingAddress($billingAddress, array $details)
    {



            $this->updateAddressData($billingAddress, $details['billingAddress']);


        $billingAddress->setFirstname($details['firstName']);
        $billingAddress->setLastname($details['lastName']);
        $billingAddress->setEmail($details['email']);

        // PayPal's address supposes not saving against customer account
        $billingAddress->setSaveInAddressBook(false);
        $billingAddress->setSameAsBilling(false);
        $billingAddress->unsCustomerAddressId();
    }

    /**
     * Sets address data from exported address
     *
     * @param Address $address
     * @param array $addressData
     * @return void
     */
    private function updateAddressData(Address $address, array $addressData)
    {
        $extendedAddress = isset($addressData['extendedAddress'])
            ? $addressData['extendedAddress']
            : null;

        $address->setStreet([$addressData['street'], $extendedAddress]);
        $address->setCity($addressData['city']);
//        $address->setRegionCode($addressData['region']);
        $address->setCountryId($addressData['country_id']);
        $address->setPostcode($addressData['postcode']);

        // PayPal's address supposes not saving against customer account
        $address->setSaveInAddressBook(false);
        $address->setSameAsBilling(false);
        $address->setCustomerAddressId(null);
    }

    protected function disabledQuoteAddressValidation(Quote $quote)
    {
        $billingAddress = $quote->getBillingAddress();
        $billingAddress->setShouldIgnoreValidation(true);

        if (!$quote->getIsVirtual()) {
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setShouldIgnoreValidation(true);
            if (!$billingAddress->getEmail()) {
                $billingAddress->setSameAsBilling(1);
            }
        }
    }


}