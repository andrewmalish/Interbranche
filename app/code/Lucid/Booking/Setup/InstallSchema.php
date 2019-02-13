<?php


namespace Lucid\Booking\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_lucid_booking_produkt = $setup->getConnection()->newTable($setup->getTable('lucid_booking_produkt'));


        $table_lucid_booking_produkt->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );


        $table_lucid_booking_produkt->addColumn(
            'Name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Name'
        );
        

        
        $table_lucid_booking_produkt->addColumn(
            'Picture',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Picture'
        );
        

        
        $table_lucid_booking_produkt->addColumn(
            'Salutation',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Salutation'
        );
        

        
        $table_lucid_booking_produkt->addColumn(
            'Content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Content'
        );
        

        
        $table_lucid_booking_produkt->addColumn(
            'width',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'width'
        );
        

        
        $table_lucid_booking_produkt->addColumn(
            'height',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'height'
        );
        

        
        $table_lucid_booking_produkt->addColumn(
            'alignment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'alignment'
        );
        

        
        $table_lucid_booking_produkt->addColumn(
            'sender',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'sender'
        );
        

        $table_lucid_booking_calendar = $setup->getConnection()->newTable($setup->getTable('lucid_booking_calendar'));

        
        $table_lucid_booking_calendar->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        

        $table_lucid_booking_calendar->addColumn(
            'Price1',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Price1'
        );
        

        
        $table_lucid_booking_calendar->addColumn(
            'Price2',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Price2'
        );
        

        
        $table_lucid_booking_calendar->addColumn(
            'Level',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Level'
        );
        

        
        $table_lucid_booking_calendar->addColumn(
            'Days',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            [],
            'Days'
        );
        

        
        $table_lucid_booking_calendar->addColumn(
            'Website',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Website'
        );
        

        
        $table_lucid_booking_calendar->addColumn(
            'website_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            [],
            'website_active'
        );
        


        $setup->getConnection()->createTable($table_lucid_booking_calendar);

        $setup->getConnection()->createTable($table_lucid_booking_produkt);

        $setup->endSetup();
    }
}
