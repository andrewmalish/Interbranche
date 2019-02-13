<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lucid\Booking\Setup;

use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Upgrade Data script
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpgradeData implements UpgradeDataInterface
{

    private $eavSetupFactory;
    private $blockFactory;

    public function __construct(
        BlockFactory $blockFactory,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory


    )
    {

        $this->eavSetupFactory = $eavSetupFactory;
        $this->blockFactory = $blockFactory;
    }


    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        if (version_compare($context->getVersion(), '0.0.17')) {

            $buttonsContent = '<ul class="social">' .
                '<li class="facebook"><a href="http://facebook.com/" target="_blank" alt="Facebook">Facbook</a> </li>' .
                '<li class="instagram"><a href="http://instagram.com/" target="_blank" alt="Instagram">Instagram</a> </li>' .
                                '</ul>';

            $cmsBlockData = [
                'title' => 'Social buttons',
                'identifier' => 'social_buttons_block',
                'content' => $buttonsContent,
                'is_active' => 1,
                'stores' => [0],
                'sort_order' => 0
            ];

            $this->blockFactory->create()->setData($cmsBlockData)->save();



        }


        if (version_compare($context->getVersion(), '0.0.19')) {

            $buttonsContent = '<strong>We use cookies to make your experience better.</strong>' .
                '<span>To comply with the new e-Privacy directive, we need to ask for your consent to set the cookies.</span>';

            $cmsBlockData = [
                'title' => 'Cookie Notice',
                'identifier' => 'cookie_notice',
                'content' => $buttonsContent,
                'is_active' => 1,
                'stores' => [0],
                'sort_order' => 0
            ];

            $this->blockFactory->create()->setData($cmsBlockData)->save();

        }

    }
}