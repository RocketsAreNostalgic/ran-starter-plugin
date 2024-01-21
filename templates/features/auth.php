<?php
/**
 * Auth template
 *
 * @package RanPlugin
 */

declare(strict_types = 1);

?>
<form id="ran-auth-form" action="#" method="post" data-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
	<div class="auth-btn">
		<input class="submit_button" type="button" value="Login" id="ran-show-auth-form">
	</div>
	<div id="ran-auth-container" class="auth-container">
		<a id="ran-auth-close" class="close" href="#">&times;</a>
		<h2>Site Login</h2>
		<label for="username">Username</label>
		<input id="username" type="text" name="username">
		<label for="password">Password</label>
		<input id="password" type="password" name="password">
		<input class="submit_button" type="submit" value="Login" name="submit">
		<p class="status"></p>

		<p class="actions">
			<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">Forgot Password?</a> - <a href="<?php echo esc_url( wp_registration_url() ); ?>">Register</a>
		</p>

		<?php wp_nonce_field( 'ajax-login-nonce', 'ran_auth' ); ?>
	</div>
</form>
