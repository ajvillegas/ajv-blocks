<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.alexisvillegas.com
 * @since      1.0.0
 *
 * @package    AJV_Blocks
 * @subpackage AJV_Blocks/admin/partials
 */

?>
<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<p>
		<?php
		echo esc_html__( 'Use the options below to enable or disable any of the custom blocks or the core block enhancements added by the plugin.', 'ajv-blocks' );
		?>
	</p>

	<form method="post" action="options.php">
		<?php
		// Grab all options and assign default values.
		$defaults = array(
			'core-block-enhancements' => 1,
		);

		foreach ( $this->blocks as $block ) {
			$defaults[ $block . '-block' ] = 1;
		}

		$options = wp_parse_args( get_option( $this->plugin_name, $defaults ), $defaults );

		settings_fields( $this->plugin_name );
		do_settings_sections( $this->plugin_name );
		?>

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Enable Custom Blocks', 'ajv-blocks' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php echo esc_html__( 'Enable Custom Blocks', 'ajv-blocks' ); ?></span></legend>

							<?php
							foreach ( $this->blocks as $block ) {
								$field_name = $block . '-block';
								$block_name = str_replace( '-', ' ', $block );

								?>
								<label for="<?php echo esc_attr( $field_name ); ?>">
									<input id="<?php echo esc_attr( $field_name ); ?>" name="<?php echo esc_attr( $this->plugin_name ); ?>[<?php echo esc_attr( $field_name ); ?>]" type="checkbox" value="1" <?php checked( $options[ $field_name ], 1 ); ?>>
									<?php echo esc_html( ucwords( $block_name ) ) . ' Block'; ?>
								</label>
								<br>
								<?php
							}
							?>
						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php echo esc_html__( 'Core Block Enhancements', 'ajv-blocks' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php echo esc_html__( 'Core Block Enhancements', 'ajv-blocks' ); ?></span></legend>

							<label for="core-block-enhancements">
								<input id="core-block-enhancements" name="<?php echo esc_attr( $this->plugin_name ); ?>[core-block-enhancements]" type="checkbox" value="1" <?php checked( $options['core-block-enhancements'], 1 ); ?>>
								<?php echo esc_html__( 'Enable custom settings for the Columns, Cover, Group and Spacer blocks.', 'ajv-blocks' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>

		<?php submit_button( esc_html__( 'Save Changes', 'ajv-blocks' ), 'primary', 'submit', true ); ?>
	</form>
</div>
