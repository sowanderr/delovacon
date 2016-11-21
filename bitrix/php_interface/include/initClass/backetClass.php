<?php

use Bitrix\Sale;

class backetClass
{
	public static $productfield;
	function ant_OnBeforeBasketDelete($id)
	{
		$dbBasketItems = CSaleBasket::GetList(
			array(), 
			array(
				"ID" => $id,
				"FUSER_ID" => CSaleBasket::GetBasketUserID(),
				"LID" => 's1',
				"ORDER_ID" => "NULL"
			),
			false,
			false,
			array("ID", "PRODUCT_ID", "QUANTITY", 'FUSER_ID', 'PRICE', 'CURRENCY', 'CAN_BUY', 'MODULE', 'PRODUCT_PROVIDER_CLASS', 'DISCOUNT_PRICE', 'NAME', 'LID', 'PROPS')
		);
		if ($arItem = $dbBasketItems->Fetch()) {
			self::$productfield = array(
				'PRODUCT_ID' => $arItem['PRODUCT_ID'],
				'QUANTITY' => $arItem['QUANTITY'],
				//'FUSER_ID' => $arItem['FUSER_ID'],
				'PRICE' => $arItem['PRICE'],
				'CURRENCY' => $arItem['CURRENCY'],
				//'CAN_BUY' => $arItem['CAN_BUY'],
				//'MODULE' => $arItem['MODULE'],
				'PRODUCT_PROVIDER_CLASS' => $arItem['PRODUCT_PROVIDER_CLASS'],
				'DISCOUNT_PRICE' => $arItem['DISCOUNT_PRICE'],
				'NAME' => $arItem['NAME'],
				'LID' => $arItem['LID']
			);

		}

		global $APPLICATION;
		$APPLICATION->set_cookie("PROPDUCTDELATE", json_encode(self::$productfield), time() + 60 * 5);


	}

	static function restoreProduct($delProduct)
	{

		$basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
		if ($basket && $delProduct) {
			$item = $basket->createItem('catalog', $delProduct['PRODUCT_ID']);
			$item->setFields($delProduct);
			$basket->save();
		}

	}


}//class