<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
global $USER;
$APPLICATION->SetTitle("Контакты");
$oManager = \BXmaker\GeoIP\Manager::getInstance();
$city = $oManager->getCity();

geooplata('Москва');

$arResult = geooplata($city);
if(!$arResult)
{
	$arResult = geooplata('Москва');
}

?>


	<div class="wrapper_inner">
		<div class="title">

			Почтовый
			адрес:&nbsp;<? $APPLICATION->IncludeFile(SITE_DIR . "include/address-1.php", Array(), Array("MODE" => "html", "NAME" =>
				"Адрес")); ?><br>
			Email:&nbsp;<a
				href="mailto:info@delo-vkusa.net"><? $APPLICATION->IncludeFile(SITE_DIR . "include/email-1.php", Array(), Array("MODE" => "html", "NAME" => "Email")); ?></a><br>
			Телефон:&nbsp;<? $APPLICATION->IncludeFile(SITE_DIR . "include/phone-1.php", Array(), Array("MODE" => "html", "NAME" => "Телефон")); ?>
			<br>
		</div>
		<br>
		<h1>Адреса розничных магазинов:</h1>

	</div>
<? foreach($arResult as $value): ?>

	<div class="clearboth"></div>
	<div class="wrapper_inner clearfix">
		<div class="store_description">
	<? if($value['postAdress']): ?>
			<div class="store_property">

				<div class="title">
					Адрес
				</div>
				<div class="value">
					<?= $value['postAdress']; ?>
				</div>
			</div>
	<? endif; ?>
	<? if($value['phoneNumber']): ?>
			<div class="store_property">
				<div class="title">
					Телефон
				</div>
				<div class="value">
					<?= $value['phoneNumber']; ?>
				</div>
			</div>
	<? endif; ?>
	<? if($value['emailAdress']): ?>
			<div class="store_property">
				<div class="title">
					Email
				</div>
				<div class="value">
					<?= $value['emailAdress']; ?>
				</div>
			</div>
	<? endif; ?>
	<? if($value['timeWork']): ?>
			<div class="store_property">
				<div class="title">
					Режим работы
				</div>
				<div class="value">
					<?= html_entity_decode($value['timeWork'],null,'UTF-8'); ?>
				</div>
			</div>
	<? endif; ?>
			<? if($value['whenship']): ?>

				<div class="store_property">
					<div class="title">
						Срок доставки (раб. дни):
					</div>
					<div class="value">
						<?= $value['whenship']; ?>
					</div>
				</div>

			<? endif; ?>
		</div>


	<? if(($value['howtogo'])): ?>
		<div class="store_description">
			<div class="store_property">
				<div class="title">
					Описание проезда:
				</div>
				<div class="value">
					<?=html_entity_decode($value["howtogo"],null,'UTF-8'); ?>
				</div>
			</div>
		</div>
	<? endif; ?>

	</div>

	<div class="clearboth">
	</div>

	<div class="contacts_map">


		<div class="contacts_map clearfix">
			<? $APPLICATION->IncludeComponent(
				"bitrix:map.google.view",
				"map",
				array(
					"API_KEY" => "",
					"COMPOSITE_FRAME_MODE" => "A",
					"COMPOSITE_FRAME_TYPE" => "AUTO",
					"CONTROLS" => array(
						0 => "TYPECONTROL",
					),
					"INIT_MAP_TYPE" => "ROADMAP",
					"MAP_DATA" => $value['googleMapInfoSer'],
					"MAP_HEIGHT" => "400",
					"MAP_ID" => "",
					"MAP_WIDTH" => "100%",
					"OPTIONS" => array(
						0 => "ENABLE_DBLCLICK_ZOOM",
						1 => "ENABLE_DRAGGING",
					),
					"ZOOM_BLOCK" => array(
						"POSITION" => "right center",
					),
					"COMPONENT_TEMPLATE" => "map"
				),
				false
			); ?>
		</div>


	</div>
<? endforeach; ?>

	<div class="wrapper_inner">
		<div class="contacts_right clearfix">
			<? /*?><blockquote>
			 <?$APPLICATION->IncludeFile(SITE_DIR."include/contacts_text.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("CONTACTS_TEXT")));?>
		</blockquote><?*/ ?><? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("form-feedback-block"); ?><? $APPLICATION->IncludeComponent(
				"bitrix:form.result.new",
				"inline",
				Array(
					"CACHE_TIME" => "3600000",
					"CACHE_TYPE" => "A",
					"CHAIN_ITEM_LINK" => "",
					"CHAIN_ITEM_TEXT" => "",
					"EDIT_URL" => "",
					"IGNORE_CUSTOM_TEMPLATE" => "N",
					"LIST_URL" => "",
					"SEF_MODE" => "N",
					"SUCCESS_URL" => "?send=ok",
					"USE_EXTENDED_ERRORS" => "Y",
					"VARIABLE_ALIASES" => Array("WEB_FORM_ID" => "WEB_FORM_ID", "RESULT_ID" => "RESULT_ID"),
					"WEB_FORM_ID" => "3"
				)
			); ?><? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("form-feedback-block", ""); ?>
		</div>
	</div>

	<div class="clearboth">
	</div>
	<br><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>