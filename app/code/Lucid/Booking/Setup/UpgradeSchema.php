<?php

namespace Lucid\Booking\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {


        $setup->startSetup();


        if (version_compare($context->getVersion(), '1.0.2') < 0) {


            $tableName = $setup->getTable('lucid_booking_calendar');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();


                $connection->addColumn(
                    $tableName,
                    'websites_json',
                    ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'nullable' => false, 'comment' => 'websites_json']
                );

            }

        }

        if (version_compare($context->getVersion(), '1.0.5') < 0) {

            $ep1_data_table = $setup->getConnection()->newTable($setup->getTable('ep1_data_table'));


            $ep1_data_table->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
                'Entity ID'
            );


            $ep1_data_table->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Name'
            );



            $ep1_data_table->addColumn(
                'data_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                1500,
                [],
                'Data in text'
            );

            $ep1_data_table->addColumn(
                'data_text_blob',
                \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                1500,
                [],
                'Data in blob'
            );

            $ep1_data_table->addColumn(
                'data_text_varbinary',
                \Magento\Framework\DB\Ddl\Table::TYPE_VARBINARY,
                1500,
                [],
                'Data in varbinary'
            );



            $ep1_data_table->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Product relation'
            );


            $ep1_data_table->addColumn(
                'date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                255,
                [],
                'Date Creation'
            );

            $setup->getConnection()->createTable($ep1_data_table);
        }


        if (version_compare($context->getVersion(), '1.0.6') < 0) {

            $ep1_order_data = $setup->getConnection()->newTable($setup->getTable('ep1_order_data'));


            $ep1_order_data->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
                'Entity ID'
            );


            $ep1_order_data->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Order ID'
            );

            $ep1_order_data->addColumn(
                'customer_7path',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                1500,
                [],
                'Customer 7Path'
            );


            $ep1_order_data->addColumn(
                'step_1_json',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                1500,
                [],
                'Step 1 in json'
            );

            $ep1_order_data->addColumn(
                'step_2_json',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                1500,
                [],
                'Step 2 in json'
            );

            $ep1_order_data->addColumn(
                'step_3_json',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                1500,
                [],
                'Step 3 in json'
            );

            $ep1_order_data->addColumn(
                'step_4_json',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                1500,
                [],
                'Step 4 in json'
            );


            $setup->getConnection()->createTable($ep1_order_data);
        }


        if (version_compare($context->getVersion(), '1.0.11') < 0) {

            $ep1_order_data = $setup->getTable('ep1_order_data');


            $connection = $setup->getConnection();

            $columns = [
                'blob_data' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                    'nullable' => false,
                    'comment' => 'ep1_in_blob',
                ],

                'varbinary_data' => [
                    'type' => 'varbinary',
                    'nullable' => false,
                    'comment' => 'EP1 in Varbinary',

                ]

            ];

                foreach ($columns as $name => $definition) {
                    $connection->addColumn($ep1_order_data, $name, $definition);
                }



        }


        if (version_compare($context->getVersion(), '1.0.13') < 0) {

            $ep1_order_data = $setup->getTable('lucid_booking_calendar');


            $connection = $setup->getConnection();

            $columns = [
                'DaysTo' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'nullable' => false,
                    'comment' => 'Days To',
                ],

            ];

                foreach ($columns as $name => $definition) {
                    $connection->addColumn($ep1_order_data, $name, $definition);
                }



        }


        if (version_compare($context->getVersion(), '1.0.14') < 0) {

            $lucid_booked_website = $setup->getConnection()->newTable($setup->getTable('lucid_booked_websites'));


            $lucid_booked_website->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
                'Entity ID'
            );


            $lucid_booked_website->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Order ID'
            );

            $lucid_booked_website->addColumn(
                'website',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Website'
            );

            $lucid_booked_website->addColumn(
                'subpage',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Subpage'
            );

            $lucid_booked_website->addColumn(
                'booked_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                1500,
                [],
                'Booked Date'
            );


            $setup->getConnection()->createTable($lucid_booked_website);
        }

        if (version_compare($context->getVersion(), '1.0.15') < 0) {

            $ep1_order_data = $setup->getTable('ep1_order_data');


            $connection = $setup->getConnection();

            $columns = [
                'email_sms_time' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'email_sms_time',
                ],

            ];

            foreach ($columns as $name => $definition) {
                $connection->addColumn($ep1_order_data, $name, $definition);
            }



        }

        if (version_compare($context->getVersion(), '1.0.16') < 0) {

            $calendar_days_schedule = $setup->getConnection()->newTable($setup->getTable('calendar_days_schedule'));


            $calendar_days_schedule->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
                'Entity ID'
            );


            $calendar_days_schedule->addColumn(
                'day',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'day'
            );

            $calendar_days_schedule->addColumn(
                'is_disabled',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                [],
                'Is day disabled'
            );

            $calendar_days_schedule->addColumn(
                'basis_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Basis Price'
            );

            $calendar_days_schedule->addColumn(
                'premium_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Premium Price'
            );


            $calendar_days_schedule->addColumn(
                'website_json_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Website data in json'
            );


            $setup->getConnection()->createTable($calendar_days_schedule);
        }


        if (version_compare($context->getVersion(), '1.0.21') < 0) {

            $lucid_booked_website = $setup->getTable('lucid_booked_websites');


            $connection = $setup->getConnection();



            $columns = [
                'modified_ep1_blob' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                    'nullable' => false,
                    'size' => 1500,
                    'comment' => 'Modified Ep1',
                ],

            ];

            foreach ($columns as $name => $definition) {
                $connection->addColumn($lucid_booked_website, $name, $definition);
            }

        }

        if (version_compare($context->getVersion(), '1.0.22') < 0) {


            $tableName = $setup->getTable('lucid_booking_calendar');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();


                $connection->addColumn(
                    $tableName,
                    'days_in_advance',
                    ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'nullable' => false, 'comment' => 'days_in_advance']
                );

            }

        }

        if (version_compare($context->getVersion(), '1.0.23') < 0) {

            $lucid_booked_website = $setup->getTable('lucid_booked_websites');


            $connection = $setup->getConnection();



            $columns = [
                'timestamp' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Time Stamp',
                ],
                'session_identificator' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Session Identificator',
                ],

            ];

            foreach ($columns as $name => $definition) {
                $connection->addColumn($lucid_booked_website, $name, $definition);
            }

        }

        if (version_compare($context->getVersion(), '1.0.26') < 0) {

            $connection = $setup->getConnection();
            $lucid_booked_website = $setup->getTable('lucid_booked_websites');
//            $ep1_data_table = $setup->getTable('ep1_order_data');

            $connection->truncateTable($lucid_booked_website);
//            $connection->truncateTable($ep1_data_table);

        }

	    if (version_compare($context->getVersion(), '1.0.27') < 0) {

		    $ep1_order_data = $setup->getTable('ep1_order_data');

		    $setup->getConnection()->dropColumn($ep1_order_data, 'email_sms_time');

		    $connection = $setup->getConnection();
		    $columns = [
			    'email_sms_time' => [
				    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				    'nullable' => true,
				    'comment' => 'email_sms_time',
			    ],
		    ];

		    foreach ($columns as $name => $definition) {
			    $connection->addColumn($ep1_order_data, $name, $definition);
		    }

	    }

		if (version_compare($context->getVersion(), '1.0.28') < 0) {
			$ep1_order_data = $setup->getTable('ep1_order_data');

			$setup->getConnection()->dropColumn($ep1_order_data, 'sending_status');

			$connection = $setup->getConnection();
			$columns = [
				'sending_status' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'size' => 255,
					'comment' => 'Sending Status SMS\Email',
				],
			];

			foreach ($columns as $name => $definition) {
				$connection->addColumn($ep1_order_data, $name, $definition);
			}

		}

        $setup->endSetup();
    }
}