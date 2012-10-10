<?php

/**
 * WP-Simple-Settings-Framework
 *
 * Copyright (c) 2012 Matt Gates.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Framework
 * @subpackage  WP-Simple-Settings-Framework
 * @author      Matt Gates <info@mgates.me>
 * @copyright   2012 Matt Gates.
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://mgates.me
 * @version     1.0
 */

namespace Geczy\WPSettingsFramework;

class SF_Settings_API {

	public static $options = array();
	public static $current_options = array();

	public function __construct( $id, $title, $menu = 'plugins.php' ) {

		$this->set_assets_url( trailingslashit( plugins_url( 'assets/' , __FILE__ ) ) );

		$this->set_id( $id );
		$this->set_title( $title );
		$this->set_menu( $menu );

		$this->includes();
		$this->actions();

		self::$options = $this->load_options();
		self::$current_options = get_option( $this->get_id() . '_options' );

		$this->parse_options();

		/* If the option has no saved data, load the defaults. */
		/* @TODO: Can prob add this to the activation hook. */
		if ( !self::$current_options ) {
			$this->set_defaults();
		}
	}

	// ==================================================================
	//
	// Getters and setters.
	//
	// ------------------------------------------------------------------


	/**
	 * Get URL to assets
	 *
	 * @return string URL to assets
	 */
	public function get_assets_url() {
	    return $this->assets_url;
	}

	/**
	 * Set URL to assets
	 *
	 * @param  string $assets_url URL to assets
	 * @return [class type]    $this
	 */
	public function set_assets_url($assets_url) {
	    $this->assets_url = $assets_url;

	    return $this;
	}

	/**
	 * Retrieve ID
	 *
	 * @return string ID to use throughout the script
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set ID
	 *
	 * @param string  $id ID to use throughout the script
	 * @return [class type]    $this
	 */
	public function set_id( $id ) {
		$this->id = $id;

		return $this;
	}

	/**
	 * Retrieve settings page title
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Set settings page title
	 *
	 * @param string  $title [description]
	 * @return [class type]    $this
	 */
	public function set_title( $title ) {
		$this->title = $title;

		return $this;
	}


	/**
	 * Retrieve location of settings subpage
	 *
	 * @return [type] [description]
	 */
	public function get_menu() {
		return $this->menu;
	}

	/**
	 * Set settings menu location
	 *
	 * @param string  $menu Menu ID
	 * @return [class type]    $this
	 */
	public function set_menu( $menu ) {
		$this->menu = $menu;

		return $this;
	}

	// ==================================================================
	//
	// Begin initialization.
	//
	// ------------------------------------------------------------------


	private function includes() {
		require_once dirname( __FILE__ ) . '/sf-class-sanitize.php';
		new SF_Sanitize;
	}

	private function actions() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_init', array( &$this, 'register_options' ) );
		add_action( 'admin_menu', array( &$this, 'create_menu' ) );
	}

	/* Resources required on admin screen. */
	public function admin_enqueue_scripts() {
		wp_register_script( 'bootstrap-tooltip' , $this->get_assets_url() . 'js/bootstrap-tooltip.js' ,  array( 'jquery' ), '1.0' );
		wp_register_script( 'select2' , $this->get_assets_url() . 'js/select2/select2.min.js' ,  array( 'jquery' ), '1.0' );
		wp_register_style( 'select2' , $this->get_assets_url() . 'js/select2/select2.css' );
	}

	public function admin_print_scripts() {
		wp_enqueue_script( 'select2' );
		wp_enqueue_style( 'select2' );
		wp_enqueue_script( 'bootstrap-tooltip' );
	}

	public function register_options() {
		register_setting( $this->get_id() . '_options_nonce', $this->get_id() . '_options', array( &$this, 'validate_options' ) );
	}

	public function create_menu() {
		$page = add_submenu_page( $this->get_menu(), $this->get_title(), $this->get_title(), 'manage_options', $this->get_id(), array( &$this, 'init_settings_page' ) );
		add_action( 'admin_print_scripts-' . $page, array( &$this, 'admin_print_scripts' ) );
	}

	private function parse_options() {
		$options = self::$options;

		foreach ( $options as $option ) {

			if ( $option['type'] == 'heading' ) {
				$tab_name = sanitize_title( $option['name'] );
				$this->tab_headers[$tab_name] = $option['name'];

				continue;
			}

			$option['tab'] = $tab_name;
			$tabs[$tab_name][] = $option;

		}

		$this->tabs = $tabs;

		return $tabs;
	}

	private function load_options() {
		if ( empty( self::$options ) ) {
			require_once 'sf-options.php';
			return $options;
		}

		return self::$options;
	}

	public function validate_options( $input ) {
		if ( !isset( $_POST['update'] ) )
			return $this->get_defaults();

		$clean = array();
		$options = self::$options;

		$tabname = $_POST['currentTab'];

		foreach ( self::$current_options as $id => $value ) :
			$clean[$id] = $value;
		endforeach;

		foreach ( $this->tabs[$tabname] as $option ) :

			if ( ! isset( $option['id'] ) )
				continue;

			if ( ! isset( $option['type'] ) )
				continue;

			$id = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $option['id'] ) );

		// Set checkbox to false if it wasn't sent in the $_POST
		if ( 'checkbox' == $option['type'] && ! isset( $input[$id] ) )
			$input[$id] = false;

		// For a value to be submitted to database it must pass through a sanitization filter
		if ( has_filter( 'geczy_sanitize_' . $option['type'] ) ) {
			$clean[$id] = apply_filters( 'geczy_sanitize_' . $option['type'], $input[$id], $option );
		}

		endforeach;

		add_settings_error( $this->get_id(), 'save_options', __( 'Settings saved.', 'geczy' ), 'updated' );
		return $clean;
	}

	private function set_defaults() {
		$options = $this->get_defaults();
		update_option( $this->get_id() . '_options', $options );
	}

	private function get_defaults() {
		$output = array();
		$config = self::$options;

		foreach ( $config as $option ) {
			if ( ! isset( $option['id'] ) || ! isset( $option['std'] ) || ! isset( $option['type'] ) )
				continue;

			if ( has_filter( 'geczy_sanitize_' . $option['type'] ) ) {
				$output[$option['id']] = apply_filters( 'geczy_sanitize_' . $option['type'], $option['std'], $option );
			}
		}

		return $output;
	}

	private function template_body() {

		$options = self::$options;

		if ( empty( $options ) )
			return false;

?>

		<form method="post" action="options.php">
			<?php settings_fields( $this->get_id() . '_options_nonce' ); ?>
			<table class="form-table">

		<?php

		$tabs = $this->get_tabs();
		$tabname = !empty ( $_GET['tab'] ) ? $_GET['tab'] : $tabs[0]['slug'];

		foreach ( $this->tabs[$tabname] as $value ) :
			$this->settings_options_format( $value );
		endforeach;
?>

			</table>

			<p class="submit">
				<input type="hidden" name="currentTab" value="<?php echo $tabname; ?>">
				<input type="submit" name="update" class="button-primary" value="<?php _e( sprintf( 'Save %s changes', $this->tab_headers[$tabname] ), 'geczy' ); ?>" />
			</p>
		</form>

		<?php

	}

	private function settings_options_format( $value ) {
		if ( empty( $value ) )
			return false;

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
			'group',
		);

		foreach ( $defaultOptions as $key ) {
			if ( !is_array( $key ) && empty( $value[$key] ) ) $value[$key] = '';
			else if ( is_array( $key ) ) foreach ( $key as $val ) $value[$key][$val] = esc_attr( $value[$key][$val] );
		}

		/* Each to it's own variable for slim-ness' sakes. */
		extract( $value );

		$optionVal   = $this->get_option( $id );
		$optionVal   = $optionVal !== false && $optionVal !== null ? esc_attr ( $optionVal ) : esc_attr ( $std );
		$numberType  = $type == 'number' && !empty( $restrict ) && is_array( $restrict ) ? true : false;
		$title       = $name;
		$name        = $this->get_id() . "_options[{$id}]";

		$grouped     = !$title              ? 'style="padding-top:0px;"'                                       : '';
		$tip         =  $tip               ? '<a href="#" tip="' . $tip . '" class="tips" tabindex="99"></a>' : '';
		$description =  $desc && !$grouped && !$group && $type != 'checkbox' ? '<br /><small>' . $desc . '</small>'      : '<label for="' . $id . '"> ' .$desc . '</label>';
		$description =  ( $type == 'title' && !empty( $desc ) ) ? '<p>' . $desc . '</p>' : $description;

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
				 min="<?php echo $restrict['min']; ?>"
				 max="<?php echo $restrict['max']; ?>"
				 step="<?php echo isset( $restrict['step'] ) ? $restrict['step'] : 'any'; ?>"
				 <?php endif; ?>

				 class="regular-text <?php echo $class; ?>"
				 style="<?php echo $css; ?>"
				 placeholder="<?php echo $placeholder; ?>"
				 value="<?php echo $optionVal; ?>"
				/>
		<?php echo $description;
		break;

	case 'checkbox':
		?><input name="<?php echo $name; ?>"
				 id="<?php echo $id; ?>"
				 type="checkbox"
				 class="<?php echo $class; ?>"
				 style="<?php echo $css; ?>"
				 <?php if ( isset( $optionVal ) ) echo checked( $optionVal, '1', false ); else if ( $std ) echo checked( $std, '1', false ); ?>
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
							<?php if ( $key == $optionVal ) echo 'checked="checked"'; else if ( empty( $optionVal ) && $std == $key ) echo 'checked="checked"'; ?>
					/>
					<?php echo $val; ?>
					</label><br /><?php
		endforeach;
		break;

	case 'single_select_page':

		if ( empty( $optionVal ) )
			$optionVal = $std;

		$args = array(
			'name'       => $name,
			'id'         => $id,
			'sort_order' => 'ASC',
			'echo'       => 0,
			'selected'   => $optionVal
		);
		echo wp_dropdown_pages( $args );
		echo $description;
		?><script type="text/javascript">jQuery(function() {jQuery("#<?php echo $id; ?>").select2({ width: '350px' });});</script><?php
		break;

	case 'select':

		?><select id="<?php echo $id; ?>"
				  class="<?php echo $class; ?>"
				  style="<?php echo $css; ?>"
				  name="<?php echo $name; ?>"
		  		  >

		<?php foreach ( $options as $key => $val ) : ?>
					<option value="<?php echo $key; ?>" <?php selected( $optionVal, $key, true ); ?>>
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
				  ><?php if ( isset( $optionVal ) ) echo $optionVal; else echo $std; ?></textarea>
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

	public function init_settings_page() {

		$this->template_header();
		$this->template_body();
		$this->template_footer();

	}

	private function get_tabs() {
		$tabs = array();
		foreach ( self::$options as $option ) {

			if ( $option['type'] != 'heading' )
				continue;

			$option['slug'] = sanitize_title( $option['name'] );
			$tabs[] = $option;
		}
		return $tabs;
	}

	// Heading for Navigation
	private function display_tabs() {
		$tabs = $this->get_tabs();
		$tabname = !empty ( $_GET['tab'] ) ? $_GET['tab'] : $tabs[0]['slug'];
		$menu = '';

		foreach ( $tabs as $tab ) {
			$class = $tabname == $tab['slug'] ? 'nav-tab-active' : '';
			$menu .= sprintf( '<a id="%s-tab" class="nav-tab %s" title="%s" href="?page=%s&tab=%s">%s</a>', $tab['slug'], $class, $tab['name'], $this->get_id(), $tab['slug'], esc_html( $tab['name'] ) );
		}

		return $menu;
	}

	private function template_header() {
?>
		<div class="wrap">
			<?php screen_icon(); ?><h2><?php echo $this->get_title(); ?></h2>

			<h2 class="nav-tab-wrapper">
				<?php echo $this->display_tabs(); ?>
			</h2><?php

		if ( !empty ( $_REQUEST['settings-updated'] ) )
			settings_errors();

	}

	private function template_footer() {
		echo '</div>';
	}

	public function update_option( $name, $value ) {
		self::$current_options[$name] = $value;
		return update_option( $this->get_id() .'_options', self::$current_options );
	}

	public function get_option( $name, $default = false ) {
		return isset( self::$current_options[$name] ) ? self::$current_options[$name] : $default;
	}

}
