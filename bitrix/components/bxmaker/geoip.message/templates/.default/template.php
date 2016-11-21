<?    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	use Bitrix\Main\Localization\Loc as Loc;

	$COMPONENT_NAME = 'BXMAKER.GEOIP.MESSAGE';

	$randString =  $this->randString();

	if ($arParams['AJAX'] != 'Y') {

		?><span class="c_bxmaker_geoip_message_default_box" data-type="<?=$arParams['TYPE'];?>" data-template="<?=$templateName;?>" id="c_bxmaker_geoip_delivery_default_box_<?= $randString; ?>"><?

		$this->setFrameMode(true);

		$frame = $this->createFrame('c_bxmaker_geoip_delivery_default_box_' . $randString, false)->begin();

		echo $arResult['CURRENT']['MESSAGE'];

		$frame->beginStub();

		echo $arResult['DEFAULT']['MESSAGE'];

		$frame->end();

		?></span><?

	}
	else {
		echo $arResult['CURRENT']['MESSAGE'];
	}
