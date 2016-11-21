<?

AddEventHandler("sale", "OnBeforeBasketDelete", array("backetClass", "ant_OnBeforeBasketDelete"));
//AddEventHandler("sale", "OnBasketDelete", array("backetClass", "ant_OnBasketDelete"));

require_once 'include/initClass/backetClass.php';


function GetYearsAndMountItems($iblock_id)
{
	$arYears = array();
	$rsItems = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $iblock_id, "ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y"), false, false, array("ID", "DATE_ACTIVE_FROM"));
	while ($arItem = $rsItems->Fetch()) {
		if ($arItem["DATE_ACTIVE_FROM"]) {
			//print_r($arItem["DATE_ACTIVE_FROM"]);

			$date = explode(' ', $arItem["DATE_ACTIVE_FROM"]);
			$date = $date[0];
			$date = explode('.', $date);
			$arYears[$date[1]] = array('year' => $date[2], "mount" => $date[1], "day" => $date[0]);
		}
	}
	return $arYears;
}

function printr($ar)
{
	global $USER;
	if ($USER->IsAdmin()) {
		if (is_array($ar)) {
			echo '<pre>';
			print_r($ar);
			echo '</pre>';

		} else {

			echo '<br>';
			var_dump($ar);

		}
	}
}

function geooplata($city)
{
	//\Bitrix\Main\Loader::IncludeModule("iblock") or die;
	$arGM = array();
	$arSelect = array("ID", "IBLOCK_ID","NAME", "PROPERTY_*");
	$res = CIBlockElement::GetList(array(), array('PROPERTY_CITY' => $city, 'IBLOCK_ID' => 4), false, array(),
		$arSelect);
	while ($prop = $res->GetNextElement()) {

		$pro=$prop->GetProperties();


		 $coordinat=  explode(',',$pro['MAP']['VALUE']);
		$arGM['google_lat'] = floatval($coordinat[0]);///широта
		$arGM['google_lon'] = floatval($coordinat[1]);//долгота
		$arGM['google_scale'] = 17; //google_scale
		$arGM['PLACEMARKS'][] = array(
			'TEXT' => $pro['ADDRESS']['VALUE'], //Адрес
			'LAT' => floatval($coordinat[0]),
			'LON' => floatval($coordinat[1])
		);
		$arGM = serialize($arGM);
		$arResultS[] = array(
			'googleMapInfoSer' => $arGM,
			'postAdress' => $pro['ADDRESS']['VALUE'],//Адрес
			'emailAdress' => $pro['EMAIL']['VALUE'],//email
			'phoneNumber' => $pro['PHONE']['VALUE'],//phone
			'timeWork' => $pro['SCHEDULE']['VALUE']['TEXT'],//timework
			'howtogo'=>$pro['HOWTOGO']['VALUE']['TEXT'],
			'whenship'=>$pro['WHENSHIP']['VALUE']
		);
		unset($arGM);

	}

	return $arResultS;

}