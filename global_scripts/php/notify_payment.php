<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once("mysql_connection.php");



// Clave determinada en cuentadigital para validar las operaciones.
define("API_KEY", "kwpQPJJS7GD3GmCF");

// Numero de cuentadigital utilizada. En la DB se guarda este numero. 1: agusfn, 2: tomasfn, 3: rfn07
define("CD_ACCOUNT", 3);





$requestBody = file_get_contents('php://input');
$lines = explode("\n", $requestBody);

// Remove empty lines.
$lines = array_filter($lines);


$totalPayments = 0;
$registeredPayments = 0;
$alreadyRegistered = 0;
$nonWebsitePayments = 0;
$checksumFailed = 0;


foreach($lines as $line)
{

	$paymentData = explode(",", $line);

	if(sizeof($paymentData) != 10)
		continue;

	$operationClass = $paymentData[0]; // 1: credit, 2: debit, 3: final line
	$payDate = date_create_from_format('dmYHis', $paymentData[1].$paymentData[2]);
	$amountPaid = floatval($paymentData[3]);
	$netReceived = subtractCdFee($amountPaid);
	$barCode = $paymentData[4];
	$reference = $paymentData[5];
	$payMethod = $paymentData[6];
	$uniqueOperationCode = $paymentData[7];
	$checkSum = $paymentData[8];
	//$orderInList = $paymentData[9];
	

	if($operationClass != 1)
		continue;

	$totalPayments++;

	if(!checkCheckSum($checkSum, $operationClass, $paymentData[1].$paymentData[2], $paymentData[3], $barCode, $reference, $uniqueOperationCode)) {
		$checksumFailed++;
		continue;
	}
	
	if(!preg_match("#^ID-([JP][0-9]{5,6})-.*USD-(.*)ARS$#", $reference, $refData)) {
		$nonWebsitePayments++;
		continue;
	}

	$orderId = $refData[1];
	$orderTotal = floatval($refData[2]);

	if(isPaymentAlreadyRegistered($barCode, $uniqueOperationCode)) {
		$alreadyRegistered++;
		continue;
	}

	if(unpaidOrderExists($orderId, $barCode)) {
		markOrderAsPaid($orderId, date_format($payDate, "Y-m-d H:i:s"));
	}


	registerPayment(
		date_format($payDate, "Y-m-d H:i:s"),
		$amountPaid,
		$netReceived,
		$barCode,
		$orderId,
		($amountPaid < ($orderTotal - 7) || $amountPaid > ($orderTotal + 7)) ? true : false,
		$uniqueOperationCode,
		$payMethod
	);

	$registeredPayments++;

}


file_put_contents("payments_webhook.txt", date("d/m/Y H:i:s").". Registrados: ".$registeredPayments.". Ya registrados: ".$alreadyRegistered.". No del sitio: ".$nonWebsitePayments.". Checksum failed: ".$checksumFailed.". Totales: ".$totalPayments.". Operaciones: ".(sizeof($lines)-1)."\r\n", FILE_APPEND);

echo "OK";







/**
 * Check if the checksum provided is correct.
 * @param  string $providedCheckSum [description]
 * @param  string $operationClass   [description]
 * @param  string $date             DDMMYYYYHHMMSS (dmYHis)
 * @param  string $amountPaid       asi como vino en el csv
 * @param  string $barCode          [description]
 * @param  string $reference        [description]
 * @param  string $uniqueOpCode     [description]
 * @return boolean                   [description]
 */
function checkCheckSum($providedCheckSum, $operationClass, $date, $amountPaid, $barCode, $reference, $uniqueOpCode)
{
	$calculatedCheckSum = hash('sha256', $operationClass.$date.$amountPaid.$barCode.$reference.$uniqueOpCode.API_KEY);
	return hash_equals($calculatedCheckSum, $providedCheckSum);
}



/**
 * Register a new cuentadigital Payment in the database.
 * @param  string $paidAt       datetime Y-m-d H:i:s
 * @param  float $amountPaid   [description]
 * @param  float $netAmount    [description]
 * @param  string $barCode      [description]
 * @param  string $orderId      [description]
 * @param  boolean $priceWarning [description]
 * @param  string $uniqueOpCode [description]
 * @param  string $payMethod [description]
 * @return null               [description]
 */
function registerPayment($paidAt, $amountPaid, $netAmount, $barCode, $orderId, $priceWarning, $uniqueOpCode, $payMethod)
{
	global $con;
	
	$sql = "INSERT INTO `cd_payments` (`number`, `cd_account`, `registered_at`, `paid_at`, `amount_paid`, 
	`net_ammount`, `invoice_number`, `site_payment`, `order_id`, `description`, `price_warning`, 
	`unique_operation_code`, `payment_method`) 
		VALUES (NULL, ".CD_ACCOUNT.", NOW(), '".mysqli_real_escape_string($con, $paidAt)."', ".$amountPaid.", 
		".$netAmount.", '".mysqli_real_escape_string($con, $barCode)."', 1, '".mysqli_real_escape_string($con, $orderId)."', '', ".($priceWarning ? 1 : 0).", 
		'".mysqli_real_escape_string($con, $uniqueOpCode)."', '".mysqli_real_escape_string($con, $payMethod)."')";
	
	mysqli_query($con, $sql);
}


/**
 * Check if an unpaid order exists with given ID and coupon barcode/invoice number.
 * @param  [type] $orderId [description]
 * @param  [type] $barCode [description]
 * @return [type]          [description]
 */
function unpaidOrderExists($orderId, $barCode)
{
	global $con;

	$sql = "SELECT * FROM `orders` WHERE `order_id`='".mysqli_real_escape_string($con, $orderId)."' AND 
	`order_purchaseticket` LIKE '%".mysqli_real_escape_string($con, $barCode)."' AND 
	`order_confirmed_payment` = 0";

	$query = mysqli_query($con, $sql);

	if(mysqli_num_rows($query) >= 1)
		return true;
	else
		return false;

}


/**
 * Mark an order as Paid.
 * @param  string $orderId
 * @param  string $date    Date of payment. Format Y-m-d H:i:s
 * @return null          [description]
 */
function markOrderAsPaid($orderId, $date)
{
	global $con;
	mysqli_query($con, "UPDATE `orders` SET `order_confirmed_payment`=1, `order_payment_time`='".mysqli_real_escape_string($con, $date)."' 
		WHERE `order_id`='".mysqli_real_escape_string($con, $orderId)."'");
}




/**
 * Check if a payment has already been registered.
 * @param  [type]  $barCode      [description]
 * @param  [type]  $uniqueOpCode [description]
 * @return boolean               [description]
 */
function isPaymentAlreadyRegistered($barCode, $uniqueOpCode)
{
	global $con;

	$sql = "SELECT COUNT(*) FROM `cd_payments` WHERE `cd_account` = ".CD_ACCOUNT." AND `invoice_number` = '".mysqli_real_escape_string($con, $barCode)."' 
	AND (`unique_operation_code` = '".mysqli_real_escape_string($con, $uniqueOpCode)."' OR `paid_at` < '2019-08-01')";

	$query = mysqli_query($con, $sql);

	if(mysqli_fetch_row($query)[0] >= 1)
		return true;
	else
		return false;
}


/**
 * Subtract CD fees from payment.
 * @param  [type] $amountPaid [description]
 * @return [type]             [description]
 */
function subtractCdFee($amountPaid)
{
	$fee = 0.0399 * $amountPaid + 1.25;
	$netReceived = $amountPaid - $fee - 0.21*$fee;
	return round($netReceived, 2);
}