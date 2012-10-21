<?php

/**
 * Format an options array into HTML
 */

namespace Geczy\WPSettingsFramework;

class SF_Format_Options extends SF_Settings_API {

	/**
	 * Format an option array into HTML
	 *
	 * @param array   $value Single option array.
	 *
	 * @access public
	 *
	 * @return string HTML.
	 */
	public function settings_options_format( $value ) {
		if ( empty( $value ) ) return false;

		$defaultOptions = array(
			'name',
			'desc',
			'placeholder',
			'class',
			'tip',
			'id',
			'css',
			'type',
			'std',
			'options',
			'restrict',
		);

		foreach ( $defaultOptions as $key ) {
			if ( !is_array( $key ) && !isset( $value[$key] ) ) $value[$key] = '';
			else if ( is_array( $key ) ) foreach ( $key as $val ) $value[$key][$val] = esc_attr( $value[$key][$val] );
		}

		/* Each to it's own variable for slim-ness' sakes. */
		extract( $value );

		$optionVal   = $this->get_option( $id );
		$optionVal   = $optionVal !== false ? esc_attr ( $optionVal ) : false;
		$numberType  = $type == 'number' && !empty( $restrict ) && is_array( $restrict ) ? true : false;
		$title       = $name;
		$name        = $this->$id . "_options[{$id}]";

		$grouped     = !$title ? 'style="padding-top:0px;"' : '';
		$tip         =  $tip ? '<a href="#" title="' . $tip . '" class="sf-tips" tabindex="99"></a>' : '';
		$description =  $desc && !$grouped && $type != 'checkbox' ? '<br /><small>' . $desc . '</small>' : '<label for="' . $id . '"> ' .$desc . '</label>';
		$description =  ( ( $type == 'title' || $type == 'radio' ) && !empty( $desc ) ) ? '<p>' . $desc . '</p>' : $description;

		/* Header of the option. */
		?><tr valign="top">
		<?php if ( $type != 'heading' && $type != 'title' ) : ?>
					<th scope="row" <?php echo $grouped; ?> >

						<?php echo $tip; ?>

						<?php if ( !$grouped ) : ?>
						<label for="<?php echo $name; ?>" class="description"><?php echo $title; ?></label>
						<?php endif; ?>

					</th>
		<?php endif; ?>
					<td <?php echo $grouped; ?> >
		<?php
		/* Meat & footer of the option. */
		switch ( $type ) :

		case 'title':
			?><thead>
			<tr>
				<th scope="col" colspan="2">
					<h3 class="title"><?php echo $title; ?></h3>
					<?php echo $description; ?>
				</th>
			</tr>
		  </thead><?php
		break;

	case 'text'   :
	case 'number' :
		?><input name="<?php echo $name; ?>"
				 id="<?php echo $id; ?>"
				 type="<?php echo $type; ?>"

				 <?php if ( $numberType ): ?>
				 min="<?php echo !empty( $restrict['min'] ) ? $restrict['min'] : ''; ?>"
				 max="<?php echo !empty( $restrict['max'] ) ? $restrict['max'] : ''; ?>"
				 step="<?php echo isset( $restrict['step'] ) ? $restrict['step'] : 'any'; ?>"
				 <?php endif; ?>

				 class="regular-text <?php echo $class; ?>"
				 style="<?php echo $css; ?>"
				 placeholder="<?php echo $placeholder; ?>"
				 value="<?php echo $optionVal !== false ? $optionVal : $std; ?>"
				/>
		<?php echo $description;
		break;

	case 'checkbox':
		?><input name="<?php echo $name; ?>"
				 id="<?php echo $id; ?>"
				 type="checkbox"
				 class="<?php echo $class; ?>"
				 style="<?php echo $css; ?>"
				 <?php if ( $optionVal !== false ) echo checked( $optionVal, 1, false ); else echo checked( $std, 1, false ); ?>
				 />
		<?php echo $description;
		break;

	case 'radio':
		foreach ( $options as $key => $val ) : ?>
					<label class="radio">
					<input type="radio"
						   name="<?php echo $name; ?>"
						   id="<?php echo $key; ?>"
						   value="<?php echo $key; ?>"
						   class="<?php echo $class; ?>"
							<?php if ( $optionVal !== false ) echo checked( $optionVal, $key, false ); else echo checked( $std, $key, false ); ?>
					/>
					<?php echo $val; ?>
					</label><br /><?php
		endforeach;
		echo $description;
		break;

	case 'single_select_page':

		$selected = ( $optionVal !== false ) ? $optionVal : $std;

		$args = array(
			'name'       => $name,
			'id'         => $id,
			'sort_order' => 'ASC',
			'echo'       => 0,
			'selected'   => $selected
		);
		echo wp_dropdown_pages( $args );
		echo $description;
		?><script type="text/javascript">jQuery(function() {jQuery("#<?php echo $id; ?>").select2({ width: '350px' });});</script><?php
		break;

	case 'select':

		$selected = ( $optionVal !== false ) ? $optionVal : $std;

		?><select id="<?php echo $id; ?>"
				  class="<?php echo $class; ?>"
				  style="<?php echo $css; ?>"
				  name="<?php echo $name; ?>"
				  >

		<?php foreach ( $options as $key => $val ) : ?>
					<option value="<?php echo $key; ?>" <?php selected( $selected, $key, true ); ?>>
					<?php echo $val; ?>
					</option>
		<?php endforeach; ?>
		</select>
		<script type="text/javascript">jQuery(function() {jQuery("#<?php echo $id; ?>").select2({ width: '350px' });});</script>
		<?php break;

	case 'textarea':
		?><textarea name="<?php echo $name; ?>"
							id="<?php echo $id; ?>"
							class="large-text <?php echo $class; ?>"
							style="<?php if ( $css ) echo $css; else echo 'width:300px;'; ?>"
							placeholder="<?php echo $placeholder; ?>"
							rows="3"
				  ><?php echo ( $optionVal !== false ) ? $optionVal : $std; ?></textarea>
				<?php echo $description;
		break;

		// Heading for Navigation
	case 'heading' :
		?><h3><?php echo esc_html( $value['name'] ); ?></h3><?php
		break;

		endswitch;

		/* Footer of the option. */
		if ( $type != 'heading' || $type != 'title' ) echo '</td></tr>';

	}

}
