<?php
/**
 * @package Adfix_Squarefeed
 * @author  Alona Tsarova
 */

namespace Adfix\Squarefeed\Setup;

use Adfix\Squarefeed\Helper\Data;
use Adfix\Squarefeed\Logger\Logger;
use Magento\Integration\Model\Integration;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Integration\Api\OauthServiceInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Integration\Api\IntegrationServiceInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var OauthServiceInterface
     */
    protected $oauthService;

    /**
     * @var IntegrationServiceInterface
     */
    protected $integrationService;

    /**
     * UpgradeData constructor.
     *
     * @param Logger $logger
     * @param OauthServiceInterface $oauthService
     * @param IntegrationServiceInterface $integrationService
     */
    public function __construct(
        Logger $logger,
        OauthServiceInterface $oauthService,
        IntegrationServiceInterface $integrationService
    ) {
        $this->logger = $logger;
        $this->oauthService = $oauthService;
        $this->integrationService = $integrationService;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $integrationData = [
                'name' => Data::API_INTEGRATION_NAME,
                'email' => Data::API_INTEGRATION_EMAIL,
                'resource' => $this->getIntegrationResources()
            ];

            try {
                /** @var Integration $integration */
                $integration = $this->integrationService->create($integrationData);
                if ($this->oauthService->createAccessToken($integration->getConsumerId(), 0)) {
                    $integration->setStatus(Integration::STATUS_ACTIVE)->save();
                }
            } catch (\Exception $e) {
                $this->logger->info('[UpgradeData] ERROR: ' . $e->getMessage());
                $this->logger->info('Error Code - ' . $e->getCode());
                $this->logger->info('Line - ' . $e->getLine() . ', ' . $e->getFile());
                $this->logger->info($e->getTraceAsString());
            }
        }

        $installer->endSetup();
    }

    /**
     * Retrieve list of resources for integration
     *
     * @return array
     */
    protected function getIntegrationResources()
    {
        return [
            'Magento_Analytics::analytics',
            'Magento_Analytics::analytics_api',
            'Magento_Sales::sales',
            'Magento_Sales::sales_operation',
            'Magento_Sales::sales_order',
            'Magento_Sales::actions',
            'Magento_Sales::actions_view',
            'Magento_Catalog::catalog',
            'Magento_Catalog::catalog_inventory',
            'Magento_Catalog::products',
            'Magento_Catalog::categories',
            'Magento_Customer::customer',
            'Magento_Customer::manage',
            'Magento_Backend::marketing',
            'Magento_CatalogRule::promo',
            'Magento_CatalogRule::promo_catalog',
            'Magento_SalesRule::quote',
            'Magento_Reports::report',
            'Magento_Reports::report_marketing',
            'Magento_Reports::shopcart',
            'Magento_Reports::product',
            'Magento_Reports::abandoned',
            'Magento_Reports::report_search',
            'Magento_Newsletter::problem',
            'Magento_Reports::review',
            'Magento_Reports::review_customer',
            'Magento_Reports::review_product',
            'Magento_Reports::salesroot',
            'Magento_Reports::salesroot_sales',
            'Magento_Reports::customers',
            'Magento_Reports::totals',
            'Magento_Reports::customers_orders',
            'Magento_Reports::accounts',
            'Magento_Reports::report_products',
            'Magento_Reports::viewed',
            'Magento_Reports::bestsellers',
            'Magento_Reports::statistics',
            'Magento_Reports::statistics_refresh',
            'Magento_Backend::store',
            'Magento_Backend::stores',
            'Magento_Backend::stores_settings',
            'Magento_Config::config',
            'Magento_GoogleAnalytics::google',
            'Magento_Contact::contact',
            'Magento_Shipping::carriers',
            'Magento_Config::config_general',
            'Magento_Config::web',
            'Magento_Tax::config_tax',
            'Magento_SalesRule::config_promo',
            'Magento_Config::currency',
            'Magento_Tax::manage_tax',
            'Magento_CurrencySymbol::system_currency',
            'Magento_CurrencySymbol::currency_rates',
            'Magento_CurrencySymbol::symbols',
            'Magento_Backend::stores_attributes',
            'Magento_Catalog::attributes_attributes',
            'Magento_Catalog::sets',
            'Adfix_Squarefeed::squarefeed'
        ];
    }
}
