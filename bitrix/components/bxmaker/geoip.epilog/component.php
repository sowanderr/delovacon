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

	global $CACHE_MANAGER;
	use Bitrix\Main\Loader;


	$this->setFrameMode(true);


	$PARTNER_COMPONENT_ID = 'BXMAKER.GEOIP.EPILOG';
	$MODULE_ID            = "bxmaker.geoip";


	$arParams['CACHE_TIME'] = (isset($arParams['CACHE_TIME']) ? $arParams['CACHE_TIME'] : 86400);
	$arParams['CACHE_TYPE'] = (isset($arParams['CACHE_TYPE']) && in_array($arParams['CACHE_TYPE'], array('N', 'Y', 'A')) ? $arParams['CACHE_TYPE'] : 'A');


	if (!Loader::includeSharewareModule($MODULE_ID)) {
		ShowError(GetMessage($PARTNER_COMPONENT_ID . "_MODULE_NOT_INSTALLED"));
		return 0;
	}

	Loader::includeModule($MODULE_ID);
	$oManager = \Bxmaker\GeoIP\Manager::getInstance();
	$oAsset   = \Bitrix\Main\Page\Asset::getInstance();

	$arParams['LOCATION_ID']  = $oManager->getLocation();
	$arParams['CITY']  = $oManager->getCity();
	$arParams['REGION']  = $oManager->getRange();
	$arParams['DISTRICT']  = $oManager->getDistrict();

	CJSCore::Init();

	//����������� ���������
	$oAsset->addJs($componentPath . '/js/jquery.js', true);


	//���������
	$oAsset->addString('');

	if ($arParams["CACHE_TYPE"] == "N" || $arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N") {
		$CACHE_TIME = 0;
	}
	else {
		$CACHE_TIME = $arParams["CACHE_TIME"];
	}

	$CACHE_ID  = md5(implode('|', $arParams) . '|' . SITE_ID . '|' . $this->__template);
	$CACHE_DIR = "/bxmaker.geoip.epilog/";

	$cache = new CPHPCache;
	if ($cache->StartDataCache($arParams["CACHE_TIME"], $CACHE_ID, $CACHE_DIR)) {

		\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

		global $CACHE_MANAGER;
		$CACHE_MANAGER->StartTagCache($CACHE_DIR);
		// ��������� ����� ��� ����
		$CACHE_MANAGER->RegisterTag('bxmaker_geoip_params');


		$arResult['ITEMS'] = array();

		if (Loader::includeModule('sale')) {

			if(\CSaleLocation::isLocationProEnabled())
			{
				$res = \Bitrix\Sale\Location\LocationTable::getList(array(
					'select' => array('*', 'CITY_NAME' => 'NAME.NAME'),
					'order'  => array('SORT' => 'ASC'),
					'filter' => array(
						'=NAME.LANGUAGE_ID' => LANGUAGE_ID,
						'!CITY_ID'          => false
					),
					'limit'  => 42
				));
				while ($ar = $res->fetch()) {

					$location = $ar['ID'];
					$city = $ar['CITY_NAME'];
					$country = '';
					$region = '';
					$district = '';
					$resParent = \Bitrix\Sale\Location\LocationTable::getList(array(
						'select' => array('ID', 'CODE', 'PARENT_ID', 'TYPE_ID', 'PARENT_NAME' => 'NAME.NAME'),
						'filter' => array(
							'=NAME.LANGUAGE_ID' => LANGUAGE_ID,
							'<LEFT_MARGIN'      => $ar['LEFT_MARGIN'],
							'>=RIGHT_MARGIN'    => $ar['RIGHT_MARGIN'],
						),
						'order'  => array('LEFT_MARGIN' => 'ASC')
					));
					while ($arParent = $resParent->fetch()) {
						switch($arParent['TYPE_ID'])
						{
							case 1: {
								$country = $arParent['PARENT_NAME'];
								break;
							}
							case 3: {
								$region = $arParent['PARENT_NAME'];
								break;
							}
							case 4: {
								$district = $arParent['PARENT_NAME'];
								break;
							}
						}
					}


					$arResult['ITEMS'][] = array(
						'location'   => $location,
						'country' => $country,
						'city' => $city,
						'region' => $region,
						'district' => $district,
						'yandex' => 0
					);
				}

			}
			else
			{
				$res = \CSaleLocation::GetList(
					array(
						"SORT" => "ASC",
						"COUNTRY_NAME_LANG" => "ASC",
						"CITY_NAME_LANG" => "ASC"
					),
					array(
						"LID" => LANGUAGE_ID,
						'!CITY_NAME' => false
					),
					false,
					array('nPageSize' => 42),
					array()
				);
				while ($ar = $res->fetch()) {

					//����
					$location = $ar['ID'];
					$city     = $ar['CITY_NAME'];
					$country  = $ar['COUNTRY_NAME'];
					$region   = $ar['REGION_NAME'];
					$district = '';


					$arResult['ITEMS'][] = array(
						'location'   => $location,
						'country' => $country,
						'city' => $city,
						'region' => $region,
						'district' => $district,
						'yandex' => 0
					);
				}

			}

		}
		else {


			$arCity = array(
				array("������","������","������",""),
				array("������","�����-���������","�����-���������",""),
				array("������","�����������","������������� �������","��������� ����� �����������"),
				array("������","��������","������������ �������","��������� ����� ��������"),
				array("������","��������","������������ �������","��������� ����� ��������"),
				array("������","���������","������������� �������","��������� ����� ���������"),
				array("������","�������","����������� �������","��������� ����� �������"),
				array("������","������������","������������ �������","��������� ����� ������������"),
				array("������","�������","���������� �������","��������� ����� �������"),
				array("������","������","���������� ����������","��������� ����� ������"),
				array("������","�������","��������� �������","��������� ����� �������"),
				array("������","������","���������� ���������","��������� ����� ������"),

				array("������","���������","������������� ����","��������� ����� ���������"),
				array("������","����������","������������ ����","��������� ����� ����������"),
				array("������","��������","���������� �������","��������� ����� ��������"),
				array("������","�������������","��������� �������","����"),
				array("������","������ ��������","������������� �������","��������� ����� ������ ��������"),
				array("������","������ �����","������������ �������","��������� ����� ������ �����"),
				array("������","�����������","����������� �������","��������� ����� �����������"),
				array("������","�����������","������������� �������","��������� ����� �����������"),
				array("������","���","��������� �������","��������� ����� ���"),
				array("������","��������","������������ �������","��������� ����� ��������"),
				array("������","�����","�������� ����","��������� ����� �����"),
				array("������","������-��-����","���������� �������","��������� ����� ������-��-����"),
				array("������","������","��������� �������","��������� ����� ������"),

				array("������","������","��������� �������","��������� ����� ������"),
				array("������","�������","����������� �������","��������� ����� �������"),
				array("������","��������","���������� �������","��������� ����� ��������"),
				array("������","����������","�������������� ����","��������� ����� ����������"),
				array("������","������","��������� �������","����"),
				array("������","��������","���������� �������","��������� ����� ��������"),
				array("������","�����","�������� �������","��������� ����� �����"),
				array("������","��������","��������� �������","��������� ����� ��������"),
				array("������","�����","������� �������","��������� ����� �����"),
				array("������","����","�������� �������","��������� ����� ����"),
				array("������","������","��������� �������","��������� ����� ������"),
				array("������","���","���������� ������������","��������� ����� ���"),
				array("������","���������","����������� ����","��������� ����� ���������"),
				array("������","���������","����������� �������","��������� ����� ���������"),
			);

			foreach($arCity as $item)
			{
				$arResult['ITEMS'][] = array(
					'location' => 0,
					'country' => $item[0],
					'city' => $item[1],
					'region' => $item[2],
					'district' => $item[3],
					'yandex' => '1'
				);
			}

			if($oManager::isUTF())
			{
				$arResult['ITEMS'] = $APPLICATION->ConvertCharsetArray($arResult, 'WINDOWS-1251', 'UTF-8');
			}


		}

		$CACHE_MANAGER->EndTagCache();



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
