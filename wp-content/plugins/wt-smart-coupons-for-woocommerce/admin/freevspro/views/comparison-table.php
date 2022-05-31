<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}

$no_icon='<span class="dashicons dashicons-dismiss" style="color:#ea1515;"></span>&nbsp;';
$yes_icon='<span class="dashicons dashicons-yes-alt" style="color:#18c01d;"></span>&nbsp;';

global $wp_version;
if(version_compare($wp_version, '5.2.0')<0)
{
 	$yes_icon='<img src="'.plugin_dir_url(dirname(__FILE__)).'assets/images/tick_icon_green.png" style="float:left;" />&nbsp;';
}

$supported_coupon_types_arr=array(
		__('Give away', 'wt-smart-coupons-for-woocommerce'),
		__('URL coupons', 'wt-smart-coupons-for-woocommerce'),
	);
$pro_only_coupon_types_arr=array(
		__('Purchase history-based coupons', 'wt-smart-coupons-for-woocommerce'),
		__('Store credit', 'wt-smart-coupons-for-woocommerce'),
		__('Gift coupons', 'wt-smart-coupons-for-woocommerce'),
		__('Sign-up coupons', 'wt-smart-coupons-for-woocommerce'),
		__('Cart abandonment coupons', 'wt-smart-coupons-for-woocommerce'),
		__('Combo coupons', 'wt-smart-coupons-for-woocommerce'),
	);

$basic_supported_coupon_types=$yes_icon.implode("<br />$yes_icon", $supported_coupon_types_arr)."<br />".$no_icon.implode("<br />$no_icon", $pro_only_coupon_types_arr);

//'Give away coupons' pro supports multiple products
$supported_coupon_types_arr[0]=__('Give away (multiple products)', 'wt-smart-coupons-for-woocommerce');
$pro_supported_coupon_types=$yes_icon.implode("<br />$yes_icon", array_merge($supported_coupon_types_arr, $pro_only_coupon_types_arr));



$supported_coupon_restrictions_arr=array(
		__('Shipping method', 'wt-smart-coupons-for-woocommerce'),
		__('Payment method', 'wt-smart-coupons-for-woocommerce'),
		__('User roles', 'wt-smart-coupons-for-woocommerce'),
		__('Product quantity', 'wt-smart-coupons-for-woocommerce'),
		__('Product subtotal', 'wt-smart-coupons-for-woocommerce'),
	);

$pro_only_coupon_restrictions_arr=array(
		__('Country', 'wt-smart-coupons-for-woocommerce'),
	);


$basic_supported_coupon_restrictions=$yes_icon.implode("<br />$yes_icon", $supported_coupon_restrictions_arr)."<br />".$no_icon.implode("<br />$no_icon", $pro_only_coupon_restrictions_arr);
$pro_supported_coupon_restrictions=$yes_icon.implode("<br />$yes_icon", array_merge($supported_coupon_restrictions_arr, $pro_only_coupon_restrictions_arr));


/**
*	Array format
*	First 	: Feature
*	Second 	: Basic availability. Supports: Boolean, Array(Boolean and String values), String
*	Pro 	: Pro availability. Supports: Boolean, Array(Boolean and String values), String
*/
$comparison_data=array(
	array(
		__('Supported coupon types', 'wt-smart-coupons-for-woocommerce'),
		$basic_supported_coupon_types,
		$pro_supported_coupon_types,
	),
	array(
		__('Applicable coupon restrictions', 'wt-smart-coupons-for-woocommerce'),
		$basic_supported_coupon_restrictions,
		$pro_supported_coupon_restrictions,
	),
	array(
		__('Apply coupon automatically', 'wt-smart-coupons-for-woocommerce'),
		true,
		true,
	),
	array(
		__('Duplicate coupons', 'wt-smart-coupons-for-woocommerce'),
		true,
		true,
	),
	array(
		__('Set a coupon start date', 'wt-smart-coupons-for-woocommerce'),
		true,
		true,
	),
	array(
		__("Select page(s) to display applicable coupons", 'wt-smart-coupons-for-woocommerce'),
		array(
			$yes_icon.__('My account page', 'wt-smart-coupons-for-woocommerce')
		),
		array(
			$yes_icon.__('My account page', 'wt-smart-coupons-for-woocommerce')."<br />",
			$yes_icon.__('Cart', 'wt-smart-coupons-for-woocommerce')."<br />",
			$yes_icon.__('Checkout', 'wt-smart-coupons-for-woocommerce')
		),
	),
	array(
		__("Coupon styling", 'wt-smart-coupons-for-woocommerce'),
		true,
		true,
	),
	array(
		__("Coupon templates", 'wt-smart-coupons-for-woocommerce'),
		__("Standard", 'wt-smart-coupons-for-woocommerce'),
		__("Multiple options", 'wt-smart-coupons-for-woocommerce'),
	),
	array(
		__("Set discount for giveaway products", 'wt-smart-coupons-for-woocommerce'),
		false,
		true,
	),
	array(
		__("Option to apply tax after discount (give away products)", 'wt-smart-coupons-for-woocommerce'),
		false,
		true,
	),
	array(
		__("Import coupons", 'wt-smart-coupons-for-woocommerce'),
		false,
		true,
	),
	array(
		__("Bulk generate coupons", 'wt-smart-coupons-for-woocommerce'),
		false,
		true,
	),
	array(
		__("Supports coupon shortcodes", 'wt-smart-coupons-for-woocommerce'),
		false,
		true,
	),
	array(
		__("Display count down discount sales banner", 'wt-smart-coupons-for-woocommerce'),
		false,
		true,
	),
	array(
		__("Add coupon expiry in days", 'wt-smart-coupons-for-woocommerce'),
		false,
		true,
	),
	array(
		__("Supports custom coupon code format (prefix, suffix, length)", 'wt-smart-coupons-for-woocommerce'),
		false,
		true,
	),
	array(
		__("Custom endpoints and endpoint title for coupon listing page", 'wt-smart-coupons-for-woocommerce'),
		false,
		true,
	),
);
function wt_smcpn_free_vs_pro_column_vl($vl, $yes_icon, $no_icon)
{
	if(is_array($vl))
	{
		foreach ($vl as $value)
		{
			if(is_bool($value))
			{
				echo ($value ? $yes_icon : $no_icon);
			}else
			{
				//string only
				echo $value;
			}
		}
	}else
	{
		if(is_bool($vl))
		{
			echo ($vl ? $yes_icon : $no_icon);
		}else
		{
			//string only
			echo $vl;
		}
	}
}
?>

<table class="wt_smcpn_freevs_pro">
	<tr>
		<td style="width:240px;"><?php _e('FEATURES', 'wt-smart-coupons-for-woocommerce'); ?></td>
		<td><?php _e('FREE', 'wt-smart-coupons-for-woocommerce'); ?></td>
		<td><?php _e('PREMIUM', 'wt-smart-coupons-for-woocommerce'); ?></td>
	</tr>
	<?php
	foreach ($comparison_data as $val_arr)
	{
		?>
		<tr>
			<td><?php echo $val_arr[0];?></td>
			<td>
				<?php
				wt_smcpn_free_vs_pro_column_vl($val_arr[1], $yes_icon, $no_icon);
				?>
			</td>
			<td>
				<?php
				wt_smcpn_free_vs_pro_column_vl($val_arr[2], $yes_icon, $no_icon);
				?>
			</td>
		</tr>
		<?php
	}
	?>
</table>