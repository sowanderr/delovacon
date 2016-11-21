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


	$PARTNER_COMPONENT_ID = 'BXMAKER.GEOIP.MESSAGE';
	$MODULE_ID            = "bxmaker.geoip";

	if (!Loader::includeSharewareModule($MODULE_ID)) {
		ShowError(GetMessage($PARTNER_COMPONENT_ID . "_MODULE_NOT_INSTALLED"));
		return false;
	}
	Loader::includeModule($MODULE_ID);


	$oManager               = \Bxmaker\GeoIP\Manager::getInstance();
	$arParams['CACHE_TYPE'] = (isset($arParams['CACHE_TYPE']) ? $arParams['CACHE_TYPE'] : 'A');
	$arParams['CACHE_TIME'] = (isset($arParams['CACHE_TIME']) ? $arParams['CACHE_TIME'] : 3600);
	$arParams['TYPE']       = (isset($arParams['TYPE']) && strlen(trim($arParams['TYPE'])) > 0 ? $arParams['TYPE'] : '');
	$arParams['CITY']       = (isset($arParams['CITY']) && strlen(trim($arParams['CITY'])) > 0 ? $arParams['CITY'] : $oManager->getCity());
	$arParams['AJAX']       = (isset($arParams['AJAX']) && $arParams['AJAX'] == 'Y' ? 'Y' : 'N');


	if ($arParams["CACHE_TYPE"] == "N" || $arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N") {
		$CACHE_TIME = 0;
	}
	else {
		$CACHE_TIME = $arParams["CACHE_TIME"];
	}

	$CACHE_ID = md5(implode('|', array(
			'CITY'       => ($oManager->isUTF() ? mb_strtolower($arParams['CITY']) : strtolower($arParams['CITY'])),
			'CACHE_TIME' => $arParams['CACHE_TIME'],
			'TYPE'       => $arParams['TYPE']
		)) . '|' . SITE_ID . '|' . $this->__template);

	$CACHE_DIR = "/bxmaker.geoip.message/";

	$arResult = array(
		'DEFAULT' => array(),
		'CURRENT' => array()
	);


	$cache = new CPHPCache;
	if ($cache->StartDataCache($CACHE_TIME, $CACHE_ID, $CACHE_DIR)) {

		global $CACHE_MANAGER;
		$CACHE_MANAGER->StartTagCache($CACHE_DIR);

		// добавляем метку для кэша
		$CACHE_MANAGER->RegisterTag('bxmaker_geoip_message');

		$city     = $oManager->getCity();
		$location = $oManager->getLocation();

		$arLocationGroupId = array();
		if(Loader::includeModule('sale'))
		{
			$dbGroup = CSaleLocationGroup::GetLocationList(array("LOCATION_ID" => $location));
			while ($arGroup = $dbGroup->Fetch()) {
				$arLocationGroupId = $arGroup['LOCATION_GROUP_ID'];
			}
		}


		if ($arParams['CITY']) {
			$city = $arParams['CITY'];
		}

		$arDefault = array();
		$arCurrent = array();
		$arFilterOr = array(
			'LOGIC' => 'OR', // по умолчанию элементы склеиваются через AND
			'=CITY' => $city,
			'=DEF'  => true
		);
		if(count($arLocationGroupId))
		{
			$arFilterOr['=GROUP'] = $arLocationGroupId;
		}


		$oMessage = new \Bxmaker\GeoIP\MessageTable();
		$dbr      = $oMessage->getList(array(
			'filter' => array(
				'TYPE.TYPE'    => $arParams['TYPE'],
				'TYPE.SITE_ID' => SITE_ID,
				$arFilterOr
			)
		));
		while ($ar = $dbr->fetch()) {

			if ($ar['DEF']) {
				$arResult['DEFAULT'] = $ar;
			}
			else {
				$arResult['CURRENT'] = $ar;
			}
		}

		if (empty($arResult['CURRENT'])) {
			$arResult['CURRENT'] = $arResult['DEFAULT'];
		}

		$cache->EndDataCache(
			array(
				'arResult' => $arResult
				//				"templateCachedData" => $this->GetTemplateCachedData()
			)
		);
	}
	else {
		$vars = $cache->GetVars();
		//		$this->SetTemplateCachedData($vars['templateCachedData']);
		$arResult = $vars['arResult'];
	}

	$this->IncludeComponentTemplate();

