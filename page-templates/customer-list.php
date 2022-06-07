<?php
/*
Template Name: Customer List
*/
?>

<!DOCTYPE HTML>

<html>

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

    <title>customers</title>

    <style>

        body {

            background: white;

            color: black;

            width: 95%;

            margin: 0 auto;

        }



        table {

            border: 1px solid #000;

            width: 100%;

        }



        table td, table th {

            border: 1px solid #000;

            padding: 6px;

		text-align: left;

        }



        article {

            border-top: 2px dashed #000;

            padding: 20px 0;

        }

    </style>

</head>

<body>



<section>



    <?php

global $wpdb;
$a= 'wc-completed';

        $myrows = $wpdb->get_results($wpdb->prepare("SELECT p.post_title, im.meta_value AS quan, im1.meta_value AS productID, bf.meta_value AS b_fname, bl.meta_value AS b_lname, be.meta_value AS b_email, i.order_id
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
													AND o.post_status = '%s'",$a));

        ?>



 

            <?php foreach ($myrows as $val) {

                ?>
                    <?= $val->b_email ?><br>
                <?php

            }

            ?>



</section>

</body>

</html>