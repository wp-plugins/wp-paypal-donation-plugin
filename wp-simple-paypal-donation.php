<?php
/*
Plugin Name: WP Paypal Donation
Version: v1.1
Plugin URI: http://www.shrawasti.com/about/development-center/
Author: Atif Mohammad
Author URI: http://www.shrawasti.com/atif/
Plugin Description: Wordpress plugin to receive donation in one click. Can be used in the sidebar, posts and pages.
WordPress PayPal Donations Plugin uses IPN to ensure the values are correct and you can also chose to display your total donations to date.
*/

/*
    This program is free software; you can redistribute it
    under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

$wp_paypal_donation_version_ = 1.1;

// Some default options
add_option('wp_paypal_donation_email', get_bloginfo('admin_email'));
add_option('paypal_donation_currency', 'USD');
add_option('wp_paypal_donate_header', 'Donation');

function paypalDonationReceive()
{
    $paypal_email = get_option('wp_paypal_donation_email');
    $donation_currency = get_option('paypal_donation_currency');
    $paypal_subject = get_option('wp_paypal_donate_header');
	$paypal_return_url_ = get_option('paypal_return_url');
	$donation_button = get_bloginfo('wpurl')."/wp-content/plugins/wp-paypal-donation-plugin/donate_btn.gif";

    /* === Paypal form === */
    $output_ .= '
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_donations" />
    ';
    $output_ .= "<input type=\"hidden\" name=\"business\" value=\"$paypal_email\" />";
    $output_ .= "<input type=\"hidden\" name=\"item_name\" value=\"$paypal_subject\" />";
    $output_ .= "<input type=\"hidden\" name=\"currency_code\" value=\"$donation_currency\" />";
	$output_ .= "<input type=\"hidden\" name=\"return\" value=\"$paypal_return_url_\" />";
    $output_ .= "<input type=\"image\" src=\"$donation_button\" name=\"submit\" alt=\"Make payments with payPal - it's fast, free and secure!\" />";
    $output_ .= '</form>';
    /* = end of paypal form = */
    return $output_;
}

function wp_paypal_donation_process($content)
{
    if (strpos($content, "<!--WP_PAYPAL_DONATION_-->") !== FALSE)
    {
        $content = preg_replace('/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content);
        $content = str_replace('<!--WP_PAYPAL_DONATION_-->', paypalDonationReceive(), $content);
    }
    return $content;
}


// Displays PayPal Donation Accept Options menu
function wp_paypal_donation_add_option_page() {
    if (function_exists('add_options_page')) {
        add_options_page('WP Paypal Donation', 'WP PayPal Donation', 8, __FILE__, 'wp_paypal_donation_options_page');
    }
}

function wp_paypal_donation_options_page() {

    global $wp_paypal_donation_version_;

    if (isset($_POST['info_update']))
    {
        echo '<div id="message" class="updated fade"><p><strong>';

        update_option('wp_paypal_donation_email', (string)$_POST["wp_paypal_donation_email"]);
        update_option('paypal_donation_currency', (string)$_POST["paypal_donation_currency"]);
        update_option('wp_paypal_donate_header', stripslashes((string)$_POST["wp_paypal_donate_header"]));
		update_option('paypal_return_url', stripslashes((string)$_POST["paypal_return_url"]));
        echo 'Options Updated!';
        echo '</strong></p></div>';
    }

    $paypal_donation_currency = stripslashes(get_option('paypal_donation_currency'));

    ?>

    <div class=wrap>

    <h2>WordPress Paypal Donation Settings v <?php echo $wp_paypal_donation_version_; ?></h2>

    <p>For more information and latest updates, please visit:<br />
    <a href="http://www.shrawasti.com">http://www.shrawasti.com</a></p>

    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <input type="hidden" name="info_update" id="info_update" value="true" />

    <fieldset class="options" style="border:1px solid #CDD8A7; padding:20px; margin:10px;">
    <legend>Paypal Display Setting:</legend>

    <p>There are three ways you can use this plugin:</p>
    <ol>
    <li> Add the trigger text <strong>&lt;!--WP_PAYPAL_DONATION_--&gt;</strong> to a post or page</li>
    <li> Call the function from a template file: <strong>&lt;?php echo paypalDonationReceive(); ?&gt;</strong></li>
    <li> Use the <strong>WP Paypal Donation</strong> Widget from the Widgets menu</li>
    </ol>

    </fieldset>

    <fieldset class="options" style="border:1px solid #CDD8A7; padding:20px; margin:10px;">
    <strong><legend>WP Paypal Donation Plugin Options :</legend></strong><br />

    <table width="100%" border="0" cellspacing="6" cellpadding="6" bgcolor="#EAFDF9">

    <tr valign="top"><td width="26%" align="right">
    <strong>Paypal Email Address:</strong>
    </td><td align="left">
    <input name="wp_paypal_donation_email" type="text" size="35" value="<?php echo get_option('wp_paypal_donation_email'); ?>"/>
    <br />This is the Paypal Email address where the donations will go.<br /><br />
    </td></tr>

    <tr valign="top"><td width="26%" align="right">
    <strong>Choose Donation Currency : </strong>
    </td><td align="left">
    <select id="paypal_donation_currency" name="paypal_donation_currency">
    <?php _e('<option value="USD"') ?><?php if ($paypal_donation_currency == "USD") echo " selected " ?><?php _e('>US Dollar</option>') ?>
    <?php _e('<option value="GBP"') ?><?php if ($paypal_donation_currency == "GBP") echo " selected " ?><?php _e('>Pound Sterling</option>') ?>
    <?php _e('<option value="EUR"') ?><?php if ($paypal_donation_currency == "EUR") echo " selected " ?><?php _e('>Euro</option>') ?>
    <?php _e('<option value="AUD"') ?><?php if ($paypal_donation_currency == "AUD") echo " selected " ?><?php _e('>Australian Dollar</option>') ?>
    <?php _e('<option value="CAD"') ?><?php if ($paypal_donation_currency == "CAD") echo " selected " ?><?php _e('>Canadian Dollar</option>') ?>
    <?php _e('<option value="NZD"') ?><?php if ($paypal_donation_currency == "NZD") echo " selected " ?><?php _e('>New Zealand Dollar</option>') ?>
    <?php _e('<option value="HKD"') ?><?php if ($paypal_donation_currency == "HKD") echo " selected " ?><?php _e('>Hong Kong Dollar</option>') ?>
    </select>
    <br />This is the currency for your visitors to make Donations in.<br /><br />
    </td></tr>

    <tr valign="top"><td width="26%" align="right">
    <strong>Donation Subject :</strong>
    </td><td align="left">
    <input name="wp_paypal_donate_header" type="text" size="35" value="<?php echo get_option('wp_paypal_donate_header'); ?>"/>
    <br />Enter the reason for the Donation. The visitors will see this text.<br /><br />
    </td></tr>
	<tr valign="top"><td width="26%" align="right">
    <strong>Return URL :</strong>
    </td><td align="left">
    <input name="paypal_return_url" type="text" size="35" value="<?php echo get_option('paypal_return_url'); ?>"/><br />
This is the URL the customer will be redirected to after a successful payment.
    <br />Like : http://wordpress/thanks</i><br /><br />
    </td></tr>

    </table>
    </fieldset>

    <div class="submit">
        <input type="submit" name="info_update" value="<?php _e('Update options'); ?> &raquo;" />
    </div>

    </form>
    </div><?php
}

function show_wp_paypal_donation_widget($args)
{
	extract($args);
	
	$paypal_donate_subject = get_option('wp_paypal_donate_header');
	echo $before_widget;
	echo $before_title . $paypal_donate_subject . $after_title;
    echo paypalDonationReceive();
    echo $after_widget;
}

function wp_paypal_donation_widget_control()
{
    ?>
    <p>
    <? _e("Set the Plugin Settings from the Settings menu"); ?>
    </p>
    <?php

}
function widget_wp_paypal_donation_init()
{
    $widget_options = array('classname' => 'widget_wp_paypal_donation', 'description' => __( "Display WP Paypal Donation.") );
    wp_register_sidebar_widget('wp_paypal_wp_paypal_donation_widgets_widgets', __('WP Paypal Donation'), 'show_wp_paypal_donation_widget', $widget_options);
    wp_register_widget_control('wp_paypal_donation_widgets', __('WP Paypal Donation'), 'wp_paypal_donation_widget_control' );
}

add_filter('the_content', 'wp_paypal_donation_process');

add_action('init', 'widget_wp_paypal_donation_init');

// Insert the wp_paypal_donation_add_option_page in the 'admin_menu'
add_action('admin_menu', 'wp_paypal_donation_add_option_page');

?>
