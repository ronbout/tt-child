<?php
/*
Template Name: Gift Voucher
*/
get_header(); ?>

        <section class="content">

        <?php get_template_part('inc/page-title'); ?>

        <?php

           if(isset($_POST['getorder']))

           {

           ?>


        <?php

        $ordera =  get_posts(array(
            'include' => $_POST['oid'],
            'numberposts' => 1,
            'meta_key'    => '_billing_email',
            'meta_value'  => $_POST['oemail'],
            'post_type'   => 'shop_order',
            'post_status' => 'wc-completed',
        ));

    if ( $ordera ) {
            ?>
        <div class="container" id="user_loggin">
        <?php
        $id = $_POST['oid'];
        $order = wc_get_order($id);
        /*echo '<pre>';
        print_r($order);
        echo '</pre>';*/
        if (!defined('ABSPATH')) exit; // Exit if accessed directly
        ?>



        <?php /*if ( $order->has_status( 'pending' ) ) : */ ?><!--


        <p><?php /*printf( __( 'An order has been created for you on %s. To pay for this order please use the following link: %s', 'woocommerce' ), get_bloginfo( 'name' ), '<a href="' . esc_url( $order->get_checkout_payment_url() ) . '">' . __( 'pay', 'woocommerce' ) . '</a>' ); */ ?></p>

        <?php /*endif; */ ?>
        </br>
        <?php /*print_r($order->billing_email); */ ?>
        <?php /*do_action( 'woocommerce_email_before_order_table', $order, 'aminnazir.net@gmail.com', '' ); */ ?>

        <h2><?php /*echo __( 'Order:', 'woocommerce' ) . ' ' . $order->get_order_number(); */ ?> (<?php /*printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order->order_date ) ), date_i18n( wc_date_format(), strtotime( $order->order_date ) ) ); */ ?>)</h2>
        -->
        <?php
        ob_start();
        ?>
        <div id="contentID">
            <br>
        <div dir="ltr" style="background-color:#f5f5f5;margin:0;padding:70px 0 70px 0;width:96%">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
        <td valign="top" align="center">
        <div><p style="margin-top:0"><img tabindex="0" class="CToWUd a6T"
        src="http://thetaste.ie/wp/wp-content/uploads/2017/12/thetaste-site-homepage-logo5.png"
        alt="TheTaste.ie"
        style="border:none;display:inline;font-size:14px;font-weight:bold;min-height:auto;line-height:100%;outline:none;text-decoration:none;text-transform:capitalize">
        <br><br>
        <div style="opacity: 0.01; left: 825.6px; top: 558.6px;" dir="ltr" class="a6S">
        <div data-tooltip="Download" data-tooltip-class="a1V" id=":q6"
        class="T-I J-J5-Ji aQv T-I-ax7 L3 a5q" role="button" tabindex="0"
        aria-label="Download attachment ">
        <div class="aSK J-J5-Ji aYr"></div>
        </div>
        </div>
                             </div>
        <table style="background-color:#fdfdfd;border:1px solid #dcdcdc;border-radius:3px" width="600"
        border="0" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
        <td valign="top" align="center">

        <table
        style="background-color:#000000;border-radius:3px 3px 0 0;color:#ffffff;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:'helvetica neue','helvetica','roboto','arial',sans-serif"
        width="600" border="0" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
        <td>
        <h1 style="color:#ffffff;display:block;font-family:'helvetica neue','helvetica','roboto','arial',sans-serif;font-size:26px;font-weight:300;line-height:150%;margin:0;padding:36px 48px;text-align:left">
        <b>GIFT VOUCHER</b> | TASTE EXPERIENCE</h1>
        </td>
        </tr>
        </tbody>
        </table>

        </td>
        </tr>
        <tr>
        <td valign="top" align="center">

        <table class="table1" width="600" border="0" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
        <td style="background-color:#fdfdfd " valign="top">

        <table width="100%" border="0" cellpadding="20" cellspacing="0">
        <tbody>
        <tr>
        <td valign="top" style="padding: 48px;">
        <div
        style="color:#737373;font-family:'helvetica neue','helvetica','roboto','arial',sans-serif;font-size:14px;line-height:150%;text-align:left">
       
		<p><b style="color:black;">From :</b> <?= $_POST['from'] ?></p>
        <br>
        <p><b style="color:black;">To :</b> <?= $_POST['to'] ?></p>
        <br>
		<p><b style="color:black;">Message :</b> <?= $_POST['message'] ?></p>
        <br>

        <h2 style="display:block;font-family:'helvetica neue','helvetica','roboto','arial',sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:16px 0px 8px;text-align:left"><?php echo __('Gift Voucher Number : ', 'woocommerce') . ' ' . $order->get_order_number(); ?></h2>
        <br>
        <table style="width:100%;border:1px solid #eee" border="1" cellpadding="6" cellspacing="0" class="products">
        <thead>
        <tr>
        <th scope="col" style="text-align:left;border:1px solid #eee;padding:12px">Taste Experience</th>
        <th scope="col" style="text-align:left;border:1px solid #eee;padding:12px"><?php _e('Quantity', 'woocommerce'); ?></th>
        <th scope="col" style="text-align:left;border:1px solid #eee;padding:12px"><?php _e('Price', 'woocommerce'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        switch ($order->get_status()) {
            case "completed" :
                echo $order->email_order_items_table($order->is_download_permitted(), false, true);
                break;
            case "processing" :
                echo $order->email_order_items_table($order->is_download_permitted(), true, true);
                break;
            default :
                echo $order->email_order_items_table($order->is_download_permitted(), true, false);
                break;
        }
        ?>
                </tbody>
        <tfoot>
        <?php
        if ($totals = $order->get_order_item_totals()) {
            $i = 0;
            foreach ($totals as $total) {
                $i++;
                ?>
        <tr>
        <th scope="row" colspan="2"
        style="text-align:left; padding: 12px;  border: 1px solid #eee; <?php if ($i == 1) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
        <td style="text-align:left; padding: 12px; border: 1px solid #eee; <?php if ($i == 1) echo 'border-top-width: 4px;'; ?>"><?php echo ($i == 2 ? '--' : $total['value']) ; ?></td>
        </tr><?php
                                                           }
                                                       }
                                                       ?>
        </tfoot>
        </table>
        

        </td>
        </tr>
        </tbody>
        </table>
        </div>
        </td>
        </tr>
        </tbody>
        </table>

        </td>
        </tr>
        </tbody>
        </table>

        </td>
        </tr>
        <tr>
        <td valign="top" align="center">

        <table width="600" border="0" cellpadding="10" cellspacing="0">
        <tbody>
        <tr>
        <td style="padding:0" valign="top">
        <table width="100%" border="0" cellpadding="10" cellspacing="0">
        <tbody>
        <tr>
        <td colspan="2"
        style="padding:0 48px 48px 48px;border:0;color:#666666;font-family:'arial';font-size:12px;line-height:125%;text-align:center"
        valign="middle">
        </td>
        </tr>
        </tbody>
        </table>
        </td>
        </tr>
        </tbody>
        </table>

        </td>
        </tr>
        </tbody>
        </table>
        </td>
        </tr>
        </tbody>
        </table>
        </div>
        </div>

        <?php
        $content = ob_get_clean();
        echo $content;
        if(isset($_POST['sendgift'])) {
            $message = '<html><body><head><style>.products{width: 100%;border: 1px solid rgb(238, 238, 238);}.products tr th{text-align: left;border: 1px solid rgb(238, 238, 238);padding: 12px;}.products tr td{text-align: left;vertical-align: middle;border: 1px solid rgb(238, 238, 238);word-wrap: break-word;padding: 12px;}</style></head>';
            $message .= str_replace('\\', '', $_POST['content']);
            $message .= '</body></html>';

            $to = $_POST['toemail'];

            $subject = 'TheTaste.ie Gift Voucher';

            $headers = "From: " . strip_tags("info@thetaste.ie") . "\r\n";
            $headers .= "Reply-To: ". strip_tags("info@thetaste.ie") . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            wp_mail($to, $subject, $message, $headers);
            ?>
        <div class="container" id="giftemail">

        </br>
        <div>
        <div class="alert alert-success" role="alert" style="text-align: center; color: green; font-size: 22px;">
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        Gift Voucher Sent to <?= $_POST['toemail'] ?>
        </div>
        </div>
        </div>

        <?php
    }
   ?>
        </br>

        <form class="contact-form themeform" method="post" style="text-align: center">
        <input type="hidden" value="<?= $_POST['message']?>" name="message">
        <input type="hidden" value="<?= $_POST['oemail']?>" name="oemail">
        <input type="hidden" value="<?= $_POST['oid']?>" name="oid">
        <input type="hidden" value="<?= $_POST['from']?>" name="from">
        <input type="hidden" value="<?= $_POST['to']?>" name="to">
        <input type="hidden" value="<?= $_POST['toemail']?>" name="toemail">
        <textarea type="hidden" style="display:none;" id="emailContent" name="content"></textarea>
        <input type="hidden" value="1" name="sendgift">
        <p>Send voucher to <?= $_POST['toemail']?>.</p>
        <br>
        <button type="submit" name="getorder" class="btn btn-default" style="width:50%; height:50px; font-size:16px;">Send</button>
        </form>
        <br><br>
        </div>

        <?php
    }
    else
    {
        ?>
        <div class="container" id="error">
        </br>
        <div class="alert alert-danger" role="alert" style="text-align: center; color: red; font-size: 22px;">
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <b>TheTaste Error :</b>Your Order Details Do Not Match Our System.
        </div>
        </div>
        <?php
    }
}

 else
 {
     ?>
        </br>
        <div class="container">
        <div class="panel panel-default">
            
        
  
       <div class="panel-body" style="margin: 6px 10px 26px; display: table; width: 40%; float:left;">
             <p style="font-size: 16pt;"><strong>Send Gift Voucher</strong><br><br>If you want to change your Taste<br> Experience in to a Gift Voucher,<br>without the prices and include a<br>message, please fill in the form<br>below...<br><br></p>

        <form class="contact-form themeform" method="post" action="">
        <div class="">
            <label>Order ID<span>(required)</span></label>
        <input type="text" name="oid" class="form-control" id="product_id" placeholder="" required="">
        </div>
        <div>
            <label>Your Email<span>(required)</span></label>
        <input type="text" name="oemail" class="form-control" id="product_id" placeholder="" required="">
        </div>
        <div>
            <label>Your Name<span>(required)</span></label>
        <input type="text" name="from" class="form-control" id="product_id" placeholder="" required="">
        </div>
        <div>
            <label>Recipent Name :<span>(required)</span></label>
        <input type="text" name="to" class="form-control" id="product_pass" placeholder="" required="">
        </div>
        <div>
            <label>Recipent Email :<span>(required)</span></label>
        <input type="email" name="toemail" class="form-control" id="product_pass" placeholder="" required="">
        </div>
        <div>
            <label>Message :<span></span></label>
        <input type="text" name="message" class="form-control" id="message" placeholder="" style="height:200px;">
        </div>
        <br>
        <button type="submit" name="getorder" class="btn btn-default" style="width:64%; height:50px; font-size:16px;">Send Gift Voucher</button>
        </form>
        <br><br><br>
        </div>
        <div style="width:54%;float:left;"><img src="http://thetaste.ie/wp/wp-content/uploads/2019/06/iStock-1009336738.jpg" style="width:100%;"></div>
        </div>
        </div>
        <?php

        }
        ?>
        </section>


        <?php get_footer(); ?>

                <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<style>
    .products
    {
        width: 100%;
        border: 1px solid rgb(238, 238, 238);
    }
    .products tr th
    {
        text-align: left;
        border: 1px solid rgb(238, 238, 238);
        padding: 12px;
    }
    .products tr td
    {
        text-align: left;
        vertical-align: middle;
        border: 1px solid rgb(238, 238, 238);
        word-wrap: break-word;
        padding: 12px;
    }
</style>
<script>
    $( document ).ready(function(){
        $(".amount").html("Gift");
 $(".products td").css("padding","12px");
      getEmailContent = $("#contentID").html();

        $("#emailContent").val(getEmailContent);
    });
</script>
