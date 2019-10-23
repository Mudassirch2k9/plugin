<?php
    use OPA\Inc\Base\BaseController;

    $baseController = new BaseController();
?>

<div class="wrap">
	<h1>WP Product Advisor Plugin</h1>
	<?php settings_errors(); ?>

	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab-1"><?php _e('Manage Settings', 'wp-product-advisor'); ?></a></li>
		<li><a href="#tab-2"><?php _e( 'Updates', 'wp-product-advisor' ); ?></a></li>
		<li><a href="#tab-3"><?php _e( 'About', 'wp-product-advisor' ); ?></a></li>
	</ul>

	<div class="tab-content">
		<div id="tab-1" class="tab-pane active">

			<form method="post" action="options.php">
				<?php 
					settings_fields( $baseController->option_group_plugin_settings );
					do_settings_sections( 'opa_settings' );
					submit_button();
				?>
			</form>
			
		</div>

		<div id="tab-2" class="tab-pane">
			<h3><?php _e( 'Updates', 'wp-product-advisor' ); ?></h3>
		</div>

		<div id="tab-3" class="tab-pane">
			<h3><?php _e( 'About', 'wp-product-advisor' ); ?></h3>
		</div>
	</div>
</div>