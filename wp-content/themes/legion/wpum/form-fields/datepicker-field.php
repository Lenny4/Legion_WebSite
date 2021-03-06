<?php
/**
 * WPUM Template: date picker form field.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2016, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4.1
 */

?>
<input
	type="text"
	class="form-control"
	name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>"
	id="<?php echo esc_attr( $key ); ?>"
	placeholder="<?php echo ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : ''; ?>"
	value="<?php echo isset( $field['value'] ) ? esc_attr( $field['value'] ) : ''; ?>"
	<?php if ( ! empty( $field['required'] ) ) echo 'required'; ?>
	<?php if ( isset( $field['read_only'] ) && $field['read_only'] ) echo 'readonly'; ?>
	data-dateformat="<?php echo ! empty( $field['date_format'] ) ? esc_attr( $field['date_format'] ) : ''; ?>"
	/>
<?php if ( ! empty( $field['description'] ) ) : ?><small class="description"><?php echo $field['description']; ?></small><?php endif; ?>
