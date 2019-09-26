<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.146
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoContent\Setup\UpgradeData;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Api\Repository\TemplateRepositoryInterface;

class UpgradeData101 implements UpgradeDataInterface
{
    private $templateRepository;

    public function __construct(
        TemplateRepositoryInterface $templateRepository
    ) {
        $this->templateRepository = $templateRepository;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        foreach ($this->templateRepository->getCollection() as $template) {
            $conditions = $template->getData(TemplateInterface::CONDITIONS_SERIALIZED);

            try {
                \Zend_Json::decode($conditions);
            } catch (\Exception $e) {
                $conditions = \Zend_Json::encode(@unserialize($conditions));
            }

            $replaces = [
                "Mirasvit\\\\SeoContent\\\\Model\\\\Template\\\\Rule\\\\Condition\\\\Validate" =>
                    "Mirasvit\\\\SeoContent\\\\Model\\\\Template\\\\Rule\\\\Condition\\\\CategoryCondition",

                "Mirasvit\\\\Seo\\\\Model\\\\Template\\\\Rule" =>
                    "Mirasvit\\\\SeoContent\\\\Model\\\\Template\\\\Rule",
            ];

            foreach ($replaces as $from => $to) {
                $conditions = str_replace($from, $to, $conditions);
            }

            $template->setConditionsSerialized($conditions);
            $this->templateRepository->save($template);
        }
    }
}