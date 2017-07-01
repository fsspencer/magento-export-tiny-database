<?php

require_once 'abstract.php';
ini_set('display_errors', 1);

class Codealist_Shell_GenerateTinyDump extends Mage_Shell_Abstract
{

    /**
     * Runs the process
     */
    public function run()
    {
        $db = $this->getDbInfo();
        $fileNameSchema = $db['name'] . '-schema-' . date('j-m-y-h-i-s') . '.sql';
        $fileNameData = $db['name'] . '-data-' . date('j-m-y-h-i-s') . '.sql';

        $dumpSchemaCmd = $this->getDumpCommand($fileNameSchema, true);
        $dumpDataCmd = $this->getDumpCommand($fileNameData, false);

        $fp = fopen(realpath(dirname(__FILE__)) . "/dump_data.sh", "w");
        fputs($fp, "#!/bin/bash\n\n");
        fputs($fp, $dumpSchemaCmd."\n\n");
        fputs($fp, $dumpDataCmd."\n\n");

        $tarFile = $db['name'] . '-' . date('j-m-y-h-i-s') . '.gz';
        fputs($fp, 'tar cfz ' . $tarFile . ' ' . $fileNameSchema . ' ' . $fileNameData . "\n\n");
        fputs($fp, 'rm ' . $fileNameSchema . ' ' . $fileNameData);
        fclose($fp);

    }

    public function getDumpCommand($fileName, $noData = false)
    {
        $db = $this->getDbInfo();
        $dumpSchema = 'mysqldump' . ' ';
        if ($noData) {
            $dumpSchema .= '--no-data' . ' ';
        } else {
            $dumpSchema .= $this->getIgnoreTables();
        }
        $dumpSchema .= '-u ' . $db['user'] . ' ';
        $dumpSchema .= '--password="' . $db['pass'] . '" ';
        $dumpSchema .= '-h ' . $db['host'] . ' ';
        if (isset($db['port']) || $db['port'] == '') $dumpSchema .= '--port=' . $db['port'] . ' ';
        $dumpSchema .= $db['name'] .' > ' . $fileName;

        return $dumpSchema;
    }

    public function getDbInfo()
    {
        $config  = Mage::getConfig()->getResourceConnectionConfig("default_setup");

        $db = array(
            'host' => (string)$config->host,
            'user' => (string)$config->username,
            'pass' => (string)$config->password,
            'name' => (string)$config->dbname,
            'pref' => Mage::getConfig()->getTablePrefix()
        );

        $db['port'] = $this->getDatabasePort($db);

        return $db;
    }

    public function getDatabasePort($db)
    {
        $port = '3306';
        if ($hostArr = explode($db['host'], ':')) {
            $port = $hostArr[1];
            $db['host'] = $hostArr[0];
        }
        return $port;
    }

    public function getIgnoreTables()
    {
        $tables = array(
            'adminnotification_inbox',
            'aw_core_logger',
            'dataflow_batch_export',
            'dataflow_batch_import',
            'log_customer',
            'log_quote',
            'log_summary',
            'log_summary_type',
            'log_url',
            'log_url_info',
            'log_visitor',
            'log_visitor_info',
            'log_visitor_online',
            'index_event',
            'report_event',
            'report_viewed_product_index',
            'report_compared_product_index',
            'catalog_compare_item',
            'catalogindex_aggregation',
            'catalogindex_aggregation_tag',
            'catalogindex_aggregation_to_tag'
        );

        if ($this->getArg('ignore-orders')) {
            $tables = array_merge($tables, array(
                    'sales_flat_creditmemo',
                    'sales_flat_creditmemo_comment',
                    'sales_flat_creditmemo_grid',
                    'sales_flat_creditmemo_item',
                    'sales_flat_invoice',
                    'sales_flat_invoice_comment',
                    'sales_flat_invoice_grid',
                    'sales_flat_invoice_item',
                    'sales_flat_order',
                    'sales_flat_order_address',
                    'sales_flat_order_grid',
                    'sales_flat_order_item',
                    'sales_flat_order_payment',
                    'sales_flat_order_shipping_rate',
                    'sales_flat_order_status_history',
                    'sales_flat_quote',
                    'sales_flat_quote_address',
                    'sales_flat_quote_address_item',
                    'sales_flat_quote_item',
                    'sales_flat_quote_item_option',
                    'sales_flat_quote_payment',
                    'sales_flat_quote_shipping_rate',
                    'sales_flat_shipment',
                    'sales_flat_shipment_comment',
                    'sales_flat_shipment_grid',
                    'sales_flat_shipment_item',
                    'sales_flat_shipment_track',
                    'sales_invoiced_aggregated',
                    'sales_invoiced_aggregated_order',
                    'sales_order_aggregated_created',
                    'sales_order_aggregated_updated',
                    'sales_order_status',
                    'sales_order_status_label',
                    'sales_order_status_state',
                    'sales_order_tax',
                    'sales_order_tax_item',
                    'sales_payment_transaction',
                    'sales_recurring_profile',
                    'sales_recurring_profile_order',
                    'sales_refunded_aggregated',
                    'sales_refunded_aggregated_order',
                    'sales_shipping_aggregated',
                    'sales_shipping_aggregated_order')
            );
        }

        if ($this->getArg('ignore-url-rewrites')) {
            $tables = array_merge($tables, array('core_url_rewrite'));
        }

        if ($this->getArg('ignore-flat-catalog')) {

            /** @var Mage_Core_Model_Store $store */
            foreach (Mage::app()->getStores() as $store) {
                if ($store->getId() > 0) {
                    $tables = array_merge($tables, array(
                        'catalog_category_flat_store_' . $store->getId()),
                        'catalog_product_flat_' . $store->getId()
                    );
                }
            }

        }

        $ignoreTables = ' ';
        $db = $this->getDbInfo();
        foreach($tables as $table) {
            $ignoreTables .= '--ignore-table=' . $db['name'] . '.' . $db['pref'] . $table . ' ';
        }

        return $ignoreTables;
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f generate.php -- [options]

  --ignore-orders            Ignore Orders
  --ignore-flat-catalog            Ignore Flat Catalog
  --ignore-url-rewrites            Ignore URL Rewrites
  help                                    This help


USAGE;
    }


}

$shell = new Codealist_Shell_GenerateTinyDump();
$shell->run();
