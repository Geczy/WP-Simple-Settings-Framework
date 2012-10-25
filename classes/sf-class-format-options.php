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

		$defaults = array(
			'name'        => '',
			'desc'        => '',
			'placeholder' => '',
			'class'       => '',
			'tip'         => '',
			'id'          => '',
			'css'         => '',
			'type'        => 'text',
			'std'         => '',
			'options'     => array(),
			'restrict'    => array(),
			'disabled'    => '',
		);

		/* Each to it's own variable for slim-ness' sakes. */
		extract(shortcode_atts($defaults, $value));

		$restrict_defaults = array(
			'min'  => 0,
			'max'  => '',
			'step' => 'any',
		);

		$restrict = shortcode_atts($restrict_defaults, $restrict);

		$value   = $this->get_option( $id );
		$value   = $value !== false ? esc_attr ( $value ) : false;

		$title   = $name;
		$name    = $this->id . "_options[{$id}]";

		$grouped = !$title ? ' style="padding-top:0px;"' : '';
		$tip     = SF_Format_Options::get_formatted_tip($tip);

		$description = $desc && !$grouped && $type != 'checkbox'
						? '<br /><small>' . $desc . '</small>'
						: '<label for="' . $id . '"> ' .$desc . '</label>';

		$description = ( ( $type == 'title' || $type == 'radio' ) && !empty( $desc ) )
						? '<p>' . $desc . '</p>'
						: $description; ?>

		<!-- Header of the option. -->
		<tr valign="top">
		<?php if ( $type != 'heading' && $type != 'title' ) : ?>
			<th scope="row"<?php echo $grouped; ?>>

				<?php echo $tip; ?>

				<?php if ( !$grouped ) : ?>
					<label for="<?php echo $name; ?>" class="description"><?php echo $title; ?></label>
				<?php endif; ?>

			</th>
		<?php endif; ?>
					<td <?php echo $grouped; ?> >

		<?php switch ( $type ) :

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
	case 'html':
		echo $prewritten;
		break;
	case 'prewritten_text':
				?><input name="<?php echo $name; ?>"
				 id="<?php echo $id; ?>"
				 type="<?php echo $type; ?>"
				<?php if ($disabled == 'true'){ ?> disabled="<?php echo $disabled;?>"<?php }?>
				 class="regular-text <?php echo $class; ?>"
				 style="<?php echo $css; ?>"
				 placeholder="<?php echo $placeholder; ?>"
				 value="<?php echo $prewritten; ?>"
				/>
		<?php echo $description;
		break;
	case 'text'   :
	case 'number' :
		?><input name="<?php echo $name; ?>"
				 id="<?php echo $id; ?>"
				 type="<?php echo $type; ?>"
				<?php if ($disabled == 'true'){ ?> disabled="<?php echo $disabled;?>"<?php }?>
				 <?php if ( $type == 'number' ): ?>
				 min="<?php echo $restrict['min']; ?>"
				 max="<?php echo $restrict['max']; ?>"
				 step="<?php echo $restrict['step']; ?>"
				 <?php endif; ?>

				 class="regular-text <?php echo $class; ?>"
				 style="<?php echo $css; ?>"
				 placeholder="<?php echo $placeholder; ?>"
				 value="<?php echo $value !== false ? $value : $std; ?>"
				/>
		<?php echo $description;
		break;

	case 'checkbox':
		?><input name="<?php echo $name; ?>"
				 id="<?php echo $id; ?>"
				 type="checkbox"
				 class="<?php echo $class; ?>"
				 style="<?php echo $css; ?>"
				 <?php if ( $value !== false ) echo checked( $value, 1, false ); else echo checked( $std, 1, false ); ?>
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
							<?php if ( $value !== false ) echo checked( $value, $key, false ); else echo checked( $std, $key, false ); ?>
					/>
					<?php echo $val; ?>
					</label><br /><?php
		endforeach;
		echo $description;
		break;

	case 'single_select_page':

		$selected = ( $value !== false ) ? $value : $std;

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

		$selected = ( $value !== false ) ? $value : $std;

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
				  ><?php echo ( $value !== false ) ? $value : $std; ?></textarea>
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

	private function get_formatted_tip($tip) {
		return $tip ? sprintf('<a href="#" title="%s" class="sf-tips" tabindex="99"></a>', $tip) : '';
	}

}
