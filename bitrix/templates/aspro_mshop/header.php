<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if ($GET["debug"] == "y") {
	error_reporting(E_ERROR | E_PARSE);
}
IncludeTemplateLangFile(__FILE__);
global $APPLICATION, $TEMPLATE_OPTIONS, $arSite, $GLOBALS;
$arSite = CSite::GetByID(SITE_ID)->Fetch();
$htmlClass = ($_REQUEST && isset($_REQUEST['print']) ? 'print' : false);
?>
	<!DOCTYPE html>
<html xml:lang="<?= LANGUAGE_ID ?>" lang="<?= LANGUAGE_ID ?>"
	  xmlns="http://www.w3.org/1999/xhtml" <?= ($htmlClass ? 'class="' . $htmlClass . '"' : '') ?>>
	<head>
		<!-- <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/css/Foundation2-1-6.css"> -->
		<!-- <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/css/window.css"> -->
		<title><? $APPLICATION->ShowTitle(false) ?></title>
		<? $APPLICATION->ShowMeta("viewport"); ?>
		<? $APPLICATION->ShowMeta("HandheldFriendly"); ?>
		<? $APPLICATION->ShowMeta("apple-mobile-web-app-capable", "yes"); ?>
		<? $APPLICATION->ShowMeta("apple-mobile-web-app-status-bar-style"); ?>
		<? $APPLICATION->ShowMeta("SKYPE_TOOLBAR"); ?>

		<? $APPLICATION->AddHeadString('<script>BX.message(' . CUtil::PhpToJSObject($MESS, false) . ')</script>', true); ?>
		<? if (CModule::IncludeModule("aspro.mshop")) {
			CMShop::Start(SITE_ID);
		} ?>
		<!--[if gte IE 9]>
		<style type="text/css">.basket_button, .button30, .icon {
			filter: none;
		}</style><![endif]-->
		<link
			href='<?= CMain::IsHTTPS() ? 'https' : 'http' ?>://fonts.googleapis.com/css?family=Ubuntu:400,500,700,400italic&subset=latin,cyrillic'
			rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/css/font-awesome.css">

		<link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/css/font/font-awesome.css">
		<script src="<?= SITE_DIR?>bitrix/js/aspro.mshop/aspro.mshop/jquery-1.8.3.min.js"></script>
		
		<? $APPLICATION->ShowHead(); ?>
	</head>
<body id="main">
	<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>
	<div style="width:100%; height: 2px;" id="close_window"></div>
<? if (!CModule::IncludeModule("aspro.mshop")) { ?>
	<center><? $APPLICATION->IncludeFile(SITE_DIR . "include/error_include_module.php"); ?></center></body></html><? die(); ?><? } ?>
<? $APPLICATION->IncludeComponent("aspro:theme.mshop", ".default", array("COMPONENT_TEMPLATE" => ".default"), false); ?>
<? CMShop::SetJSOptions(); ?>
<? $isFrontPage = CSite::InDir(SITE_DIR . 'index.php'); ?>
<? $isContactsPage = CSite::InDir(SITE_DIR . 'contacts/'); ?>
<? $isBasketPage = CSite::InDir(SITE_DIR . 'basket/'); ?>
<div
	class="wrapper <?= ($TEMPLATE_OPTIONS["HEAD"]["CURRENT_MENU_COLOR"] != "none" ? "has_menu" : ""); ?> h_color_<?= $TEMPLATE_OPTIONS["HEAD"]["CURRENT_HEAD_COLOR"]; ?> m_color_<?= $TEMPLATE_OPTIONS["HEAD"]["CURRENT_MENU_COLOR"]; ?> <?= ($isFrontPage ? "front_page" : ""); ?> basket_<?= strToLower($TEMPLATE_OPTIONS["BASKET"]["CURRENT_VALUE"]); ?> head_<?= strToLower($TEMPLATE_OPTIONS["HEAD"]["CURRENT_VALUE"]); ?> banner_<?= strToLower($TEMPLATE_OPTIONS["BANNER_WIDTH"]["CURRENT_VALUE"]); ?>">
	<div class="header_wrap <?= strtolower($TEMPLATE_OPTIONS["HEAD_COLOR"]["CURRENT_VALUE"]) ?>">
		<div class="top-h-row">
			<div class="wrapper_inner clearfix">
				<div class="city-container">
					<? $APPLICATION->IncludeComponent(
						"bxmaker:geoip.line",
						".default",
						Array(
							"CACHE_TIME" => "0",
							"CACHE_TYPE" => "A",
							"COMPONENT_TEMPLATE" => ".default"
						)
					); ?>

				</div>
				<div class="content_menu">
					<? $APPLICATION->IncludeComponent("bitrix:menu", "top_content_row", array(
						"ROOT_MENU_TYPE" => $TEMPLATE_OPTIONS["HEAD"]["CURRENT_MENU"],
						"MENU_CACHE_TYPE" => "Y",
						"MENU_CACHE_TIME" => "86400",
						"MENU_CACHE_USE_GROUPS" => "N",
						"MENU_CACHE_GET_VARS" => array(),
						"MAX_LEVEL" => "1",
						"CHILD_MENU_TYPE" => "left",
						"USE_EXT" => "N",
						"DELAY" => "N",
						"ALLOW_MULTI_SELECT" => "N",
					), false
					); ?>
				</div>

				<div class="phones">
							<span class="phone_wrap">
								<span class="icons"></span>
								<span class="phone_text">
									<? $APPLICATION->IncludeFile(SITE_DIR . "include/phone.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("PHONE"))); ?>
								</span>
							</span>
							<span class="order_wrap_btn">
								<? if (\Bitrix\Main\Config\Option::get("aspro.mshop", "SHOW_CALLBACK", "Y") != "N"): ?>
									<span class="callback_btn"><?= GetMessage("CALLBACK") ?></span>
								<? endif; ?>
							</span>
				</div>
				<div class="h-user-block" id="personal_block">
					<div class="form_mobile_block">
						<div
							class="search_middle_block"><? include($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include/search.title.catalog3.php'); ?></div>
					</div>
					<? $APPLICATION->IncludeComponent(
						"bitrix:system.auth.form",
						"top",
						array(
							"REGISTER_URL" => SITE_DIR . "auth/registration/",
							"FORGOT_PASSWORD_URL" => SITE_DIR . "auth/forgot-password/",
							"PROFILE_URL" => SITE_DIR . "personal/",
							"SHOW_ERRORS" => "Y",
							"COMPONENT_TEMPLATE" => "top",
							"COMPOSITE_FRAME_MODE" => "A",
							"COMPOSITE_FRAME_TYPE" => "AUTO"
						),
						false
					); ?>
				</div>
				<!--						<div class="clearfix"></div>-->
			</div>
		</div>
		<header id="header">
			<div class="wrapper_inner">
				<table class="middle-h-row">
					<tr>
						<td class="logo_wrapp">
							<div class="logo">
								<? CMShop::ShowLogo(); ?>
							</div>
						</td>
						<td class="center_block">
							<div class="main-nav">
								<? include($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include/menu.top_general_multilevel.php'); ?>
							</div>

							<div class="middle_phone">
								<div class="phones">
										<span class="phone_wrap">
											<span class="icons"></span>
											<span class="phone_text">
												<? $APPLICATION->IncludeFile(SITE_DIR . "include/phone.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("PHONE"))); ?>
											</span>
										</span>
										<span class="order_wrap_btn">
											<? if (\Bitrix\Main\Config\Option::get("aspro.mshop", "SHOW_CALLBACK", "Y") != "N"): ?>
												<span class="callback_btn"><?= GetMessage("CALLBACK") ?></span>
											<? endif; ?>
										</span>
								</div>
							</div>
							<div class="search">
								<? include($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include/search.title.catalog.php'); ?>
							</div>
						</td>
						<td class="basket_wrapp">
							<div class="wrapp_all_icons">
								<div class="header-compare-block icon_block iblock" id="compare_line">
									<? include($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include/catalog.compare.list.compare_top.php'); ?>
								</div>
								<div class="header-cart" id="basket_line">
									<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("header-cart"); ?>
									<? if ($TEMPLATE_OPTIONS["BASKET"]["CURRENT_VALUE"] === "FLY" && !$isBasketPage && !CSite::InDir(SITE_DIR . "order/")): ?>
										<script type="text/javascript">
											$(document).ready(function () {
												$.ajax({
													url: arMShopOptions["SITE_DIR"] + "ajax/basket_fly.php",
													type: "post",
													success: function (html) {
														$("#basket_line").append(html);
													}
												});
											});
										</script>
									<? else: ?>
										<? $APPLICATION->IncludeFile(SITE_DIR . "include/basket_top.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("BASKET_TOP"))); ?>
									<? endif; ?>
									<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("header-cart", ""); ?>
								</div>
							</div>
							<div class="clearfix"></div>
						</td>
					</tr>
				</table>
			</div>
			<div class="catalog_menu">
				<div class="wrapper_inner">
					<div class="wrapper_middle_menu">
						<? include($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include/menu.top_catalog_multilevel.php'); ?>
					</div>
				</div>
			</div>
		</header>
	</div>
<? if (!$isFrontPage): ?>
	<div class="wrapper_inner">
		<section class="middle">
			<div class="container">
				<? $APPLICATION->IncludeComponent("bitrix:breadcrumb", "mshop", array(
					"START_FROM" => "0",
					"PATH" => "",
					"SITE_ID" => "-",
					"SHOW_SUBSECTIONS" => "N"
				),
					false
				); ?>
				<h1><?= $APPLICATION->ShowTitle(false); ?></h1>
				<? if ($isContactsPage): ?>
			</div>
		</section>
	</div>
	<? else: ?>
	<div id="content">
	<? if (CSite::InDir(SITE_DIR . 'help/') || CSite::InDir(SITE_DIR . 'company/') || CSite::InDir(SITE_DIR . 'info/')): ?>
	<div class="left_block">
		<? $APPLICATION->IncludeComponent("bitrix:menu", "left_menu", array(
			"ROOT_MENU_TYPE" => "left",
			"MENU_CACHE_TYPE" => "A",
			"MENU_CACHE_TIME" => "3600000",
			"MENU_CACHE_USE_GROUPS" => "N",
			"MENU_CACHE_GET_VARS" => "",
			"MAX_LEVEL" => "1",
			"CHILD_MENU_TYPE" => "left",
			"USE_EXT" => "Y",
			"DELAY" => "N",
			"ALLOW_MULTI_SELECT" => "N"),
			false, array("ACTIVE_COMPONENT" => "Y")
		); ?>
	</div>
	<div class="right_block">
	<? endif; ?>
	<? endif; ?>
<? endif; ?>


	<script>
		/* это просто писец
		 $("#close_window").mouseover(function(){

		 $("a#conme").fancybox({
		 "hideOnContentClick" :true,
		 });
		 setTimeout(function( event ){
		 $('a#conme').trigger('click');
		 }, 10);
		 });

		 */
		/*
		 $(document).ready(function() {
		 $(document).mousemove(function(e) {
		 if(e.pageY <= 5) {
		 // Launch MODAL BOX
		 $('#really-close').modal('show');
		 }
		 });
		 });

		 $(function() {
		 $(document).on('mousemove',function(e) {
		 if(e.pageY <= 5) {
		 // Launch MODAL BOX
		 $('#really-close').modal('show');
		 $(document).off('mousemove');
		 }
		 });
		 });

		 */
	</script>
<? ?>
	<a id="conme" style="display: none;" href="#contactsmediv">(название кнопки)</a>

	<div style="display:none;">
		<div id="contactsmediv" style="padding: 10px;">
			<? $APPLICATION->IncludeComponent(
				"bitrix:main.feedback",
				"poopap_modal",
				Array(
					"COMPONENT_TEMPLATE" => "poopap_modal",
					"EMAIL_TO" => "info@mos-teplo.com",
					"EVENT_MESSAGE_ID" => array("7"),
					"OK_TEXT" => "Спасибо, ваше сообщение принято.",
					"REQUIRED_FIELDS" => array("NAME", "EMAIL"),
					"USE_CAPTCHA" => "Y"
				)
			); ?>
		</div>
	</div>
<? /*?>
<div style="visibility: hidden;display:none;">
<div id="buy_in_credit_form">
	<?$APPLICATION->IncludeComponent(
		"bitrix:form.result.new",
		"inline",
		Array(
			"WEB_FORM_ID" => $arParams["ASK_FORM_ID"],
			"IGNORE_CUSTOM_TEMPLATE" => "N",
			"USE_EXTENDED_ERRORS" => "N",
			"SEF_MODE" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "3600",
			"LIST_URL" => "",
			"EDIT_URL" => "",
			"SUCCESS_URL" => "?send=ok",
			"CHAIN_ITEM_TEXT" => "",
			"CHAIN_ITEM_LINK" => "",
			"VARIABLE_ALIASES" => Array("WEB_FORM_ID" => "WEB_FORM_ID", "RESULT_ID" => "RESULT_ID"),
			"AJAX_MODE" => "Y",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
		)
	);?>
</div></div>
<?*/ ?>

<? if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") $APPLICATION->RestartBuffer(); ?>