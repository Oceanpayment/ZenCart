<?php
/**
 * checkout_failure header_php.php
 *
 * @package page
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: header_php.php 6373 2007-05-25 20:22:34Z drbyte $
 */


// if the customer is not logged on, redirect them to the shopping cart page
if (!$_SESSION['customer_id']) {
  zen_redirect(zen_href_link(FILENAME_TIME_OUT));
}


require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));
$breadcrumb->add(NAVBAR_TITLE_1);
$breadcrumb->add(NAVBAR_TITLE_2);

// find out the last order number generated for this customer account
$orders_query = "SELECT * FROM " . TABLE_ORDERS . "
                 WHERE customers_id = :customersID
                 ORDER BY date_purchased DESC LIMIT 1";
$orders_query = $db->bindVars($orders_query, ':customersID', $_SESSION['customer_id'], 'integer');
$orders = $db->Execute($orders_query);
$orders_id = $orders->fields['orders_id'];

// use order-id generated by the actual order process
// this uses the SESSION orders_id, or if doesn't exist, grabs most recent order # for this cust (needed for paypal et al).
// Needs reworking in v1.4 for checkout-rewrite
$zv_orders_id = (isset($_SESSION['order_number_created']) && $_SESSION['order_number_created'] >= 1) ? $_SESSION['order_number_created'] : $orders_id;
$orders_id = $zv_orders_id;
$order_summary = $_SESSION['order_summary'];
unset($_SESSION['order_summary']);
unset($_SESSION['order_number_created']);


/**
 *  插件本身的的响应代码解决方案信息
 *	更新日期2015-04-12
 */
$CodeAction = array(
		'80010' => CODE_ACTION_TEXT_1,
		'80011' => CODE_ACTION_TEXT_1,
		'80012' => CODE_ACTION_TEXT_1,
		'80013' => CODE_ACTION_TEXT_1,
		'80014' => CODE_ACTION_TEXT_2,
		'80020' => CODE_ACTION_TEXT_3,
		'80021' => CODE_ACTION_TEXT_4,
		'80022' => CODE_ACTION_TEXT_5,
		'80023' => CODE_ACTION_TEXT_6,
		'80024' => CODE_ACTION_TEXT_7,
		'80025' => CODE_ACTION_TEXT_1,
		'80026' => CODE_ACTION_TEXT_8,
		'80027' => CODE_ACTION_TEXT_9,
		'80028' => CODE_ACTION_TEXT_10,
		'80030' => CODE_ACTION_TEXT_1,
		'80031' => CODE_ACTION_TEXT_11,
		'80032' => CODE_ACTION_TEXT_12,
		'80033' => CODE_ACTION_TEXT_12,
		'80034' => CODE_ACTION_TEXT_12,
		'80035' => CODE_ACTION_TEXT_12,
		'80036' => CODE_ACTION_TEXT_13,
		'80037' => CODE_ACTION_TEXT_12,
		'80050' => CODE_ACTION_TEXT_14,
		'80051' => CODE_ACTION_TEXT_15,
		'80054' => CODE_ACTION_TEXT_12,
		'80061' => CODE_ACTION_TEXT_12,
		'80062' => CODE_ACTION_TEXT_12,
		'80063' => CODE_ACTION_TEXT_12,
		'80064' => CODE_ACTION_TEXT_12,
		'80090' => CODE_ACTION_TEXT_16,
		'80091' => CODE_ACTION_TEXT_17,
		'80092' => CODE_ACTION_TEXT_18,
		'80100' => CODE_ACTION_TEXT_19,
		'80101' => CODE_ACTION_TEXT_20,
		'80120' => CODE_ACTION_TEXT_21,
		'80121' => CODE_ACTION_TEXT_21,
		'80200' => CODE_ACTION_TEXT_22,
);




/**
 *  响应代码解决方案
 */
if(MODULE_PAYMENT_OCEANPAYMENT_PHONLINEBANK_RESPONSE_CODE == 'Online'){
	//获取线上的响应代码解决方案信息
	$oceanpayment_url = 'http://www.oceanpayment.com.cn/TransResponseCode.php';
		
	$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		
	$data = array(
			'code' => $_SESSION['errorCode'],
			'lang' => $lang
	);

		
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_URL,$oceanpayment_url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_TIMEOUT,5);
		
	if (curl_errno($ch)) {
		//超时则获取插件本身
		if(isset($CodeAction[$_SESSION['errorCode']])){
			$op_actionMsg = $CodeAction[$_SESSION['errorCode']];
		}else{
			$op_actionMsg = $_SESSION['payment_details'];
		}
		
	}else{
		$op_actionMsg = curl_exec($ch);
		if(empty($op_actionMsg)){
			$op_actionMsg = $_SESSION['payment_details'];
		}
	}
		
}elseif(MODULE_PAYMENT_OCEANPAYMENT_PHONLINEBANK_RESPONSE_CODE == 'Local'){
	//获取插件本身的的响应代码解决方案信息
	if(isset($CodeAction[$_SESSION['errorCode']])){
		$op_actionMsg = $CodeAction[$_SESSION['errorCode']];
	}else{
		$op_actionMsg = $_SESSION['payment_details'];
	}
}








// This should be last line of the script:
$zco_notifier->notify('NOTIFY_HEADER_END_CHECKOUT_SUCCESS');
?>