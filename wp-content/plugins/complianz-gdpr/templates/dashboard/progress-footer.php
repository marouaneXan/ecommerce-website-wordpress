<?php
	$wizard_completed = COMPLIANZ::$wizard->wizard_completed_once();
	$cookieblocker = $wizard_completed && cmplz_get_value( 'disable_cookie_block' ) != 1 ? 'completed' : 'warning';
	$placeholder = $wizard_completed && cmplz_get_value( 'dont_use_placeholders' ) != 1 ? 'completed' : 'warning';
	$cookiebanner = cmplz_cookiebanner_should_load(true) ? 'completed' : 'warning';
?>
<div class="item-footer">
	<div>
		<div class="cmplz-footer-contents">
			<a class="button button-primary" href="<?php echo add_query_arg( array('page' => 'cmplz-wizard' ), admin_url('admin.php'))?>" ><?php _e("Continue Wizard", "complianz-gdpr") ?></a>
			<div class="cmplz-legend cmplz-stretch"></div>
			<div class="cmplz-legend"><?php echo cmplz_icon('bullet', $cookieblocker)?><span><?php _e("Cookie blocker", "complianz-gdpr")?></span></div>
			<div class="cmplz-legend"><?php echo cmplz_icon('bullet', $placeholder)?><span><?php _e("Placeholders", "complianz-gdpr")?></span></div>
			<div class="cmplz-legend"><?php echo cmplz_icon('bullet', $cookiebanner)?><span><?php _e("Cookie banner", "complianz-gdpr")?></span></div>

		</div>
	</div>
</div>
