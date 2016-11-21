<?
namespace Bxmaker\GeoIP;


use Bitrix\Main\Application;
use Bitrix\Main\Entity;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);


class Handler
{

	/**
	 * Компонент в футере страницы, для подмены городов
	 */
	static public function main_OnEpilog()
	{
		global $APPLICATION;

		if (!Manager::isAdminSection()) {
			$req = Application::getInstance()->getContext()->getRequest();
			$oManager = Manager::getInstance();

			if ($req->getRequestMethod() != 'POST' && strpos($req->getRequestUri(), '/bitrix/') === false && !$req->getQuery('ajax')) {

				$bSkip = false;
				$curUrl = $req->getRequestedPage();
				$arUrlSkip = explode("\n", $oManager->getParam('URL_SKIP'));
				foreach ($arUrlSkip as $str) {
					if ($str) {
						if ($oManager->isUTF()) {
							if (mb_strpos($curUrl, $str) !== false) {
								$bSkip = true;
							}
						} else {
							if (strpos($curUrl, $str) !== false) {
								$bSkip = true;
							}
						}
					}
				}

				if (!$bSkip) {
					$APPLICATION->IncludeComponent('bxmaker:geoip.epilog', trim(Manager::getInstance()->getParam('COMPONENT_EPILOG_TEMPLATE', '')), array());
				}
			}
		}
	}


	/**
	 * после создания заказа
	 */
	static public function sale_OnSaleComponentOrderProperties(&$arUserResult, $request, &$arParams, &$arResult)
	{

		$geoManager = \Bxmaker\GeoIP\Manager::getInstance();

		/**
		 * @var $request \Bitrix\Main\HttpRequest
		 */

		$bChange = false;
		// если POST запрос
		if (intval($arUserResult['PERSON_TYPE_ID']) > 0) {
			if ($request->isPost()) {
				//смена типа плательщика
				if ($arUserResult['PERSON_TYPE_ID'] != $arUserResult['PERSON_TYPE_OLD'] && $arUserResult['PERSON_TYPE_OLD'] !== false) {
					if ($arUserResult['PROFILE_ID']) {
						//если можно сменить
						if ($geoManager->getParam('CHANGE_PROFILE_CITY', 'Y') == 'Y') {
							$bChange = true;
						}
					} else {
						// если не указан профиль, значит первый раз оформляется
						$bChange = true;
					}
				}
			} else {
				// еслиу указан профиль
				if ($arUserResult['PROFILE_ID']) {
					//если можно сменить
					if ($geoManager->getParam('CHANGE_PROFILE_CITY', 'Y') == 'Y') {
						$bChange = true;
					}
				} else {
					// если не указан профиль, значит первый раз оформляется
					$bChange = true;
				}
			}
		}


		//профиль знаем
		if ($bChange) {

			$app = Application::getInstance();
			$order = \Bitrix\Sale\Order::create($app->getContext()->getSite());
			$order->isStartField();

			$propertyCollection = $order->getPropertyCollection();
			/** @var \Bitrix\Sale\PropertyValue $property */
			foreach ($propertyCollection as $property) {
				if ($property->isUtil()) {
					continue;
				}

				$arProperty = $property->getProperty();

				$curVal = null;

				if ($arProperty["IS_ZIP"] == "Y") {
					$arUserResult['ORDER_PROP'][$arProperty["ID"]] = $geoManager->getZip();
				}

				if ($arProperty["TYPE"] == 'LOCATION') {

					$curVal = \CSaleLocation::getLocationCODEbyID($geoManager->getLocation());
					$arUserResult['ORDER_PROP'][$arProperty["ID"]] = $curVal;

				}


			}
		}

	}


	/**
	 * В форме оформления заказа подставляет  город
	 *
	 * @param $arResult
	 * @param $arUserResult
	 * @param $params
	 *
	 * @return bool
	 */
	static public function sale_OnSaleComponentOrderOneStepOrderProps(&$arResult, &$arUserResult, &$params)
	{

		$geoManager = \Bxmaker\GeoIP\Manager::getInstance();

		$location = $geoManager->getLocation();
		$zip = $geoManager->getZip();

		if (Manager::isAdminSection()) return true;
		if (!intval($location)) return true;

		//есть профиль и изменять город не нужно
		if ($arUserResult['PROFILE_ID']) {
			if ($geoManager->getParam('CHANGE_PROFILE_CITY', 'Y') != 'Y') {
				return true;
			}
		}

		//если не первый запрос
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			do {
				//использование профиля
				if ($arUserResult['PROFILE_ID'] && $arUserResult['PROFILE_CHANGE'] == 'Y') {
					// если можно менть город
					if ($geoManager->getParam('CHANGE_PROFILE_CITY', 'Y') == 'Y') {
						// можно идти дальше и менять значение
						break;
					}
				}

				return true;

			} while (false);
		}


		$oldValue = null;
		$newValue = null;
		foreach ($arResult['ORDER_PROP']['USER_PROPS_Y'] as &$item) {


			if ($item['IS_LOCATION'] == 'Y') {
				foreach ($item['VARIANTS'] as $k => &$variant) {
					if (isset($variant['SELECTED']) && $variant['SELECTED'] == 'Y') {
						$oldValue = $k;
						unset($variant['SELECTED']);
					}
					if ($variant['ID'] == $location) {
						$newValue = $k;
					}

				}
				unset($variant);

				if (!is_null($newValue) && isset($item['VARIANTS'][$newValue])) {
					$item['VARIANTS'][$newValue]['SELECTED'] = 'Y';
					$arUserResult['DELIVERY_LOCATION'] = $item['VARIANTS'][$newValue]['ID'];
				} elseif (!is_null($oldValue) && isset($item['VARIANTS'][$oldValue])) {
					$item['VARIANTS'][$oldValue]['SELECTED'] = 'Y';
					$arUserResult['DELIVERY_LOCATION'] = $item['VARIANTS'][$oldValue]['ID'];
				}
			}

			if ($item['IS_ZIP'] == 'Y') {
				if ($zip != '000000' && ($item['VALUE'] == '' || $item['VALUE'] == '000000')) {
					$item['VALUE'] = $zip;
					$arUserResult['DELIVERY_LOCATION_ZIP'] = $zip;
				}
			}


		}
		unset($item);

		$oldValue = null;
		$newValue = null;
		foreach ($arResult['ORDER_PROP']['USER_PROPS_N'] as &$item) {

			if ($item['IS_LOCATION'] == 'Y') {
				foreach ($item['VARIANTS'] as $k => &$variant) {
					if (isset($variant['SELECTED']) && $variant['SELECTED'] == 'Y') {
						$oldValue = $k;
						unset($variant['SELECTED']);
					}
					if ($variant['ID'] == $location) {
						$newValue = $k;
					}
				}
				unset($variant);

				if (!is_null($newValue) && isset($item['VARIANTS'][$newValue])) {
					$item['VARIANTS'][$newValue]['SELECTED'] = 'Y';
					$arUserResult['DELIVERY_LOCATION'] = $item['VARIANTS'][$newValue]['ID'];
				} elseif (!is_null($oldValue) && isset($item['VARIANTS'][$oldValue])) {
					$item['VARIANTS'][$oldValue]['SELECTED'] = 'Y';
					$arUserResult['DELIVERY_LOCATION'] = $item['VARIANTS'][$oldValue]['ID'];
				}
			}

			if ($item['IS_ZIP'] == 'Y') {
				if ($zip != '000000' && ($item['VALUE'] == '' || $item['VALUE'] == '000000')) {
					$item['VALUE'] = $zip;
					$arUserResult['DELIVERY_LOCATION_ZIP'] = $zip;
				}
			}
		}
		unset($item);

		return true;
	}


	static public function main_OnBeforeProlog()
	{
		global $APPLICATION;

		if (!Manager::isAdminSection()) {
			$req = Application::getInstance()->getContext()->getRequest();
			if ($req->getRequestMethod() == 'POST'
				&& !!$req->getPost('module')
				&& $req->getPost('module') == 'bxmaker.geoip'
			) {

				$msg = 'bxmaker.geoip_HANDLER.';

				//
				$oManager = Manager::getInstance();

				$arResp = array(
					'response' => array(),
					'error' => array()
				);

				if ($oManager->isExpired()) {
					$arResp['error'] = array(
						'CODE' => '0',
						'MSG' => GetMessage($msg . 'DEMO_EXPIRED'),
						'MORE' => ''
					);
				} else {
					switch ($req->getPost('method')) {
						case 'saveCity': {


							if (!check_bitrix_sessid('sessid')) {
								$arResp['error'] = array(
									'CODE' => 'h1',
									'MSG' => GetMessage($msg . 'ERROR_SESSID'),
									'MORE' => ''
								);
								break;
							}

							if ($oManager->isExpired()) {
								$arResp['error'] = array(
									'CODE' => 'h2',
									'MSG' => GetMessage($msg . 'DEMO_EXPIRED'),
									'MORE' => ''
								);
								break;
							}


							//если  модуль магазина не установлен
							if (Loader::includeModule('sale')) {


								if ($oManager->getParam('YANDEX_GEOLOCATION_ON', 'Y') == 'Y') {
									// ищем местополодение

									if (intval($req->getPost('location')) > 0) {
										// из выбранных после поиска
										$oManager->selectLocation($req->getPost('location'));
									} else {
										$bFound = false;
										// неизвестный идентификатор города
										if (\CSaleLocation::isLocationProEnabled()) {
											$arCity = array();
											$res = \Bitrix\Sale\Location\LocationTable::getList(array(
												'select' => array('*', 'CITY_NAME' => 'NAME.NAME'),
												'filter' => array(
													'=NAME.LANGUAGE_ID' => LANGUAGE_ID,
													'=%NAME.NAME' => trim($oManager->restoreEncoding($req->getPost('city'))),
													'!CITY_ID' => false
												),
												'limit' => 1
											));
											if ($ar = $res->fetch()) {
												$oManager->selectLocation($ar['ID']);
												$bFound = true;
											}
										} else {
											$bFound = false;
											$dbLocation = \CSaleLocation::GetList(
												array(
													"SORT" => "ASC",
													"COUNTRY_NAME_LANG" => "ASC",
													"CITY_NAME_LANG" => "ASC"
												),
												array(
													"LID" => LANGUAGE_ID,
													"~CITY_NAME" => $oManager->restoreEncoding($req->getPost('city'))
												),
												false,
												array('nPageSize' => 1),
												array()
											);
											if ($arLocation = $dbLocation->Fetch()) {
												$oManager->selectLocation($arLocation['ID']);
												$bFound = true;
											}
										}
									}
								} else {
									if (!$req->getPost('location') || intval($req->getPost('location')) <= 0) {
										$arResp['error'] = array(
											'CODE' => 'h4',
											'MSG' => GetMessage($msg . 'ERROR_LOCATION'),
											'MORE' => ''
										);
										break;
									}

									// установка местополодение
									$oManager->selectLocation($req->getPost('location'));
								}

							} else {

								if (!$req->getPost('city') || strlen(trim($oManager->restoreEncoding($req->getPost('city')))) < 3) {
									$arResp['error'] = array(
										'CODE' => 'h5',
										'MSG' => GetMessage($msg . 'ERROR_CITY'),
										'MORE' => ''
									);
									break;
								}

								$oManager->setLocation(0);
								$oManager->setZip('000000');
								$oManager->setCountry($oManager->restoreEncoding($req->getPost('country')));
								$oManager->setCity($oManager->restoreEncoding($req->getPost('city')));
								$oManager->setRegion($oManager->restoreEncoding($req->getPost('region')));
								$oManager->setDistrict($oManager->restoreEncoding($req->getPost('district')));
								$oManager->setYandex(1);
							}

							$oManager->saveCookie();

							$arResp['response'] = array(
								'location' => $oManager->getLocation(),
								'zip' => $oManager->getZip(),
								'country' => $oManager->getCountry(),
								'city' => $oManager->getCity(),
								'region' => $oManager->getRegion(),
								'district' => $oManager->getDistrict(),
								'yandex' => $oManager->getYandex(),
							);


							break;
						}
						case 'search': {

							if (!check_bitrix_sessid('sessid')) {
								$arResp['error'] = array(
									'CODE' => 'h6',
									'MSG' => GetMessage($msg . 'ERROR_SESSID'),
									'MORE' => ''
								);
								break;
							}

							if (!$req->getPost('query') || strlen(trim($oManager->restoreEncoding($req->getPost('query')))) < 3) {
								$arResp['error'] = array(
									'CODE' => 'h7',
									'MSG' => GetMessage($msg . 'ERROR_SEARCH_LENGTH'),
									'MORE' => ''
								);
								break;
							}

							if ($oManager->isExpired()) {
								$arResp['error'] = array(
									'CODE' => 'h8',
									'MSG' => GetMessage($msg . 'DEMO_EXPIRED'),
									'MORE' => ''
								);
								break;
							}


							$arResp['response'] = array(
								'items' => array(),
								'count' => 0
							);
							//если установлен модуль интернет-магазина
							if (Loader::includeModule('sale')) {

								if (\CSaleLocation::isLocationProEnabled()) {
									// ищем местополодение
									$res = \Bitrix\Sale\Location\LocationTable::getList(array(
										'select' => array('*', 'CITY_NAME' => 'NAME.NAME'),
										'filter' => array(
											'=NAME.LANGUAGE_ID' => LANGUAGE_ID,
											'=%NAME.NAME' => '%' . $oManager->restoreEncoding($req->getPost('query')) . '%',
											'!CITY_ID' => false
										),
										'limit' => 10
									));
									while ($ar = $res->fetch()) {
										$ar['PATH'] = array();

										//Путь
										$location = $ar['ID'];
										$city = $ar['CITY_NAME'];
										$country = '';
										$region = '';
										$district = '';
										// собираем регионы
										$resParent = \Bitrix\Sale\Location\LocationTable::getList(array(
											'select' => array('ID', 'CODE', 'PARENT_ID', 'TYPE_ID', 'PARENT_NAME' => 'NAME.NAME'),
											'filter' => array(
												'=NAME.LANGUAGE_ID' => LANGUAGE_ID,
												'<LEFT_MARGIN' => $ar['LEFT_MARGIN'],
												'>=RIGHT_MARGIN' => $ar['RIGHT_MARGIN'],
											),
											'order' => array('LEFT_MARGIN' => 'ASC')
										));
										while ($arParent = $resParent->fetch()) {
											switch ($arParent['TYPE_ID']) {
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


										$arResp['response']['items'][] = array(
											'zip' => '000000',
											'location' => $location,
											'country' => $country,
											'city' => $city,
											'region' => $region,
											'district' => $district,
											'yandex' => 0
										);
									}
								} else {


									$res = \CSaleLocation::GetList(
										array(
											"SORT" => "ASC",
											"COUNTRY_NAME_LANG" => "ASC",
											"CITY_NAME_LANG" => "ASC"
										),
										array(
											"LID" => LANGUAGE_ID,
											'~CITY_NAME' => '%' . $oManager->restoreEncoding($req->getPost('query')) . '%',
										),
										false,
										array('nPageSize' => 10),
										array()
									);

									if ($ar = $res->fetch()) {

										//Путь
										$location = $ar['ID'];
										$city = $ar['CITY_NAME'];
										$country = $ar['COUNTRY_NAME'];
										$region = $ar['REGION_NAME'];
										$district = '';


										$arResp['response']['items'][] = array(
											'zip' => '000000',
											'location' => $location,
											'country' => $country,
											'city' => $city,
											'region' => $region,
											'district' => $district,
											'yandex' => 0
										);

									}
								}
							}

							$arResp['response']['count'] = count($arResp['response']['items']);

							break;
						}
						case 'getDelivery': {

							if (!check_bitrix_sessid('sessid')) {
								$arResp['error'] = array(
									'CODE' => 'h9',
									'MSG' => GetMessage($msg . 'ERROR_SESSID'),
									'MORE' => ''
								);
								break;
							}

							if ($oManager->isExpired()) {
								$arResp['error'] = array(
									'CODE' => 'h10',
									'MSG' => GetMessage($msg . 'DEMO_EXPIRED'),
									'MORE' => ''
								);
								break;
							}

							ob_start();
							if (Loader::includeModule('sale')) {
								$APPLICATION->IncludeComponent('bxmaker:geoip.delivery', trim(Manager::getInstance()->getParam('COMPONENT_DELIVERY_TEMPLATE', '')), array(
									'AJAX' => 'Y'
								));
							}
							$arResp['response']['html'] = ob_get_clean();


							break;
						}
						// ok
						case 'getMessage': {

							if (!check_bitrix_sessid('sessid')) {
								$arResp['error'] = array(
									'CODE' => 'h9',
									'MSG' => GetMessage($msg . 'ERROR_SESSID'),
									'MORE' => ''
								);
								break;
							}

							if ($oManager->isExpired()) {
								$arResp['error'] = array(
									'CODE' => 'h10',
									'MSG' => GetMessage($msg . 'DEMO_EXPIRED'),
									'MORE' => ''
								);
								break;
							}


							ob_start();
							$APPLICATION->IncludeComponent('bxmaker:geoip.message', ($req->getPost('template') ? trim((string)$req->getPost('template')) : ''), array(
								'AJAX' => 'Y',
								'TYPE' => trim((string)$req->getPost('type')),
								"COMPONENT_TEMPLATE" => ($req->getPost('template') ? trim((string)$req->getPost('template')) : '')
							));
							$arResp['response']['html'] = ob_get_clean();


							break;
						}
						default: {
							$arResp['error'] = array(
								'CODE' => 'h11',
								'MSG' => GetMessage($msg . 'ERROR_METHOD'),
								'MORE' => ''
							);
							break;
						}
					}
				}

				$oManager->showJson($arResp);
			}
		}
	}
}