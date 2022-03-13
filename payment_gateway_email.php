<?php
/*
 * Plugin Name: Payment Gateway Email
 * Description: Custom payment gateway using email to purchase product.
 * Author: Jerry Chen
 * Author URI: https://jerrychen.io/
 * Version: 1.0
 */

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

add_action( 'plugins_loaded', 'initialize_gateway_class' );
function initialize_gateway_class() {
    class Payment_Gateway_Email extends WC_Payment_Gateway {
        // plugin code here

        public function __construct() {
            $this->id = 'payment_gateway_email_id'; // payment gateway ID​
            $this->icon = ''; // payment gateway icon
            $this->has_fields = false; // for custom credit card form
            $this->title = __( 'Payment Gateway Email', 'text-domain' ); // vertical tab title
            $this->method_title = __( 'Payment Gateway Email', 'text-domain' ); // payment method name
            $this->method_description = __( 'Custom email payment gateway', 'text-domain' ); // payment method description​
            $this->supports = array( 'default_credit_card_form' );

            // load backend options fields
            $this->init_form_fields();

            // load the settings.
            $this->init_settings();
            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );
            $this->enabled = $this->get_option( 'enabled' );
            $this->test_mode = 'yes' === $this->get_option( 'test_mode' );
            $this->private_key = $this->test_mode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
            $this->publish_key = $this->test_mode ? $this->get_option( 'test_publish_key' ) : $this->get_option( 'publish_key' );

            // Action hook to saves the settings
            if(is_admin()) {
                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            }
            

            add_action( 'woocommerce_thank_you_' . $this->id, array( $this, 'thank_you_page' ) );
            // Action hook to load custom JavaScript
            //add_action( 'wp_enqueue_scripts', array( $this, 'payment_gateway_scripts' ) );
        }
        
        public function init_form_fields(){
        
            $this->form_fields = array(
                'enabled' => array(
                    'title'       => __( 'Enable/Disable', 'text-domain' ),
                    'label'       => __( 'Enable Payment Gateway Email', 'text-domain' ),
                    'type'        => 'checkbox',
                    'description' => __( 'This enable the Payment Gateway Email which allow to accept payment through email.', 'text-domain' ),
                    'default'     => 'no',
                    'desc_tip'    => true
                ),
                'title' => array(
                    'title'       => __( 'Title', 'text-domain'),
                    'type'        => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'text-domain' ),
                    'default'     => __( 'Enter your email address', 'text-domain' ),
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => __( 'Description', 'text-domain' ),
                    'type'        => 'textarea',
                    'description' => __( 'This controls the description which the user sees during checkout.', 'text-domain' ),
                    'default'     => __( 'After receiving your order we will contact you through email.', 'text-domain' ),
                )
            );
        }

        public function payment_fields() {

            if ( $this->description ) {
                if ( $this->test_mode ) {
                    $this->description .= ' Test mode is enabled. You can use the dummy credit card numbers to test it.';
                }
                echo wpautop( wp_kses_post( $this->description ) );
            }
            
            ?>
        
            <fieldset id="wc-<?php echo esc_attr( $this->id ); ?>-cc-form" style="background:transparent;">
                <div class="clear"></div>
            </fieldset>
        
            <?php
         
        }
    }
}

add_filter( 'woocommerce_payment_gateways', 'add_custom_gateway_class' );
function add_custom_gateway_class( $gateways ) {
    $gateways[] = 'Payment_Gateway_Email'; // payment gateway class name
    return $gateways;
}

?>