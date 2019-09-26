<?php
namespace Serole\CallForPrice\Controller\Adminhtml\Post;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Serole\CallForPrice\Model\CallForPrice;
use Serole\CallForPrice\Model\CallForPriceFactory;
use RuntimeException;

class InlineEdit extends Action
{
    public $jsonFactory;
    public $callforpriceFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CallForPriceFactory $callforpriceFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->callforpriceFactory = $callforpriceFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $rewardItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($rewardItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error'    => true,
            ]);
        }
        $key = array_keys($rewardItems);
        $rewardId = !empty($key) ? (int) $key[0] : '';

        $reward = $this->callforpriceFactory->create()->load($rewardId);
        try {
            $rewardData = $rewardItems[$rewardId];
            $reward->addData($rewardData);
            $reward->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithRewardId($reward, $e->getMessage());
            $error = true;
        } catch (RuntimeException $e) {
            $messages[] = $this->getErrorWithRewardId($reward, $e->getMessage());
            $error = true;
        } catch (Exception $e) {
            $messages[] = $this->getErrorWithRewardId(
                $reward,
                __('Something went wrong while saving the Refunf Status.')
            );
            $error = true;
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error'    => $error
        ]);
    }

    public function getErrorWithRewardId(CallForPrice $reward, $errorText)
    {
        return '[Status : ' . $reward->getId() . '] ' . $errorText;
    }
}
