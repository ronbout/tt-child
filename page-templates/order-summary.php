<?php
/*
Template Name: Order Summary
*/

function productPayment($id)
{
global $wpdb;
$totalPay = $wpdb->get_results($wpdb->prepare("SELECT sum(amount) as totalPay FROM " . $wpdb->prefix . "offer_payments where pid = %d", $id));
return $totalPay[0]->totalPay;
}
?>

<!DOCTYPE HTML>

<html>

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
    <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>

    <title>TheTaste</title>
    <style>

        .pimage {
            font-size: 14pt;
        }

        .pimage img {
            vertical-align: text-bottom;
            margin-right: 15px;
        }
    </style>
</head>

<body>


<section>
</br>
</br>

<?php if (current_user_can('administrator')) { ?>

<div class="container">
<?php
if (isset($_GET['product_id'])) {

    global $wpdb;

    $tproduct = 0;
    $redeem = 0;

    $id = 1;
    $pid = $_GET['product_id'];
    $pass = '';
    $total = 0;
    $myrows1 = $wpdb->get_results($wpdb->prepare("SELECT p.post_title, p.id
FROM " . $wpdb->prefix . "posts p
JOIN " . $wpdb->prefix . "postmeta pw ON p.id = pw.post_id
WHERE p.post_type = 'product'
AND p.post_status = 'publish'
AND pw.meta_key = 'RestaurantPassword'
AND p.id = %d",$pid));


    if (count($myrows1) > 0) {
        if (isset($_POST['oii'])) {

            $oii = $_POST['oii'];
            $prid = $_POST['prid'];
            $rows_affected = $wpdb->query(
                $wpdb->prepare(
                    "UPDATE " . $wpdb->prefix . "woocommerce_order_items
SET downloaded = '1' where order_item_id = %d ", $oii
                ) // $wpdb->prepare
            ); // $wpdb->query

            $wpdb->prepare(
                "UPDATE " . $wpdb->prefix . "woocommerce_order_items
SET downloaded = '1' where order_item_id = %d ", $oii
            );


        }

        $myrows = $wpdb->get_results($wpdb->prepare("SELECT p.post_title,
im.meta_value AS quan,
im1.meta_value AS productID,
bf.meta_value AS b_fname,
bl.meta_value AS b_lname,
be.meta_value AS b_email,
i.order_id, i.order_item_id as itemid, i.downloaded as downloaded
FROM " . $wpdb->prefix . "woocommerce_order_itemmeta im
JOIN " . $wpdb->prefix . "woocommerce_order_itemmeta im1 ON im.order_item_id = im1.order_item_id
LEFT JOIN " . $wpdb->prefix . "woocommerce_order_items i ON im.order_item_id = i.order_item_id
LEFT JOIN " . $wpdb->prefix . "posts o ON i.order_id = o.id
JOIN " . $wpdb->prefix . "posts p ON im1.meta_value = p.id
LEFT JOIN " . $wpdb->prefix . "postmeta bf ON o.id = bf.post_id
LEFT JOIN " . $wpdb->prefix . "postmeta bl ON o.id = bl.post_id
LEFT JOIN " . $wpdb->prefix . "postmeta be ON o.id = be.post_id
WHERE im.meta_key = '_qty'
AND im1.meta_key = '_product_id'
AND bf.meta_key = '_billing_first_name'
AND bf.post_id = o.id
AND bl.meta_key = '_billing_last_name'
AND bl.post_id = o.id
AND be.meta_key = '_billing_email'
AND be.post_id = o.id
AND o.post_status = 'wc-completed'
AND o.post_type = 'shop_order'
AND im1.meta_value = %d group by o.id", $pid));


        $gr = $wpdb->get_results($wpdb->prepare("SELECT pm.meta_value
FROM " . $wpdb->prefix . "postmeta pm
WHERE pm.post_id = %d
AND pm.meta_key = '_price'", $pid));


        $vat_val = $wpdb->get_results($wpdb->prepare("SELECT pm.meta_value
FROM " . $wpdb->prefix . "postmeta pm
WHERE pm.post_id = %d
AND pm.meta_key = 'Vat'", $pid));

        $commission_val = $wpdb->get_results($wpdb->prepare("SELECT pm.meta_value
FROM " . $wpdb->prefix . "postmeta pm
WHERE pm.post_id = %d
AND pm.meta_key = 'Commission'", $pid));



        ?>


        <div class="row">
            <div class="col-md-12">


                <p class="pimage"><?= get_the_post_thumbnail($pid, 'shop_catalog', array('class' => 'img-thumbnail')); ?><?= $myrows[0]->post_title ?></p>
            </div>
        </div>


        <div class="panel panel-default">
            <div class="panel-heading"><h2 style="text-align: center">List of Orders </h2></div>
            <div class="panel-body">

                <table class="table table-striped table-bordered">

                    <thead>

                    <th>Order ID</th>

                    <th>Customer Name</th>

                    <th>Customer Email</th>

                    <th>Quantity</th>
                    <th>Redeem</th>

                    </thead>

                    <tbody>

                    <?php foreach ($myrows as $val) {
                        $tproduct = $tproduct + 1;


                        ?>

                        <tr>

                            <td><?= $val->order_id ?></td>

                            <td><?= $val->b_fname . ' ' . $val->b_lname ?></td>

				<?php
                                	if ($val->downloaded == '1') {
                                ?>
                            		<td><?= $val->b_email ?></td>

 				<?php
                                } else {
				?>
					<td>*** GDPR BLANKED EMAIL ***</td>
				<?php
				}
                                ?>
                            <td><?= $val->quan ?> </td>
                            <td>
                                <?php
                                if ($val->downloaded == '0') {
                                    ?>
                                    <form method="post" action="">
                                        <input type="hidden" name="prid" value="<?= $val->order_id ?>">
                                        <input type="hidden" name="oii" value="<?= $val->itemid ?>">
                                        <input type="hidden" name="product_id" value="<?= $pid ?>">
                                        <input type="hidden" name="product_pass" value="<?= $pass ?>">
                                        <input type="submit" value="Redeem">

                                    </form>
                                <?php
                                } else {
                                    $redeem = $redeem + $val->quan;
                                    echo 'Served';
                                }
                                ?>

                            </td>


                        </tr>

                        <?php

                        
			$total = $total + $val->quan;
                        $grevenue = $redeem * $gr[0]->meta_value;
                        $commission = ($grevenue / 100) * $commission_val[0]->meta_value;
                        $vat = ($commission / 100) * $vat_val[0]->meta_value;
                        $grevenue = round($grevenue, 2);
                        $commission = round($commission, 2);
                        $vat = round($vat, 2);
                        $payable = $grevenue - ($commission + $vat);
                        $payable = round($payable, 2);

                    }

                    ?>



                    <tr>

                        <td></td>

                        <td></td>

                        <td></td>

                        <td></td>

                    </tr>
                    <tr>

                        <td></td>

                        <td></td>
                        <td></td>

                        <td style="text-align: right; padding-right: 120px;"><b>Gross Revenue</b></td>

                        <td><b><?= get_woocommerce_currency_symbol() ?> <?= number_format($grevenue, 2) ?></b></td>

                    </tr>
                    <tr>

                        <td></td>

                        <td></td>
                        <td></td>

                        <td style="text-align: right; padding-right: 120px;">Commission</td>

                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($commission, 2) ?> </td>

                    </tr>
                    <tr>

                        <td></td>

                        <td></td>
                        <td></td>

                        <td style="text-align: right; padding-right: 120px;">Vat</td>

                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($vat, 2) ?> </td>

                    </tr>
                    <tr>

                        <td></td>

                        <td></td>
                        <td></td>

                        <td style="text-align: right; padding-right: 120px;"><b>Net Payable </b></td>

                        <td><b><?= get_woocommerce_currency_symbol() ?> <?= number_format($payable, 2) ?></b></td>

                    </tr>
                    <tr>

                        <td></td>

                        <td></td>
                        <td></td>

                        <td style="text-align: right; padding-right: 120px;"><b>Redeemed</b></td>

                        <td>Served <?= $redeem ?> customers <br> out of a possible <?= $total ?></td>

                    </tr>

                    </tbody>

                </table>

            </div>
        </div>
    <?php


    } else {

        echo '<div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">TheTaste Error -</span> Values entered do not match in our system
</div>';
    }

}


	elseif(isset($_GET['makePaymentPage']))
	{
		
    global $wpdb;

    $tproduct = 0;
    $redeem = 0;

    $id = 1;
    $product_id = $_GET['makePaymentPage'];
    $pass = '';
    $total = 0;
    $myrows1 = $wpdb->get_results($wpdb->prepare("SELECT p.post_title, p.id
FROM " . $wpdb->prefix . "posts p
JOIN " . $wpdb->prefix . "postmeta pw ON p.id = pw.post_id
WHERE p.post_type = 'product'
AND p.post_status = 'publish'
AND pw.meta_key = 'RestaurantPassword'
AND p.id = %d",$product_id));


    if (count($myrows1) > 0) {
        if (isset($_POST['makePayment'])) {
			
		$amount = $_POST['amount'];
		$product_id = $_POST['product_id'];
             $wpdb->insert(
                '' . $wpdb->prefix . 'offer_payments',
                array(
                    "pid" => $product_id,
                    "amount" => $amount 
                )
            );

        }

        $myrows = $wpdb->get_results($wpdb->prepare("SELECT p.post_title,
im.meta_value AS quan,
im1.meta_value AS productID,
bf.meta_value AS b_fname,
bl.meta_value AS b_lname,
be.meta_value AS b_email,
i.order_id, i.order_item_id as itemid, i.downloaded as downloaded
FROM " . $wpdb->prefix . "woocommerce_order_itemmeta im
JOIN " . $wpdb->prefix . "woocommerce_order_itemmeta im1 ON im.order_item_id = im1.order_item_id
LEFT JOIN " . $wpdb->prefix . "woocommerce_order_items i ON im.order_item_id = i.order_item_id
LEFT JOIN " . $wpdb->prefix . "posts o ON i.order_id = o.id
JOIN " . $wpdb->prefix . "posts p ON im1.meta_value = p.id
LEFT JOIN " . $wpdb->prefix . "postmeta bf ON o.id = bf.post_id
LEFT JOIN " . $wpdb->prefix . "postmeta bl ON o.id = bl.post_id
LEFT JOIN " . $wpdb->prefix . "postmeta be ON o.id = be.post_id
WHERE im.meta_key = '_qty'
AND im1.meta_key = '_product_id'
AND bf.meta_key = '_billing_first_name'
AND bf.post_id = o.id
AND bl.meta_key = '_billing_last_name'
AND bl.post_id = o.id
AND be.meta_key = '_billing_email'
AND be.post_id = o.id
AND o.post_status = 'wc-completed'
AND o.post_type = 'shop_order'
AND im1.meta_value = %d group by o.id", $product_id));

$paymentList = $wpdb->get_results($wpdb->prepare("SELECT  * from " . $wpdb->prefix . "offer_payments where pid = %d", $product_id));
$totalPay = $wpdb->get_results($wpdb->prepare("SELECT sum(amount) as totalPay FROM " . $wpdb->prefix . "offer_payments where pid = %d", $product_id));
$totalPay = $totalPay['0']->totalPay;


        $gr = $wpdb->get_results($wpdb->prepare("SELECT pm.meta_value
FROM " . $wpdb->prefix . "postmeta pm
WHERE pm.post_id = %d
AND pm.meta_key = '_price'", $product_id));


        $vat_val = $wpdb->get_results($wpdb->prepare("SELECT pm.meta_value
FROM " . $wpdb->prefix . "postmeta pm
WHERE pm.post_id = %d
AND pm.meta_key = 'Vat'", $product_id));

        $commission_val = $wpdb->get_results($wpdb->prepare("SELECT pm.meta_value
FROM " . $wpdb->prefix . "postmeta pm
WHERE pm.post_id = %d
AND pm.meta_key = 'Commission'", $product_id));



        ?>


      
        <div class="row">
            <div class="col-md-12">


                <p class="pimage"><?= get_the_post_thumbnail($pid, 'shop_catalog', array('class' => 'img-thumbnail')); ?><?= $myrows[0]->post_title ?></p>
            </div>
        </div>


        <div class="panel panel-default">
           
            <div class="panel-body">

                <table class="table table-striped table-bordered">

                    <thead>

                    <th>Total Vouchers Sold</th>
                    <th>Gross Revenue</th>
                    <th>Commission</th>
                    <th>vat</th>
                    <th>Net Payable</th>
                    <th>Net Balance Due</th>

                    </thead>

                    <tbody>

                    <?php foreach ($myrows as $val) {
                        $tproduct = $tproduct + 1;

                        $total = $total + $val->quan;
                        $grevenue = $total * $gr[0]->meta_value;
                        $commission = ($grevenue / 100) * $commission_val[0]->meta_value;
                        $vat = ($commission / 100) * $vat_val[0]->meta_value;
                        $grevenue = round($grevenue, 2);
                        $commission = round($commission, 2);
                        $vat = round($vat, 2);
                        $payable = $grevenue - ($commission + $vat);
						$blance = $payable - $totalPay; 
						$blance = round($blance, 2); 
                        $payable = round($payable, 2);

	}
                    ?>



                    <tr>

                        <td><?= $total ?></td>
                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($grevenue, 2) ?></td>
                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($commission, 2) ?></td>
                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($vat, 2) ?></td>
                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($payable, 2) ?></td>
                        <td>
							
							<?= get_woocommerce_currency_symbol() ?> <?= number_format($blance, 2) ?></td>

                    </tr>
                   
					
                  

                    </tbody>

                </table>
			</div>		
			</div>	
 <div class="panel panel-default">			
			<div class="panel-body">
			<div class="panel-heading"><h2 style="text-align: center">List of Payments </h2></div>			
			<table class="table table-striped table-bordered">
				<thead>
				<tr>
                    <th>Payment ID</th>
                    <th>Payment Date</th>
                    <th>Payment Amount</th>
				</tr>
                </thead>
				<tbody>
					<?php
					
					foreach($paymentList as $val){ ?>
					<tr>
					<td><?= $val->id ?></td>
					<td><?= $val->timestamp ?></td>
					<td><?= get_woocommerce_currency_symbol() ?> <?= number_format($val->amount, 2) ?></td>
					<?php } ?>
					</tr>
				</tbody>
				</table>

            </div>
        </div>
		  <div class="row">
		 <div class="panel-body">
		 <form method="post" action="">
			<div class="form-group">
			<label for="exampleInputEmail1">Make a Payment </label>
			<input type="number" class="form-control" name="amount" step=".01"  placeholder="Enter Amount" required>
			<input type="hidden" class="form-control" name="product_id" value="<?= $product_id ?>">
			</div>
			<input  type="submit" name="makePayment" value="Submit Payment" class="btn btn-primary">
		</form>
		 </div>
		
		</div>
    <?php


    } else {

        echo '<div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">TheTaste Error -</span> Values entered do not match in our system
</div>';
    }
}

else
{
global $wpdb;

if(isset($_POST['paid']))
{

    $oin = $_POST['oin'];
    $prid = $_POST['prid'];
    $rows_affected = $wpdb->query(
        $wpdb->prepare(
            "UPDATE " . $wpdb->prefix . "woocommerce_order_items
                SET paid = '1' where order_item_name = %s ",$oin
        )
    );

    $wpdb->prepare(
        "UPDATE " . $wpdb->prefix . "woocommerce_order_items
                SET paid = '1' order_item_name = %s ",$oin
    );


}


$m_t_vocher = 0;
$m_t_revenue = 0;

$y_t_vocher = 0;
$y_t_revenue = 0;


//$date = 01;
$date = date("m");
$year = date("Y");
//  $year = 2015;

$total_vsold = 0;
$total_grevenue = 0;
$total_commission = 0;
$total_vat = 0;
$total_payable = 0;

$currentMonth = $wpdb->get_results($wpdb->prepare("SELECT im.meta_value AS quan,
                                                  im1.meta_value AS productID,
                                                  pr.meta_value AS price
                                                  FROM " . $wpdb->prefix . "woocommerce_order_itemmeta im
													JOIN " . $wpdb->prefix . "woocommerce_order_itemmeta im1 ON im.order_item_id = im1.order_item_id
													LEFT JOIN " . $wpdb->prefix . "woocommerce_order_items i ON im.order_item_id = i.order_item_id
													LEFT JOIN " . $wpdb->prefix . "posts o ON i.order_id = o.id
													JOIN " . $wpdb->prefix . "posts p ON p.id = im1.meta_value
													LEFT JOIN " . $wpdb->prefix . "postmeta pr ON p.id = pr.post_id
													WHERE im.meta_key = '_qty'
													AND im1.meta_key = '_product_id'
													AND o.post_status = 'wc-completed'
													AND pr.meta_key = '_price'
													AND o.post_type = 'shop_order'
													AND MONTH(o.post_date) = '%d'
													AND YEAR(o.post_date) = '%d'
													", $date, $year));

$currentMonthrev = $wpdb->get_results($wpdb->prepare("SELECT sum(ot.meta_value) AS monthTotalRevenue
                                                    FROM " . $wpdb->prefix . "posts o
                                                    LEFT JOIN " . $wpdb->prefix . "postmeta ot ON o.id = ot.post_id
													WHERE ot.meta_key = '_order_total'
													AND o.post_status = 'wc-completed'
													AND o.post_type = 'shop_order'
													AND MONTH(o.post_date) = '%d'
													AND YEAR(o.post_date) = '%d'
													", $date, $year));

$currentYearrev = $wpdb->get_results($wpdb->prepare("SELECT sum(ot.meta_value) AS monthTotalRevenue
                                                    FROM " . $wpdb->prefix . "posts o
                                                    LEFT JOIN " . $wpdb->prefix . "postmeta ot ON o.id = ot.post_id
													WHERE ot.meta_key = '_order_total'
													AND o.post_status = 'wc-completed'
													AND o.post_type = 'shop_order'
													AND YEAR(o.post_date) = '%d'
													", $year));


/*$outStandingPayment = $wpdb->get_results($wpdb->prepare("SELECT sum(ot.meta_value) AS monthTotalRevenue
                                                    FROM " . $wpdb->prefix . "posts o
                                                    LEFT JOIN " . $wpdb->prefix . "postmeta ot ON o.id = ot.post_id
													WHERE ot.meta_key = '_order_total'
													AND o.post_status = 'wc-completed'
													AND o.post_type = 'shop_order'
													AND YEAR(o.post_date) = '%d'
													", $year));*/

$currentYear = $wpdb->get_results($wpdb->prepare("SELECT p.post_title,
                                                  SUM(im.meta_value) AS quan,
                                                  im1.meta_value AS productID,
                                                  pr.meta_value AS price
                                                  FROM " . $wpdb->prefix . "woocommerce_order_itemmeta im
													JOIN " . $wpdb->prefix . "woocommerce_order_itemmeta im1 ON im.order_item_id = im1.order_item_id
													LEFT JOIN " . $wpdb->prefix . "woocommerce_order_items i ON im.order_item_id = i.order_item_id
													LEFT JOIN " . $wpdb->prefix . "posts o ON o.id = i.order_id
													JOIN " . $wpdb->prefix . "posts p ON im1.meta_value = p.id
													LEFT JOIN " . $wpdb->prefix . "postmeta pr ON p.id = pr.post_id
													WHERE im.meta_key = '_qty'
													AND im1.meta_key = '_product_id'
													AND o.post_status = 'wc-completed'
													AND pr.meta_key = '_price'
													AND o.post_type = 'shop_order'
													AND YEAR(o.post_date) = '%d'
													group by p.id", $year));
if (count($currentMonth) > 0) {


    foreach ($currentMonth as $val) {
        $m_t_vocher = $m_t_vocher + $val->quan;
        $m_t_revenue = $m_t_revenue + $val->price * $val->quan;
    }


}

if (count($currentYear) > 0) {


    foreach ($currentYear as $val) {
        $y_t_vocher = $y_t_vocher + $val->quan;
        $y_t_revenue = $y_t_revenue + $val->price;
    }

}

$pricea = "_price";

$products = $wpdb->get_results($wpdb->prepare("SELECT DATE(p.post_date) as post_date, p.post_title,
                                                  SUM(im.meta_value) AS quan,
                                                  im1.meta_value AS productID,
                                                  pr.meta_value AS price,
                                                  vat.meta_value AS vat,
                                                  cm.meta_value AS cm,
                                                  i.order_id,i.order_item_id as itemid, i.downloaded as downloaded,i.paid as paid
													FROM " . $wpdb->prefix . "woocommerce_order_itemmeta im
													JOIN " . $wpdb->prefix . "woocommerce_order_itemmeta im1 ON im.order_item_id = im1.order_item_id
													LEFT JOIN " . $wpdb->prefix . "woocommerce_order_items i ON im.order_item_id = i.order_item_id
													LEFT JOIN " . $wpdb->prefix . "posts o ON o.id = i.order_id
													JOIN " . $wpdb->prefix . "posts p ON im1.meta_value = p.id
													LEFT JOIN " . $wpdb->prefix . "postmeta pr ON p.id = pr.post_id
													LEFT JOIN " . $wpdb->prefix . "postmeta vat ON p.id = vat.post_id
													LEFT JOIN " . $wpdb->prefix . "postmeta cm ON p.id = cm.post_id
													WHERE im.meta_key = '_qty'
													AND im1.meta_key = '_product_id'
													AND o.post_status = 'wc-completed'
													AND pr.meta_key = '%s'
													AND vat.meta_key = 'Vat'
													AND cm.meta_key = 'Commission'
													AND o.post_type = 'shop_order'

													group by p.id order by productID desc ", $pricea));


$outStandingPayment = $wpdb->get_results($wpdb->prepare("SELECT DATE(p.post_date) as post_date, p.post_title,
                                                  SUM(im.meta_value) AS quan,
                                                  im1.meta_value AS productID,
                                                  pr.meta_value AS price,
                                                  vat.meta_value AS vat,
                                                  cm.meta_value AS cm,
                                                  i.order_id,i.order_item_id as itemid, i.downloaded as downloaded,i.paid as paid
													FROM " . $wpdb->prefix . "woocommerce_order_itemmeta im
													JOIN " . $wpdb->prefix . "woocommerce_order_itemmeta im1 ON im.order_item_id = im1.order_item_id
													LEFT JOIN " . $wpdb->prefix . "woocommerce_order_items i ON im.order_item_id = i.order_item_id
													LEFT JOIN " . $wpdb->prefix . "posts o ON o.id = i.order_id
													JOIN " . $wpdb->prefix . "posts p ON im1.meta_value = p.id
													LEFT JOIN " . $wpdb->prefix . "postmeta pr ON p.id = pr.post_id
													LEFT JOIN " . $wpdb->prefix . "postmeta vat ON p.id = vat.post_id
													LEFT JOIN " . $wpdb->prefix . "postmeta cm ON p.id = cm.post_id
													WHERE im.meta_key = '_qty'
													AND im1.meta_key = '_product_id'
													AND o.post_status = 'wc-completed'
													AND pr.meta_key = '%s'
													AND vat.meta_key = 'Vat'
													AND cm.meta_key = 'Commission'
													AND o.post_type = 'shop_order'
													AND i.paid = '0'
													group by p.id order by productID desc ", $pricea));


$total_vsold2 = 0;
$total_grevenue2 = 0;
$total_commission2 = 0;
$total_vat2 = 0;
$total_payable2 = 0;

foreach ($outStandingPayment as $val) {
    $productID2 = $val->productID;
    $post_date2 = $val->post_date;
    $productName2 = $val->post_title;
    $price2 = $val->price;
    $vsold2 = $val->quan;
    $price2 = $val->price;
    $grevenue2 = $price2 * $vsold2;
    $grevenue2 = round($grevenue2, 2);
    $commission2 = ($grevenue2 / 100) * $val->cm;
    $commission2 = round($commission2, 2);
    $vat2 = ($commission2 / 100) * $val->vat;
    $vat2 = round($vat2, 2);
    $payable2 = $grevenue2 - ($commission2 + $vat2);
    $payable2 = round($payable2, 2);



    $total_vsold2 = $total_vsold2 + $vsold2;
    $total_grevenue2 = $total_grevenue2 + $grevenue2;
    $total_commission2 = $total_commission2 + $commission2;
    $total_vat2 = $total_vat2 + $vat2;
    $total_payable2 = $total_payable2 + $payable2;

    $total_vsold2 = round($total_vsold2, 2);
    $total_grevenue2 = round($total_grevenue2, 2);
    $total_commission2 = round($total_commission2, 2);
    $total_vat2 = round($total_vat2, 2);
    $total_payable2 = round($total_payable2, 2);
}

?>



<div class="panel panel-default">

    <div class="panel-body">

        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th colspan="4">Summary for the month of <?= $date . '/' . $year ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>Current Month</th>
                <th>Vouchers Sold</th>
                <th>Year to Date</th>
                <th>Vouchers Sold</th>
                <th>Oustanding Payments</th>
            </tr>
            <tr>
                <td> <?= get_woocommerce_currency_symbol() ?> <?= number_format(round($currentMonthrev[0]->monthTotalRevenue, 2), 2) ?></td>
                <td><?= $m_t_vocher ?></td>
                <td> <?= get_woocommerce_currency_symbol() ?> <?= number_format(round($currentYearrev[0]->monthTotalRevenue, 2), 2) ?></td>
                <td><?= $y_t_vocher ?></td>
                <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($total_payable2, 2) ?></td>

            </tr>
            </tbody>
        </table>


        <?php
        if (count($products) > 0) {
            ?>
            <table class="table table-striped table-bordered dataTable" id="myTable">

                <thead>
                <tr>
                    <th colspan="10">Details</th>
                </tr>
                <tr>
                    <th>Product ID</th>
                    <th>Created Date</th>
                    <th>Product Title</th>
                    <th>Price</th>
                    <th>Vouchers Sold</th>
                    <th>Gross Revenue</th>
                    <th>Commission</th>
                    <th>Vat</th>
                    <th>Net Payable</th>
                    <th>Paid</th>
                </tr>
                </thead>
                <tbody>
                <?php



                foreach ($products as $val) {
                    //    $tproduct = $tproduct + 1;

                    $productID = $val->productID;
                    $post_date = $val->post_date;
                    $productName = $val->post_title;
                    $price = $val->price;
                    $vsold = $val->quan;
                    $price = $val->price;
                    $grevenue = $price * $vsold;
                    $grevenue = round($grevenue, 2);
                    $commission = ($grevenue / 100) * $val->cm;
                    $commission = round($commission, 2);
                    $vat = ($commission / 100) * $val->vat;
                    $vat = round($vat, 2);
                    $payable = $grevenue - ($commission + $vat);
                    $payable = round($payable, 2);

                    $total_vsold = $total_vsold + $vsold;
                    $total_grevenue = $total_grevenue + $grevenue;
                    $total_commission = $total_commission + $commission;
                    $total_vat = $total_vat + $vat;
                    $total_payable = $total_payable + $payable;

                    $total_vsold = round($total_vsold, 2);
                    $total_grevenue = round($total_grevenue, 2);
                    $total_commission = round($total_commission, 2);
                    $total_vat = round($total_vat, 2);
                    $total_payable = round($total_payable, 2);

                    ?>
                    <tr>
                        <td><?= $productID ?></td>
                        <td><?= $post_date ?></td>
                        <td><a href="?product_id=<?= $productID ?>" style="color: inherit"><?= $productName ?></a></td>
                        <td><?= $price ?></td>
                        <td><?= $vsold ?></td>
                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($grevenue, 2) ?></td>
                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($commission, 2) ?></td>
                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($vat, 2) ?></td>
                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($payable, 2) ?></td>
                        <td>
						
						<form method="post" action="?makePaymentPage=<?= $productID ?> "  target="_blank">
								 <input type="hidden" name="prid" value="<?= $val->order_id ?>">
                                    <input type="hidden" name="oii" value="<?= $val->itemid ?>">
                                    <input type="hidden" name="oin" value="<?= $productName ?>">
                                    <input type="hidden" name="product_id" value="<?= $productID ?>">
                                    <input type="hidden" name="product_pass" value="<?= $pass ?>">
								<input type="submit" value="Make Payment" name="makePaymentPage">
						</form>
								
                            <?php
						if(number_format(productPayment($productID), 2) != 0)
							
							{
							if( $payable > productPayment($productID))
							{
								echo '<span class="label label-warning">Part Paid</span>';
								
                            }
                            else
                            {

                                echo '<span class="label label-success">Paid</span>';
                            }
							}
                            ?>

                        </td>

                    </tr>

                <?php

                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Vouchers Sold</th>
                    <th>Total Gross Revenue</th>
                    <th>Total Commission</th>
                    <th>Total Vat</th>
                    <th>Total Net Payable</th>
                </tr>

                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <td><?= $total_vsold ?></td>
                    <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($total_grevenue, 2) ?></td>
                    <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($total_commission, 2) ?></td>
                    <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($total_vat, 2) ?></td>
                    <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($total_payable, 2) ?></td>
                  </tr>
                <?php
                ?>
                </tfoot>

            </table>
        <?php

        } else {
            ?>
            <div class="alert alert-danger" role="alert">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <span class="sr-only"></span>TheTaste Error - No result found
            </div>

        <?php
        }
        ?>
    </div>
    <?php

	
    }
    }

    else {
        ?>
        <div class="alert alert-danger" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only"></span>TheTaste Error - You are not authorized to view this page
        </div>

    <?php } ?>
			</div>
			</div>
</div>
<script>
    $(document).ready(function(){
        $('#myTable').DataTable( {
            "order": [[ 0, "desc" ]]
        } );
    });
</script>

</body>

</html>