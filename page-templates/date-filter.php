<?php
/*
Template Name: Date Filter
*/


?>

<!DOCTYPE HTML>

<html>

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
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
    
    <center><img src="http://thetaste.ie/wp/wp-content/uploads/2017/12/thetaste-site-homepage-logo5.png"></center>
<br><br>

<?php

global $wpdb;

$m_t_vocher = 0;
$m_t_revenue = 0;

$y_t_vocher = 0;
$y_t_revenue = 0;


//$date = 01;
if(isset($_POST['datepost']))
{
	$date = $_POST['datepost'];
}
else
{
$date = date("m");
}

if(isset($_POST['yearpost']))
{
	$year = $_POST['yearpost'];
}
else
{
$year = date("Y");
}


//  $year = 2015;

$total_vsold = 0;
$total_vredeemed = 0;
$total_grevenue = 0;
$total_commission = 0;
$total_vat = 0;
$total_payable = 0;

$pricea = "_price";

$products = $wpdb->get_results($wpdb->prepare("SELECT DATE(p.post_date) as post_date, p.post_title,
                                                  SUM(im.meta_value) AS quan,
                                                  SUM(i.downloaded) AS redeemquan,
                                                  im1.meta_value AS productID,
                                                  pr.meta_value AS price,
                                                  vat.meta_value AS vat,
                                                  cm.meta_value AS cm,
                                                  cu.meta_value AS cu,
                                                  i.order_id,i.order_item_id as itemid, i.downloaded as downloaded,i.paid as paid
													FROM " . $wpdb->prefix . "woocommerce_order_itemmeta im
													JOIN " . $wpdb->prefix . "woocommerce_order_itemmeta im1 ON im.order_item_id = im1.order_item_id
													LEFT JOIN " . $wpdb->prefix . "woocommerce_order_items i ON im.order_item_id = i.order_item_id
													LEFT JOIN " . $wpdb->prefix . "posts o ON o.id = i.order_id
													JOIN " . $wpdb->prefix . "posts p ON im1.meta_value = p.id
													LEFT JOIN " . $wpdb->prefix . "postmeta pr ON p.id = pr.post_id
													LEFT JOIN " . $wpdb->prefix . "postmeta vat ON p.id = vat.post_id
													LEFT JOIN " . $wpdb->prefix . "postmeta cm ON p.id = cm.post_id
													LEFT JOIN wp_postmeta cu ON p.id = cu.post_id AND cu.meta_key = 'coupon'
													WHERE im.meta_key = '_qty'
													AND im1.meta_key = '_product_id'
													AND o.post_status = 'wc-completed'
													AND pr.meta_key = '%s'
													AND vat.meta_key = 'Vat'
													AND cm.meta_key = 'Commission'
													AND o.post_type = 'shop_order'
													AND MONTH(o.post_date) = '%d'
													AND YEAR(o.post_date) = '%d'
													group by p.id order by productID desc ", $pricea , $date, $year));
											
								


?>



<div class="panel panel-default">
 <div class="panel-heading">New Management Console</div>
  <div class="panel-body">
 
<form class="form-inline" method="post" action="">
  <div class="form-group">
		<label>Select Month</label>			
		<select class="form-control" name="datepost" required>
		<option value="">Select Month</option>
			<?php for($i=1; $i<=12; $i++) { ?>
			<option value="<?= date('m', mktime(0, 0, 0, $i, 1)) ?>"><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
			<?php } ?>
		</select>	
</div>
  <div class="form-group">
         <label>Select Year</label>			
		<select class="form-control" name="yearpost" required>
		<option value="">Select Year</option>
			<?php $thisYear = date("Y");
			for($i=0; $i<=20; $i++) {  ?>
			<option value="<?= $thisYear-$i ?>"><?= $thisYear-$i ?></option>
			<?php } ?>
		</select>
  </div>
  <button type="submit" class="btn btn-default">Submit</button>
</form>
  </div>
</div>
<div class="panel panel-default">

    <div class="panel-body">

        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th colspan="4">Summary for the month of <?= $date . '/' . $year ?></th>
            </tr>
            </thead>
            <tbody>
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
                    <th>ID</th>
                    <th>Offer</th>
                    <th width="60">Price</th>
                    <th width="60">Sold</th>
                    <th width="60">Served</th>
                    <th width="80">Gross Revenue</th>
                    <th width="70">Com</th>
                    <th width="70">Vat</th>
                    <th width="70">Payable<br>to Client</th>
                    <th width="70">Holding<br>Unredeemed</th>
                    <th width="80">Total<br>Taste</th>
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
                    $redeem = $val->redeemquan;
                    
                    $price = $val->price;
                    $grevenue = $price * $vsold;
                    $grevenue = round($grevenue, 2);
                    $nrevenue = $price * $redeem;
                    $nrevenue = round($nrevenue, 2);
                    
                    $commission = ($nrevenue / 100) * $val->cm;
                    $commission = round($commission, 2);
                    
                    $vat = ($commission / 100) * $val->vat;
                    $vat = round($vat, 2);
                    
                    $payable = $nrevenue - ($commission + $vat);
                    $payable = round($payable, 2);

                    $holding = $grevenue - ($payable + $commission + $vat);
                    $holding = round($holding, 2);
                    
                    $totaltaste = $holding + $commission;
                    $totaltaste = round($totaltaste, 2);
                    
                    $monthlyvat = $monthlyvat + $vat;
                    $monthlypayable = $monthlypayable + $payable;
                    $monthlyunredeemed = $monthlyunredeemed + $holding;
                    
                    $monthlyvsold = $monthlyvsold + $vsold;
                    $monthlyvredeem = $monthlyvredeem + $redeem;
                    
                    ?>
                    <tr>
                        <td><?= $productID ?></td>
                        <td><a href="http://thetaste.ie/wp/restaurantmanager?product_id=<?= $productID ?>&product_pass="><?= $productName ?></a></td>
                        <td><?= $price ?></td>
                        <td><?= $vsold ?></td>
                        <td><?= $redeem ?></td>
                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($grevenue, 2) ?></td>
                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($commission, 2) ?><br><?= $val->cm ?>%</td>
                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($vat, 2) ?><br><?= $val->vat ?>%</td>
                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($payable, 2) ?></td>
                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($holding, 2) ?></td>
                        <td><?= get_woocommerce_currency_symbol() ?> <?= number_format($totaltaste, 2) ?></td>

                    </tr>

                <?php

                }
                ?>
                </tbody>
     
            </table>
            <br><br>
            <b>Monthly Totals</b>
            <br><br>
            Vouchers Sold <?= $monthlyvsold ?> / Used <?= $monthlyvredeem ?>
            <br><br>
            VAT Payable € <?= number_format($monthlyvat,2) ?>
            <br><br>
            Payable to Clients € <?= number_format($monthlypayable,2) ?>
            <br><br>
            Unredeemed € <?= number_format($monthlyunredeemed,2) ?>
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