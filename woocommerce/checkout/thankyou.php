<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bookingId=get_post_meta($order->get_id(),'chbs_booking_id',true);

$class=array('chbs-main');

if($bookingId>0)
{
    $Booking=new CHBSBooking();
    if(($booking=$Booking->getBooking($bookingId))!==false)
        array_push($class,'chbs-booking-form-id-'.(int)$booking['meta']['booking_form_id']);
}

?>

<div<?php echo CHBSHelper::createCSSClassAttribute($class); ?>>

    <div class="woocommerce-order">

        <?php if ( $order ) : ?>

            <?php if ( $order->has_status( 'failed' ) ) : ?>

                <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html__( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'chauffeur-booking-system' ); ?></p>

            <?php else : ?>

                <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'chauffeur-booking-system' ), $order ); ?></p>

            <?php endif; ?>

        <?php else : ?>

            <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'chauffeur-booking-system' ), null ); ?></p>

        <?php endif; ?>

    </div>
    
</div>
