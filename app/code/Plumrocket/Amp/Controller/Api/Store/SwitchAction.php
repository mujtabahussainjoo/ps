<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Controller\Api\Store;

use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\StoreIsInactiveException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreSwitcherInterface;
use Plumrocket\Amp\Model\Result\AmpJson;
use Plumrocket\Amp\Model\Result\AmpJsonFactory;
use Magento\Store\Model\Store;

class SwitchAction extends \Magento\Store\Controller\Store\SwitchAction
{
    /**
     * @var AmpJsonFactory
     */
    private $ampResultFactory;

    /**
     * SwitchAction constructor.
     *
     * @param ActionContext                               $context
     * @param StoreCookieManagerInterface                 $storeCookieManager
     * @param HttpContext                                 $httpContext
     * @param StoreRepositoryInterface                    $storeRepository
     * @param StoreManagerInterface                       $storeManager
     * @param AmpJsonFactory $ampResultFactory
     */
    public function __construct(
        ActionContext $context,
        StoreCookieManagerInterface $storeCookieManager,
        HttpContext $httpContext,
        StoreRepositoryInterface $storeRepository,
        StoreManagerInterface $storeManager,
        AmpJsonFactory $ampResultFactory
    ) {
        parent::__construct(
            $context,
            $storeCookieManager,
            $httpContext,
            $storeRepository,
            $storeManager
        );
        $this->ampResultFactory = $ampResultFactory;
    }

    /**
     * @return AmpJson
     */
    public function execute() : AmpJson
    {
        $ampJsonResult = $this->ampResultFactory->create();

        $currentActiveStore = $this->storeManager->getStore();

        $targetStoreCode = $this->_request->getParam(
            '___store',
            $this->storeCookieManager->getStoreCodeFromCookie()
        );
        $fromStoreCode = $this->_request->getParam('___from_store');

        $error = null;
        try {
            $fromStore = $this->storeRepository->get($fromStoreCode);
            $targetStore = $this->storeRepository->getActiveStoreByCode($targetStoreCode);
        } catch (StoreIsInactiveException $e) {
            $error = __('Requested store is inactive');
        } catch (NoSuchEntityException $e) {
            $error = __("The store that was requested wasn't found. Verify the store and try again.");
        }
        if ($error !== null) {
            $ampJsonResult->addErrorMessage($error);
        } else {
            $defaultStoreView = $this->storeManager->getDefaultStoreView();
            if ($defaultStoreView->getId() == $targetStore->getId()) {
                $this->storeCookieManager->deleteStoreCookie($targetStore);
            } else {
                $this->httpContext->setValue(Store::ENTITY, $targetStore->getCode(), $defaultStoreView->getCode());
                $this->storeCookieManager->setStoreCookie($targetStore);
            }
            if ($targetStore->isUseStoreInUrl()) {
                // Change store code in redirect url
                if (strpos($this->_redirect->getRedirectUrl(), $currentActiveStore->getBaseUrl()) !== false) {
                    $redirectUrl = str_replace(
                        $currentActiveStore->getBaseUrl(),
                        $targetStore->getBaseUrl(),
                        $this->_redirect->getRedirectUrl()
                    );
                } else {
                    $redirectUrl = $targetStore->getBaseUrl();
                }
            } else {
                $redirectUrl = $this->_redirect->getRedirectUrl();
            }

            $ampJsonResult->setFormRedirect($redirectUrl);
        }

        return $ampJsonResult;
    }
}
