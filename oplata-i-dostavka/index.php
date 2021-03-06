<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Оплата и доставка");
$oManager = \BXmaker\GeoIP\Manager::getInstance();
$city=$oManager->getCity();
if(!$city){
	$city='москва';
}
?>

	<br>
	<p style="color: #777777; font-size: 14px;">
		Получить свой заказ вы можете в любом из наших магазинов в Москве в день заказа.<br>
		Мы работаем 7 дней в неделю, 365 дней в году!
	</p>
	<p style="color: #777777; font-size: 14px;">
	</p>
	<h2>Доставка по Москве и Санкт-Петербургу:</h2>
	<p>
	</p>
	<p style="color: #777777; font-size: 14px;">
		<em>Курьером:</em>
	</p>
	<ul style="color: #777777; font-size: 14px;">
		<li>В пределах МКАД/КАД – бесплатно, при заказе на сумму более 5.000 рублей</li>
		<li>&nbsp;При заказе на сумму до 5.000 рублей – 450 рублей.</li>
		<li>За МКАД/КАД – 450 рублей +15 рублей/км.</li>
	</ul>
	<p style="color: #777777; font-size: 14px;">
		Доставка по Москве крупногабаритных грузов (свыше 25 кг, и объема 0,1 м3) независимо от стоимости заказа –
		составляет 500 рублей.
	</p>
	<p style="color: #777777; font-size: 14px;">
		<em>Курьерской компанией до пункта выдачи:</em>
	</p>
	<ul style="color: #777777; font-size: 14px;">
		<li>При заказе на сумму более 5000р – бесплатно.</li>
		<li>При заказе на меньшую сумму &nbsp;– от 100 до 250 рублей (уточняйте у менеджера).</li>
	</ul>
	<p style="color: #777777; font-size: 14px;">
		Адреса:&nbsp;пункты самовывоза Москва&nbsp;и&nbsp;пункты самовывоза Санкт-Петербург!
	</p>
	<p style="color: #777777; font-size: 14px;">
	</p>
	<h2>Доставка по России:</h2>
	<p>
	</p>
	<p style="color: #777777; font-size: 14px;">
		Фиксированная&nbsp;стоимость доставки по России компаниями Почта России и Boxberry: составляет 250 рублей, при
		условии, что вес посылки не более 2,5 кг и объем не более 0,03 м3.
	</p>
	<p style="color: #777777; font-size: 14px;">
		Посылки большего объема и веса доставляются компаниями &nbsp;DPD, Boxberry, Почта России и транспортными
		компаниями Деловые линии и ПЭК по индивидуальным расчетам.<br>
		Для расчета стоимости доставки Вы можете воспользоваться калькуляторами, представленными на сайтах курьерских
		служб, либо связаться с нашими менеджерами для получения этой информации.
	</p>
	<p style="color: #777777; font-size: 14px;">
		Оплатить заказ можно при получении (при условии,что заказ оформлен на сумму более 500 рублей).
	</p>
	<p style="color: #777777; font-size: 14px;">
		Стоимость и сроки доставки крупногабаритных грузов уточняйте у менеджера.
	</p>
	<p style="color: #777777; font-size: 14px;">
	</p>
	<h2>Самовывоз по России:</h2>
	<p>
	</p>
	<p style="color: #777777; font-size: 14px;">
		Сроки доставки до пунктов самовывоза уточняйте у менеджера в рабочие дни с пн по пт с 9:30 до 18:30.<br>
		Адреса пунктов самовывоза: <a href="/contacts/"><?=$city;?></a>
	</p>

	</p><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>