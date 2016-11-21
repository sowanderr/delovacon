<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	/** @var CBitrixComponent $this */
	/** @var array $arParams */
	/** @var array $arResult */
	/** @var string $componentPath */
	/** @var string $componentName */
	/** @var string $componentTemplate */
	/** @global CDatabase $DB */
	/** @global CUser $USER */
	/** @global CMain $APPLICATION */
	/** @global CCacheManager $CACHE_MANAGER */


	use Bitrix\Main\Loader;
	use \Bitrix\Main\Localization\Loc as Loc;

	Loc::loadMessages(__FILE__);

	$this->setFrameMode(true);

	$arParams['CACHE_TIME'] = (isset($arParams['CACHE_TIME']) ? $arParams['CACHE_TIME'] : 3600);
	$arParams['LOCATION']   = (isset($arParams['LOCATION']) ? $arParams['LOCATION'] : 0);
	$arParams['AJAX']       = (isset($arParams['AJAX']) && $arParams['AJAX'] == 'Y' ? 'Y' : 'N');


	$PARTNER_COMPONENT_ID = 'BXMAKER.GEOIP.DELIVERY';
	$MODULE_ID            = "bxmaker.geoip";

	if (!Loader::includeModule('sale')) return false;

	if (!Loader::includeSharewareModule($MODULE_ID)) {
		ShowError(GetMessage($PARTNER_COMPONENT_ID . "_MODULE_NOT_INSTALLED"));
		return false;
	}
	Loader::includeModule($MODULE_ID);


	$oManager             = \Bxmaker\GeoIP\Manager::getInstance();
	$arParams['LOCATION'] = $oManager->getLocation();


	if ($arParams["CACHE_TYPE"] == "N" || $arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N") {
		$CACHE_TIME = 0;
	}
	else {
		$CACHE_TIME = $arParams["CACHE_TIME"];
	}

	$CACHE_ID  = md5(implode('|', $arParams) . '|' . SITE_ID . '|' . $this->__template);
	$CACHE_DIR = "/bxmaker.geoip.delivery/";


	$cache = new CPHPCache;
	if ($cache->StartDataCache($CACHE_TIME, $CACHE_ID, $CACHE_DIR)) {

		global $CACHE_MANAGER;
		$CACHE_MANAGER->StartTagCache($CACHE_DIR);

		// добавляем метку для кэша
		$CACHE_MANAGER->RegisterTag('bxmaker_geoip_params');

		$arResult['ITEMS']   = array();
		$arResult['COURIER'] = array_diff(explode('|', $oManager->getParam('DELIVERY_COURIER', '')), array(''));
		$arResult['PICKUP']  = array_diff(explode('|', $oManager->getParam('DELIVERY_PICKUP', '')), array(''));
		$arResult['POCHTA']  = array_diff(explode('|', $oManager->getParam('DELIVERY_POCHTA', '')), array(''));

		$arResult['DELIVERY_BLOCKS_ON']          = $oManager->getParam('DELIVERY_BLOCKS_ON', 'Y');
		$arResult['DELIVERY_PRICE_SHOW']         = $oManager->getParam('DELIVERY_PRICE_SHOW', 'Y');
		$arResult['DELIVERY_DATE_SHOW']          = $oManager->getParam('DELIVERY_DATE_SHOW', 'Y');
		$arResult['DELIVER_BLOCK_PROLOG']        = $oManager->getParam('DELIVER_BLOCK_PROLOG', Loc::getMessage($PARTNER_COMPONENT_ID . 'DELIVER_BLOCK_PROLOG'));
		$arResult['DELIVER_BLOCK_EPILOG']        = $oManager->getParam('DELIVER_BLOCK_EPILOG', Loc::getMessage($PARTNER_COMPONENT_ID . 'DELIVER_BLOCK_EPILOG'));
		$arResult['DELIVERY_PAGE']               = $oManager->getParam('DELIVERY_PAGE', '/delivery/');
		$arResult['DELIVERY_CITY']               = $oManager->getParam('DEFAULT_CITY', Loc::getMessage($PARTNER_COMPONENT_ID . 'DEFAULT_CITY'));
		$arResult['COMPONENT_DELIVERY_TEMPLATE'] = $oManager->getParam('COMPONENT_DELIVERY_TEMPLATE', '.default');



		$app = \Bitrix\Main\Application::getInstance();
		//		$order = \Bitrix\Sale\Order::create($app->getContext()->getSite(), $USER->GetID() ? $USER->GetID() : CSaleUser::GetAnonymousUserID());
		$order = \Bitrix\Sale\Order::create($app->getContext()->getSite());
		$order->isStartField();

		//persona type
		$dbPersonType = \CSalePersonType::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array("ACTIVE" => "Y", "LID"=> $app->getContext()->getSite()));
		if($arPersonType = $dbPersonType->GetNext())
		{
			$personTypeId = $arPersonType["ID"];
		}
		else
		{
			$personTypeId = 1;
		}
		$order->setPersonTypeId($personTypeId);

		// LOCATION SET
		$props = $order->getPropertyCollection();
		$loc = $props->getDeliveryLocation();
		$arLoc = \Bitrix\Sale\Location\LocationTable::getById($arParams['LOCATION'])->fetch();
		if (!empty($arLoc) && is_object($loc))
		{
			$loc->setField('VALUE', $arLoc['CODE']);
		}

		//price
		$order->setFieldNoDemand("PRICE", 1000);

		//basket
		$basket = \Bitrix\Sale\Basket::create($app->getContext()->getSite());
		$settableFields = array_flip(\Bitrix\Sale\BasketItemBase::getSettableFields());

		$newBasketItem = \Bitrix\Sale\BasketItem::create($basket, 'catalog', '0');
		$newBasketItem->setFieldsNoDemand(array_intersect_key(Array(
			'MODULE'                 => 'catalog',
			'PRODUCT_ID'             => 0,
			'QUANTITY'               => 1,
			'DELAY'                  => 'N',
			'CAN_BUY'                => 'Y',
			'PRICE'                  => 1000,
			'WEIGHT'                 => 1000,
			'NAME'                   => '',
			'CURRENCY'               => \CSaleLang::GetLangCurrency($app->getContext()->getSite()),
			'CATALOG_XML_ID'         => '',
			'NOTES'                  => '',
			'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
			'DIMENSIONS'             => Array
			(
				'WIDTH'  => 20,
				'HEIGHT' => 20,
				'LENGTH' => 20,
			),

			'DETAIL_PAGE_URL'        => '',
			'BASE_PRICE'             => 1000,
			'BARCODE_MULTI'          => 'N',
			'PRODUCT_PRICE_ID'       => 0,
			'VAT_INCLUDED'           => 'Y',
		), $settableFields));

		$basket->addItem($newBasketItem);
		$order->setBasket($basket);

		//shipment
		$shipmentCollection = $order->getShipmentCollection();
		$shipment = $shipmentCollection->createItem();
		$shipmentItemCollection = $shipment->getShipmentItemCollection();
		$shipment->setField('CURRENCY', $order->getCurrency());

		/** @var \Bitrix\Sale\BasketItem $item */
		foreach ($order->getBasket() as $item)
		{
			/** @var \Bitrix\Sale\ShipmentItem $shipmentItem */
			$shipmentItem = $shipmentItemCollection->createItem($item);
			$shipmentItem->setQuantity($item->getQuantity());
		}

		//delivery
		$arDeliveryServiceAll = \Bitrix\Sale\Delivery\Services\Manager::getRestrictedObjectsList($shipment);


		$arDeliveryItems = array();
		if (!empty($arDeliveryServiceAll))
		{
			foreach ($arDeliveryServiceAll as $deliveryObj)
			{
				$arDelivery = array();
				$arDelivery['ID'] = $deliveryObj->getId();
				$arDelivery['NAME'] = $deliveryObj->isProfile() ? $deliveryObj->getNameWithParent() : $deliveryObj->getName();
				$arDelivery['OWN_NAME'] = $deliveryObj->getName();
				$arDelivery['DESCRIPTION'] = $deliveryObj->getDescription();
				$arDelivery['FIELD_NAME'] = 'DELIVERY_ID';
				$arDelivery["CURRENCY"] = $order->getCurrency();
				$arDelivery['SORT'] = $deliveryObj->getSort();
				$arDelivery['EXTRA_SERVICES'] = $deliveryObj->getExtraServices()->getItems();
				//$arDelivery['STORE'] = Bitrix\Sale\Delivery\ExtraServices\Manager::getStoresList($deliveryObj->getId());

				if (intval($deliveryObj->getLogotip()) > 0)
					$arDelivery["LOGOTIP"] = CFile::GetFileArray($deliveryObj->getLogotip());

				if (!empty($arDelivery['STORE']) && is_array($arDelivery['STORE']))
				{
					foreach ($arDelivery['STORE'] as $val)
						$arStoreId[$val] = $val;
				}

				$arDeliveryItems[$deliveryObj->getId()] = $arDelivery;

			}
		}

		if ($arDeliveryServiceAll) {
			foreach ($arDeliveryServiceAll as $deliveryObj) {

				$calcResult = $deliveryObj->calculate($shipment);

				$tmp = array(
					"ID"          => $deliveryObj->getId(),
					"NAME"        => $deliveryObj->getName(),
					"DESCRIPTION" => $deliveryObj->getDescription(),
					"FIELD_NAME"  => "DELIVERY_ID"
				);

				if (intval($deliveryObj->getLogotip()) > 0) {
					$tmp["LOGOTIP"] = \CFile::GetFileArray($deliveryObj->getLogotip());
				}


				$tmp['SORT']           = $deliveryObj->getSort();
				$tmp["PRICE_FORMATED"] = SaleFormatCurrency($calcResult->getPrice(), $deliveryObj->getCurrency());
				$tmp["CURRENCY"]       = $deliveryObj->getCurrency();
				$tmp["PRICE"]          = $calcResult->getPrice();
				$tmp["DELIVERY_PRICE"] = roundEx($calcResult->getPrice(), SALE_VALUE_PRECISION);
				$tmp["PERIOD_TEXT"]    = $calcResult->getPeriodDescription();


				$arResult['ITEMS'][$deliveryObj->getId()] = $tmp;
			}
			if($deliveries)
			uasort($deliveries, array('CSaleBasketHelper', 'cmpBySort'));
		}

		$cache->EndDataCache(
			array(
				'arResult' => $arResult
				//				"templateCachedData" => $this->GetTemplateCachedData()
			)
		);
	}
	else {
		$vars     = $cache->GetVars();
		//		$this->SetTemplateCachedData($vars['templateCachedData']);
		$arResult = $vars['arResult'];
	}

	$this->IncludeComponentTemplate();

