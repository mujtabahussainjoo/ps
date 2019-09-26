<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Serole\PdfInvoice\Model;
/**
 * Sales Order Invoice Pdf default items renderer
 */
class DefaultInvoice extends \Magento\Sales\Model\Order\Pdf\Items\Invoice\DefaultInvoice{
	public function draw(){
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = [];

        // draw Product name
        $lines[0] = [['text' => $this->string->split($item->getName(), 40, true, true),'font_size' =>8, 'feed' => 35]];
		
		// draw Stockloc
		// $objManager = \Magento\Framework\App\ObjectManager::getInstance();
		// $productId = $item->getProductId();
		// $productObject = $objManager->create('Magento\Catalog\Model\Product')->load($productId);
		// $stockloc =$productObject->getStockloc();
		

		// draw SKU
        $lines[0][] = [
            'text' => $this->string->split($this->getSku($item), 15),
            'feed' => 230,
			'font_size' =>8,
            'align' => 'left'
        ];

        // draw QTY
        $lines[0][] = ['text' => $item->getQty() * 1, 'feed' => 400,'font_size' =>8, 'align' => 'left'];

        // draw item Prices
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        $feedPrice = 460;
        $feedSubtotal = $feedPrice + 125;
		//print_r($prices);
        foreach ($prices as $priceData) {
			
            // if (isset($priceData['label'])) {
                // // draw Price label
                // $lines[$i][] = ['text' => $priceData['label'], 'feed' => 300,'font_size' =>8, 'align' => 'left'];
                // // draw Subtotal label
                // $lines[$i][] = ['text' => $priceData['label'], 'feed' => 500,'font_size' =>8, 'align' => 'left'];
                // $i++;
            // }
			
            // draw Price
			$price=explode("$",$priceData['price']);
			if (isset($priceData['label']) && $priceData['label']=="Excl. Tax:") {
				$lines[$i][] = [
					//'text' => preg_replace('/Excl. Tax: (.*) AU/','',$priceData['price']),
					'text' => "$".$price[1],
					'feed' => 350,
					'font' => 'normal',
					'align' => 'right',
					'font_size' =>8,
				];
				// draw Subtotal
				$subtotal=explode("$",$priceData['subtotal']);
				//print_r($subtotal);
				//exit();
				$lines[$i][] = [
					//'text' => preg_replace('/Excl. Tax: (.*) AU/','',$priceData['subtotal']),
					'text' => "$".$subtotal[1],
					'feed' => 525,
					'font' => 'normal',
					'align' => 'left',
					'font_size' =>8,
				];
			}else if (!isset($priceData['label'])) { 
					$lines[$i][] = [
						//'text' => preg_replace('/Excl. Tax: (.*) AU/','',$priceData['price']),
						'text' => "$".$price[1],
						'feed' => 350,
						'font' => 'normal',
						'align' => 'right',
						'font_size' =>8,
					];
					// draw Subtotal
					$subtotal=explode("$",$priceData['subtotal']);
					//print_r($subtotal);
					//exit();
					$lines[$i][] = [
						//'text' => preg_replace('/Excl. Tax: (.*) AU/','',$priceData['subtotal']),
						'text' => "$".$subtotal[1],
						'feed' => 525,
						'font' => 'normal',
						'align' => 'left',
						'font_size' =>8,
					];
			}	
            $i++;
        }
//exit;
        // draw Tax
        $lines[0][] = [
            'text' => str_replace("AU","",$order->formatPriceTxt($item->getTaxAmount())),
            'feed' => 475,
            'font' => 'normal',
            'align' => 'left',
			'font_size' =>8,
        ];

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = [
                    'text' => $this->string->split($this->filterManager->stripTags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35,
					'font_size' =>8
                ];

                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $printValue = $option['print_value'];
                    } else {
                        $printValue = $this->filterManager->stripTags($option['value']);
                    }
                    $values = explode(', ', $printValue);
                    foreach ($values as $value) {
                        $lines[][] = ['text' => $this->string->split($value, 30, true, true), 'feed' => 40];
                    }
                }
            }
        }

        $lineBlock = ['lines' => $lines, 'height' => 20];

        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $this->setPage($page);
    }
}
