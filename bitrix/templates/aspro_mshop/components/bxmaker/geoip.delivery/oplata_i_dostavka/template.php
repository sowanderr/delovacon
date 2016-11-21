<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	use Bitrix\Main\Localization\Loc as Loc;

	if ($arParams['AJAX'] != 'Y') {
		$this->setFrameMode(true);
	}

	$randString = $this->randString();


	//	if (preg_match('/\d+$/', $item['DATA']['TRANSIT'], $match)) {
	//		$days = intval($match[0]);
	//	}

	$COMPONENT_NAME = 'BXMAKER.GEOIP.DELIVERY';

	if ($arParams['AJAX'] != 'Y')
	{
		?><div class="c-bxmaker_geoip_delivery_default_box" id="c_bxmaker_geoip_delivery_default_box_<?= $randString; ?>"><?

		$frame = $this->createFrame('c_bxmaker_geoip_delivery_default_box_' . $randString, false)->begin('');
		}
	?>

	<!-- prolog -->

	<? if (strlen(trim($arResult['DELIVER_BLOCK_PROLOG'])) > 0): ?>
		<div
			class="prolog_box"><h2><?= preg_replace('/#CITY#/', '<span class="bxmaker_geoip_epilog_city_name">' . $arResult['DELIVERY_CITY'] . '</span>', trim($arResult['DELIVER_BLOCK_PROLOG'])); ?></h2></div>
	<? endif; ?>
	<!-- /prolog -->


	<!-- rows -->
	<div class="delivery_rows_box">
		<? if ($arResult['DELIVERY_BLOCKS_ON'] == 'Y'): ?>
			<div class="block_on_box">

				<!-- Courier -->
				<?
					if (count($arResult['COURIER']) && array_intersect($arResult['COURIER'], array_keys($arResult['ITEMS']))) {

						$arResult['COURIER'] = array_slice(array_intersect($arResult['COURIER'], array_keys($arResult['ITEMS'])), 0, 1);

						foreach ($arResult['ITEMS'] as $id => $item) {
							if (!in_array($id, $arResult['COURIER'])) continue;

							echo '<div data-delivery-id="' . $id . '" class="item_box item_courier">';

							echo Loc::getMessage($COMPONENT_NAME . 'COURIER_LABEL');

							if ($arResult['DELIVERY_PRICE_SHOW'] == 'Y' && $arResult['DELIVERY_DATE_SHOW'] == 'Y') {

								if (strlen(trim($item['PERIOD_TEXT'])) > 0) {
									//									echo ' - ';
									echo '<span class="delivery_time_box"> ' . trim($item['PERIOD_TEXT']) . '<span>,';
								}

								echo '<span class="delivery_price_box">' . Loc::getMessage($COMPONENT_NAME . 'OT') . $item['PRICE_FORMATED'] . '</span>';
							}
							elseif ($arResult['DELIVERY_DATE_SHOW'] == 'Y') {
								if (strlen(trim($item['PERIOD_TEXT'])) > 0) {
									//									echo ' - ';
									echo '<span class="delivery_time_box"> ' . trim($item['PERIOD_TEXT']) . '<span>';
								}
							}
							elseif ($arResult['DELIVERY_PRICE_SHOW'] == 'Y') {
								echo '<span class="delivery_price_box">' . Loc::getMessage($COMPONENT_NAME . 'OT') . $item['PRICE_FORMATED'] . '</span>';
							}


							echo '</div>';
						}
					}
				?>
				<!-- /Courier -->
				<!-- Pickup -->
				<?
					if (count($arResult['PICKUP']) && array_intersect($arResult['PICKUP'], array_keys($arResult['ITEMS']))) {

						$arResult['PICKUP'] = array_slice(array_intersect($arResult['PICKUP'], array_keys($arResult['ITEMS'])), 0, 1);

						foreach ($arResult['ITEMS'] as $id => $item) {
							if (!in_array($id, $arResult['PICKUP'])) continue;

							echo '<div data-delivery-id="' . $id . '" class="item_box item_pickup">';

							echo Loc::getMessage($COMPONENT_NAME . 'PICKUP_LABEL');

							if ($arResult['DELIVERY_PRICE_SHOW'] == 'Y' && $arResult['DELIVERY_DATE_SHOW'] == 'Y') {

								if (strlen(trim($item['PERIOD_TEXT'])) > 0) {
									//									echo ' - ';
									echo '<span class="delivery_time_box"> ' . trim($item['PERIOD_TEXT']) . '<span>,';
								}

								echo '<span class="delivery_price_box">' . Loc::getMessage($COMPONENT_NAME . 'OT') . $item['PRICE_FORMATED'] . '</span>';
							}
							elseif ($arResult['DELIVERY_DATE_SHOW'] == 'Y') {
								if (strlen(trim($item['PERIOD_TEXT'])) > 0) {
									//									echo ' - ';
									echo '<span class="delivery_time_box"> ' . trim($item['PERIOD_TEXT']) . '<span>';
								}
							}
							elseif ($arResult['DELIVERY_PRICE_SHOW'] == 'Y') {
								echo '<span class="delivery_price_box">' . Loc::getMessage($COMPONENT_NAME . 'OT') . $item['PRICE_FORMATED'] . '</span>';
							}

							echo '</div>';
						}
					}
				?>
				<!-- /Pickup -->
				<!-- Pochta -->
				<?
					if (count($arResult['POCHTA']) && array_intersect($arResult['POCHTA'], array_keys($arResult['ITEMS']))) {

						$arResult['POCHTA'] = array_slice(array_intersect($arResult['POCHTA'], array_keys($arResult['ITEMS'])), 0, 1);

						foreach ($arResult['ITEMS'] as $id => $item) {
							if (!in_array($id, $arResult['POCHTA'])) continue;

							echo '<div data-delivery-id="' . $id . '" class="item_box item_pickup">';

							echo Loc::getMessage($COMPONENT_NAME . 'POCHTA_LABEL');

							if ($arResult['DELIVERY_PRICE_SHOW'] == 'Y' && $arResult['DELIVERY_DATE_SHOW'] == 'Y') {

								if (strlen(trim($item['PERIOD_TEXT'])) > 0) {
									//									echo ' - ';
									echo '<span class="delivery_time_box"> ' . trim($item['PERIOD_TEXT']) . '<span>,';
								}

								echo '<span class="delivery_price_box">' . Loc::getMessage($COMPONENT_NAME . 'OT') . $item['PRICE_FORMATED'] . '</span>';
							}
							elseif ($arResult['DELIVERY_DATE_SHOW'] == 'Y') {
								if (strlen(trim($item['PERIOD_TEXT'])) > 0) {
									//									echo ' - ';
									echo '<span class="delivery_time_box"> ' . trim($item['PERIOD_TEXT']) . '<span>';
								}
							}
							elseif ($arResult['DELIVERY_PRICE_SHOW'] == 'Y') {
								echo '<span class="delivery_price_box">' . Loc::getMessage($COMPONENT_NAME . 'OT') . $item['PRICE_FORMATED'] . '</span>';
							}

							echo '</div>';
						}
					}
				?>
				<!-- /Pochta -->
			</div>
		<? else: ?>

			<div class="block_off_box">
				<?
					foreach ($arResult['ITEMS'] as $id => $item) {
						if (!in_array($id, $arResult['COURIER'])) continue;

						echo '<div data-delivery-id="' . $id . '" class="item_box item_courier">';

						echo $item['NAME'];

						if ($arResult['DELIVERY_PRICE_SHOW'] == 'Y' && $arResult['DELIVERY_DATE_SHOW'] == 'Y') {

							if (strlen(trim($item['PERIOD_TEXT'])) > 0) {
								//								echo ' - ';
								echo '<span class="delivery_time_box"> ' . trim($item['PERIOD_TEXT']) . '<span>,';
							}

							echo '<span class="delivery_price_box">' . Loc::getMessage($COMPONENT_NAME . 'OT') . $item['PRICE_FORMATED'] . '</span>';
						}
						elseif ($arResult['DELIVERY_DATE_SHOW'] == 'Y') {
							if (strlen(trim($item['PERIOD_TEXT'])) > 0) {
								//								echo ' - ';
								echo '<span class="delivery_time_box"> ' . trim($item['PERIOD_TEXT']) . '<span>';
							}
						}
						elseif ($arResult['DELIVERY_PRICE_SHOW'] == 'Y') {
							echo '<span class="delivery_price_box">' . Loc::getMessage($COMPONENT_NAME . 'OT') . $item['PRICE_FORMATED'] . '</span>';
						}

						echo '</div>';
					}
				?>

			</div>
			<?
		endif;
		?>
	</div>
	<!-- /rows -->

	<!-- epilog -->
	<? if (strlen(trim($arResult['DELIVER_BLOCK_EPILOG'])) > 0): ?>
		<div class="epilog_box"><?= trim($arResult['DELIVER_BLOCK_EPILOG']); ?></div>
	<? endif; ?>
	<!-- /epilog -->

	<?
		if ($arParams['AJAX'] != 'Y') {
		$frame->end();
	?></div><?
	}

?>