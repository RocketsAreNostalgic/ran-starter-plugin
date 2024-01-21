<?php
/**
 * The template for an example feature.
 * TODO add nonce verification. See TestimonialController for an example.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

//phpcs:disable WordPress.Security.NonceVerification.Missing,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
?>
<div class="wrap">

<h1>Example Feature Manager</h1>
	<?php settings_errors(); ?>

	<ul class="nav nav-tabs">
		<li class="<?php echo ! isset( $_POST['edit_feature'] ) ? 'active' : ''; ?>"><a href="#tab-1">Your Example</a></li>
		<li class="<?php echo isset( $_POST['edit_feature'] ) ? 'active' : ''; ?>">
			<a href="#tab-2">
				<?php echo isset( $_POST['edit_feature'] ) ? 'Edit' : 'Add'; ?> Example
			</a>
		</li>
		<li><a href="#tab-3">Export</a></li>
	</ul>

	<div class="tab-content">
		<div id="tab-1" class="tab-pane <?php echo ! isset( $_POST['edit_feature'] ) ? 'active' : ''; ?>">

			<h3>Manage Your Example Feature</h3>

			<?php
			$options = get_option( 'ran_plugin_example_settings' ) ?: array();

			?>

		</div>

		<div id="tab-2" class="tab-pane <?php echo isset( $_POST['edit_feature'] ) ? 'active' : ''; ?>">
			<form method="post" action="options.php">
				<?php
				settings_fields( 'ran_plugin_example_settings' );
				do_settings_sections( 'ran_feature' );
				submit_button();
				?>
			</form>
		</div>

	</div>
</div>
<?php //phpcs:enable ?>
