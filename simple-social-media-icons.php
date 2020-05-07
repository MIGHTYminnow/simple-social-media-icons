<?php
/**
 * Plugin Name: Simple Social Media Icons
 * Plugin URI: https://github.com/MIGHTYminnow/simple-social-media-icons
 * Description: Adds a widget and shortcode that displays social media icon links using FontAwesome icons.
 * Version: 1.3.0-beta
 * Author: MIGHTYminnow & Braad Martin
 * Author URI: https://mightyminnow.org
 * Text Domain: simple-social-media-icons
 * License: GPL2
 */

add_action( 'plugins_loaded', 'simple_social_media_icons_start' );
/**
 * Initialize the Simple Social Media Icons plugin.
 *
 * @since  1.0.0
 */
function simple_social_media_icons_start() {
    global $simple_social_media_icons;
    $simple_social_media_icons = Simple_Social_Media_Icons_Plugin::get_instance();
}

/**
 * Simple Social Media Icons plugin class
 *
 * @since  1.0.0
 */
class Simple_Social_Media_Icons_Plugin {

    /**
     * Plugin slug.
     *
     * @since  1.0.0
     *
     * @var    string
     */
    const SLUG = 'simple-social-media-icons';

    /**
     * Plugin display name.
     *
     * @since  1.0.0
     *
     * @var    string
     */
    private $plugin_display_name;

    /**
     * Plugin option name.
     *
     * @since  1.0.0
     *
     * @var    string
     */
    protected $option_name;

    /**
     * Plugin options.
     *
     * @since  1.0.0
     *
     * @var    string
     */
    protected $options;

    /**
     * Default options.
     *
     * Used for setting uninitialized plugin options.
     *
     * @since  1.0.0
     *
     * @var    array
     */
    protected $option_defaults = array(
        'icon_style'                => '1',
        'include_facebook_icon'     => true,
        'include_twitter_icon'      => true,
        'include_pinterest_icon'    => true,
        'include_instagram_icon'    => true,
        'include_google_plus_icon'  => false,
        'include_youtube_icon'      => true,
        'include_vimeo_icon'        => true,
        'include_soundcloud_icon'   => true,
        'include_linkedin_icon'     => false,
        'include_flickr_icon'       => false,
        'include_github_icon'       => false,
        'include_codepen_icon'      => false,
        'include_wordpress_icon'    => false,
        'include_medium_icon'       => false,
    );

    /**
     * List of Social Networks.
     *
     * @since  1.3.0
     *
     * @var    array
     */
    protected $social_networks;

    /**
     * Instance of this class.
     *
     * @since  1.0.0
     *
     * @var    object
     */
    protected static $instance = false;

    /**
     * Returns the instance of this class, and initializes the instance if it
     * doesn't already exist.
     *
     * @since   1.0.0
     *
     * @return  Simple_Social_Media_Icons  The SSMI object.
     */
    public static function get_instance() {
        if ( ! self::$instance ) {
	      self::$instance = new self();
	    }
	    return self::$instance;
	}
	
	public function get_social_networks() {
		return $this->social_networks;
	}

    /**
     * Simple Social Media Icons Plugin constructor.
     *
     * @since  1.0.0
     */
    private function __construct() {
		// List of Social Networks
		$this->social_networks = [
			(object) [
				'id' => 'facebook',
				'fa_code' => 'facebook',
				'name' => 'Facebook',
				'brand_color' => '#3b5998',
			],
			(object) [
				'id' => 'twitter',
				'fa_code' => 'twitter',
				'name' => 'Twitter',
				'brand_color' => '#00aced',
			],
			(object) [
				'id' => 'pinterest',
				'fa_code' => 'pinterest',
				'name' => 'Pinterest',
				'brand_color' => '#cb2027',
			],
			(object) [
				'id' => 'instagram',
				'fa_code' => 'instagram',
				'name' => 'Instagram',
				'brand_color' => '#517fa4',
			],
			(object) [
				'id' => 'google_plus',
				'fa_code' => 'google-plus',
				'name' => 'Google Plus',
				'brand_color' => '#dd4b39',
			],
			(object) [
				'id' => 'youtube',
				'fa_code' => 'youtube',
				'name' => 'YouTube',
				'brand_color' => '#bb0000',
			],
			(object) [
				'id' => 'vimeo',
				'fa_code' => 'vimeo',
				'name' => 'Vimeo',
				'brand_color' => '#00AFF5',
			],
			(object) [
				'id' => 'soundcloud',
				'fa_code' => 'soundcloud',
				'name' => 'SoundCloud',
				'brand_color' => '#FF4200',
			],
			(object) [
				'id' => 'linkedin',
				'fa_code' => 'linkedin',
				'name' => 'LinkedIn',
				'brand_color' => '#007bb6',
			],
			(object) [
				'id' => 'flickr',
				'fa_code' => 'flickr',
				'name' => 'Flickr',
				'brand_color' => '#ff0084',
			],
			(object) [
				'id' => 'github',
				'fa_code' => 'github',
				'name' => 'GitHub',
				'brand_color' => '#222',
			],
			(object) [
				'id' => 'codepen',
				'fa_code' => 'codepen',
				'name' => 'CodePen',
				'brand_color' => '#113472',
			],
			(object) [
				'id' => 'wordpress',
				'fa_code' => 'wordpress',
				'name' => 'WordPress',
				'brand_color' => '#464646',
			],
			(object) [
				'id' => 'medium',
				'fa_code' => 'medium',
				'name' => 'Medium',
				'brand_color' => '#000',
			],
		];

        // Perform plugin initialization actions.
        $this->initialize();

        // Load the plugin text domain.
        add_action( 'init', array( $this, 'load_text_domain' ) );

        // Set up the admin settings page.
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'add_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_page_enqueue' ) );

        // Register the CSS files for the shortcode (they will get enqueued later if the widget or shortcode is being used)
        add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts') );

        // Register the widget.
        add_action( 'widgets_init', array( $this, 'register_widget' ) );

        // Register the shortcode
        add_shortcode( 'ssmi', array( $this, 'ssmi_shortcode' ) );

        // Integrate with Visual Composer
        add_action( 'vc_before_init', array( $this, 'ssmi_visual_composer' ) );
    }

    /**
	 * Register the Simple Social Media Icons CSS, will be enqueued later
	 *
	 * @since   1.0.0
	 */
    public function register_scripts() {

		wp_register_style( 'ssmi-fontawesome-css', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );
		wp_register_style( 'ssmi-css', plugin_dir_url( __FILE__ ) . '/css/simple-social-media-icons.css' );
    }

    /**
     * Register the widget.
     *
     * @since  1.0.0
     */
    public function register_widget() {

        register_widget( 'Simple_Social_Media_Icons' );
    }

    /**
     * Do necessary initialization actions.
     *
     * @since  1.0.0
     */
    private function initialize() {

        // Set display name.
        $this->plugin_display_name = __( 'Simple Social Media Icons', 'simple-social-media-icons' );

        // Set options name.
        $this->option_name = 'simple_social_media_icons_options';

        // Get plugin options, and populate defaults as needed.
        $this->initialize_options( $this->option_name );
    }

    /**
     * Get plugin options, or initialize with default values.
     *
     * @since   1.0.0
     *
     * @param   string  The unique slug for the array containing the global plugin options
     * @return  array   The array of global options for the plugin
     */
    private function initialize_options( $option_name ) {

        $this->options = get_option( $option_name );

        // Initialize the plugin options with defaults if they're not set.
        if ( empty( $this->options ) ) {
            update_option( $option_name, $this->option_defaults );
        }
    }

    /**
     * This is not currently hooked up. Holding out for new easy translations via translate.wordpress.org
     *
     * Load plugin text domain.
     *
     * @since  1.0.0
     */
    public function load_text_domain() {
        load_plugin_textdomain( self::SLUG, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Create the plugin settings page.
     */
    public function add_settings_page() {

        add_options_page(
            $this->plugin_display_name,
            $this->plugin_display_name,
            'manage_options',
            self::SLUG,
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Output the plugin settings page contents.
     *
     * @since  1.0.0
     */
    public function create_admin_page() {
    ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php echo $this->plugin_display_name; ?></h2>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'ssmi_option_group' );
                echo $this->get_icon_styles_demo();
                do_settings_sections( self::SLUG );
                submit_button();
                echo $this->get_usage_text();
            ?>
            </form>
        </div>
    <?php
    }

    /**
     * Enqueue the CSS for the settings page.
     *
     * @since  1.0.0
     *
     * @param  string  $hook  The slug representing the current admin page
     */
    public function admin_page_enqueue( $hook ) {
    	if ( 'settings_page_simple-social-media-icons' === $hook ) {
            wp_enqueue_style( 'ssmi-fontawesome-css', '//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css' );
            wp_enqueue_style( 'ssmi-admin-css', plugin_dir_url( __FILE__ ) . 'css/simple-social-media-icons.css' );
        }
    }

    /**
     * Populate the settings page with specific settings.
     *
     * @since  1.0.0
     */
    public function add_settings() {

        register_setting(
            'ssmi_option_group', // Option group
            $this->option_name, // Option name
            array( $this, 'sanitize' ) // Sanitize
        );
        add_settings_section(
            'ssmi_settings_section_primary', // ID
            __( 'Settings', 'simple-social-media-icons' ), // Title
            null, // Callback
            self::SLUG // Page
        );
        add_settings_field(
            'icon_style', // ID
            __( 'Default Icon Style', 'simple-social-media-icons' ), // Title
            array( $this, 'icon_style_callback' ), // Callback
            self::SLUG, // Page
            'ssmi_settings_section_primary', // Section
            array(
            	'id'			=> 'icon_style',
            	'description'	=> __( 'Select the style of the icons', 'simple-social-media-icons' ),
            	'icon_style'    => array( '1', '2', '3', '4' ),
            )
		);
		foreach ( $this->social_networks as $social_network ) {
			add_settings_field(
				'include_' . $social_network->id . '_icon',
				sprintf( __( 'Include %s Icon', 'simple-social-media-icons' ), $social_network->name ),
				array( $this, 'checkbox_callback' ),
				self::SLUG,
				'ssmi_settings_section_primary',
				array(
					'id' => 'include_' . $social_network->id . '_icon',
					'description' => '',
				)
			);
		}
    }

	/**
     * Output a <select> for the icon style.
     *
     * @since  1.0.0
     *
     * @param  array  $sizes  All of the available image sizes
     */
    public function icon_style_callback( $args ) {

    	$option_name = esc_attr( $this->option_name ) . '[' . $args['id'] . ']';

    	$icon_styles = $args['icon_style'];

        // Output the <select> element.
        printf( '<select id="icon_style" name="%s">', $option_name );

        foreach ( $icon_styles as $icon_style ) {

        	printf(
                '<option value="%s" %s>%s</option>',
                esc_attr( $icon_style ),
                selected( $icon_style, $this->options['icon_style'], false ),
                $icon_style
            );
        }

        echo '</select>';

        echo '<p class="description">' . $args['description'] . '</p>';

    }

    /**
     * Output a checkbox setting.
     *
     * @since  0.10.0
     */
    public function checkbox_callback( $args ) {
        $option_name = esc_attr( $this->option_name ) . '[' . $args['id'] . ']';
        $option_value = isset( $this->options[ $args['id'] ] ) ? $this->options[ $args['id'] ] : '';
        printf(
            '<label for="%s"><input type="checkbox" value="1" id="%s" name="%s" %s/> %s</label>',
            $args['id'],
            $args['id'],
            $option_name,
            checked( 1, $option_value, false ),
            $args['description']
        );
    }

    /**
     * Generate the icon style demos
     *
     * @since   1.0.0
     *
     * @return  string  $output  The HTML for the icon style demos
     */
    public function get_icon_styles_demo() {

        $output = '<table class="form-table ssmi-style-demo-table">';
			$output .= '<tbody>';
				$output .= '<tr>';
					$output .= '<th scope="row">' . __( 'Icon Styles', 'simple-social-media-icons' ) . '</th>';
					$output .= '<td>';
						for ( $i = 1; $i <= 4; $i++ ) {
							$output .= '<p><strong>' . sprintf( __( 'Style %s', 'simple-social-media-icons' ), $i ) . '</strong></p>';
							$output .= '<p class="ssmi-icon-row icon-style-' . $i . '">';
								foreach ( $this->social_networks as $social_network ) {
									$output .= '<a href="" onclick="return false" class="ssmi-icon-link">';
										$output .= '<i class="fa fa-' . $social_network->fa_code . ' fa-fw ssmi-icon"></i>';
									$output .= '</a>';
								}
							$output .= '</p>';
							if ( 4 != $i ) {
								$output .= '<br>';
							}
						}
					$output .= '</td>';
				$output .= '</tr>';
			$output .= '</tbody>';
		$output .= '</table>';

        return $output;
    }

    /**
     * Generate the admin instructions/usage text.
	 *
     * @since   1.0.0
     *
     * @return  string  Usage text
     */
    public function get_usage_text() {
    	$usage_text = '<h3>' . __( 'Widget Usage', 'simple-social-media-icons' ) . '</h3>';
		$usage_text .= '<p>' . sprintf( __( 'Head to the <a href="%1$s">Widgets Page</a> or the <a href="%2$s">Customizer</a> and add the "Simple Social Media Icons" widget to one of your theme\'s widget areas.', 'simple-social-media-icons' ), admin_url( 'widgets.php' ), admin_url( 'customize.php' ) ) . '</p>';
		$usage_text .= '<p>' . __( 'Paste in the links to the social media profile pages you want to include.', 'simple-social-media-icons' ) . '</p>';
		$usage_text .= '<p>' . __( 'The widget will show on the front end as a row of icon links in the selected style.', 'simple-social-media-icons' ) . '</p>';
		$usage_text .= '<h3>' . __( 'Shortcode Usage', 'simple-social-media-icons' ) . '</h3>';
		$usage_text .= '<p>' . __( 'The shortcode works just like the widget and provides you with the same options:', 'simple-social-media-icons' ) . '</p>';
		$usage_text .= '<p><strong>[ssmi default_background_color="" default_icon_color="" ';
			foreach ( $this->social_networks as $social_network ) {
				$usage_text .= $social_network->id . '_link="" ' . $social_network->id . '_background_color="" ' . $social_network->id . '_icon_color=""';
			}
		$usage_text .= 'icon_style=""]</strong></p>';
		$usage_text .= '<p>' . __( 'Simply fill in the link for each icon to make it appear, like so:', 'simple-social-media-icons' ) . '</p>';
		$usage_text .= '<p><strong>[ssmi facebook_link="https://www.facebook.com/Google" facebook_background_color="#3b5998" twitter_link="https://twitter.com/google" icon_style="4"]</strong></p>';
		$usage_text .= '<p>' . __( 'The icon_style option accepts the number of any of the icon styles, from 1 to 4.', 'simple-social-media-icons' ) . '</p>';

        return '<div class="ssmi-usage-text">' . __( $usage_text, 'simple-social-media-icons' ) . '</div>';
    }

    /**
     * Sanitize each settings field as needed.
     *
     * @since   1.0.0
     *
     * @param   array  $input  Contains all settings fields as array keys
     * @return  array  $new_input  Contains all the sanitized settings
     */
    public function sanitize( $options ) {

        // Currently no need to sanitize because all options are either dropdowns or checkboxes
        $sanitized_options = $options;

        return $sanitized_options;
    }

    /**
     * Returns the style attribute on the <i> icon to implement custom colors.
     *
     * @since   1.2.0
     *
     * @param   string  $style  The icon style.
     * @param  string  $default_background_color  The background color for all social networks.
     * @param  string  $this_background_color  The background color for this social networks.
	 * @param  string  $default_icon_color  The icon color for all social networks.
     * @param  string  $this_icon_color  The icon color for this social networks.
     * @return  string  $style  The style attributo for <i>.
     */
    public function custom_color( $style, $default_background_color, $this_background_color, $default_icon_color, $this_icon_color ) {
        if ( ! $default_background_color && ! $this_background_color && ! $default_icon_color && ! $this_icon_color ) {
            return '';
		}

		$return = '';

		$background_color = false;
		if ( 1 != $style ) {
			if ( $default_background_color ) {
				$background_color = $default_background_color;
			}
			if ( $this_background_color ) {
				$background_color = $this_background_color;
			}
			if ( $background_color ) {
				$return .= ' background: ' . $background_color . '; ';
			}
		}

		$icon_color = false;
		if ( $default_icon_color ) {
			$icon_color = $default_icon_color;
		}
		if ( $this_icon_color ) {
			$icon_color = $this_icon_color;
		}
		if ( $icon_color ) {
			$return .= ' color: ' . $icon_color . '; ';
		}

        return ( $return ) ? ' style="' . $return . '" ' : '';
    }

    /**
     * [ssmi] shortcode
     *
     * Also used to render the widget
     *
     * @since   1.0.0
     *
     * @param   array  $atts  Attributes passed to the shortcode (possibly from the widget)
     * @return  string  $output  HTML for the icon row
     */
    public function ssmi_shortcode( $atts ) {
		$defaults = array(
			'default_background_color'     => '',
			'default_icon_color'     => '',
            'icon_style'        => 'default',
            'is_widget'         => 'false',
		);
		foreach ( $this->social_networks as $social_network ) {
			$defaults[ $social_network->id . '_link' ] = '';
			$defaults[ $social_network->id . '_background_color' ] = '';
			$defaults[ $social_network->id . '_icon_color' ] = '';
		}
        $a = shortcode_atts( $defaults, $atts );

        // Enqueue the JS and CSS
        wp_enqueue_style( 'ssmi-fontawesome-css' );
        wp_enqueue_style( 'ssmi-css' );

        // Set up variables to match the widget
        $icon_style                 = ( $a['icon_style'] == 'default' ) ? $this->options['icon_style'] : $a['icon_style'];
        $is_widget                  = $a['is_widget'];

        $output = '<div class="simple-social-media-icons ';

        // Add a class to the wrapper to indicate whether we're rendering the widget or the shortcode
        $output .= ( $is_widget === 'true' ) ? 'ssmi-widget' : 'ssmi-shortcode' ;

        $output .= '">';

        // Add a class to the icon row wrapper to indicate the icon style
        $output .= '<div class="ssmi-icon-row icon-style-' . $icon_style . '">';

		foreach ( $this->social_networks as $social_network ) {
			if ( ! $a[ $social_network->id . '_link' ] == '' && isset( $this->options[ 'include_' . $social_network->id . '_icon' ] ) ) {
				$output .= '<a href="' . esc_url( $a[ $social_network->id . '_link' ] ) . '" class="ssmi-icon-link" target="_blank">' .
					'<i ' . $this->custom_color( $icon_style, $a['default_background_color'], $a[ $social_network->id . '_background_color' ], $a['default_icon_color'], $a[ $social_network->id . '_icon_color' ] ) . ' class="fa fa-' . $social_network->fa_code . ' fa-fw ssmi-icon"></i>' .
					'<span class="screen-reader-text">' . $social_network->name . '</span>' .
				'</a>';
			}
		}

        $output .= '</div></div>';

        return $output;
    }

    /**
     * Visual Composer Integration
     *
     * @since  1.0.0
     */
    public function ssmi_visual_composer() {

        $vc_params = array();

        array_push( $vc_params, array(
            'type' => 'textfield',
            'class' => '',
            'heading' => __( 'Background Color for All Social Networks', 'simple-social-media-icons' ),
            'param_name' => 'default_background_color',
            'value' => '',
            'description' => __( 'Use this field to make all icons of the same background color.', 'simple-social-media-icons' )
            )
		);
		
        array_push( $vc_params, array(
            'type' => 'textfield',
            'class' => '',
            'heading' => __( 'Icon Color for All Social Networks', 'simple-social-media-icons' ),
            'param_name' => 'default_icon_color',
            'value' => '',
            'description' => __( 'Use this field to make all icons of the same color.', 'simple-social-media-icons' )
            )
        );

		// Check which icons should be included
		foreach ( $this->social_networks as $social_network ) {
			if ( isset( $this->options[ 'include_' . $social_network->id . '_icon' ] ) ) {
				array_push( $vc_params, array(
					'type' => 'textfield',
					'class' => '',
					'heading' => sprintf( __( '%s Link', 'simple-social-media-icons' ), $social_network->name ),
					'param_name' => $social_network->id . '_link',
					'value' => '',
					'description' => sprintf( __( 'URL to your %s page', 'simple-social-media-icons' ), $social_network->name ),
				) );
				array_push( $vc_params, array(
					'type' => 'textfield',
					'class' => '',
					'heading' => sprintf( __( '%s Background Color', 'simple-social-media-icons' ), $social_network->name ),
					'param_name' => $social_network->id . '_background_color',
					'value' => '',
					'description' => sprintf( __( 'Default is %s', 'simple-social-media-icons' ), $social_network->brand_color ),
				) );
				array_push( $vc_params, array(
					'type' => 'textfield',
					'class' => '',
					'heading' => sprintf( __( '%s Icon Color', 'simple-social-media-icons' ), $social_network->name ),
					'param_name' => $social_network->id . '_icon_color',
					'value' => '',
					'description' => sprintf( __( 'Default is #FFF', 'simple-social-media-icons' ), $social_network->brand_color ),
				) );
			}
		}

        array_push( $vc_params, array(
            'type' => 'dropdown',
            'class' => '',
            'heading' => __( 'Icon Style', 'simple-social-media-icons' ),
            'param_name' => 'icon_style',
            'description' => __( 'View the ', 'simple-social-media-icons' ) . '<a href="' . admin_url( 'options-general.php?page=simple-social-media-icons' ) . '" target="_blank">plugin options page</a>' . __( ' to preview styles', 'simple-social-media-icons' ),
            'admin_label' => true,
            'value' => array(
                '0' => __( 'default', 'simple-social-media-icons' ),
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4'
            )
        ));

        if ( function_exists( 'vc_map' ) ) {
            vc_map( array(
                'name' => __( 'Simple Social Media Icons', 'simple-social-media-icons' ),
                'base' => 'ssmi',
                'class' => 'ssmi-shortcode',
                'category' => 'Social',
                'description' => __( 'A row of social media icons', 'simple-social-media-icons' ),
                'params' => $vc_params,
            ));
        }
    }

}

/**
 * Simple Social Media Icons Widget Class.
 *
 * @since   1.0.0
 */
class Simple_Social_Media_Icons extends WP_Widget {

    /**
	 * Initialize an instance of the widget.
	 *
	 * @since   1.0.0
	 */
    public function __construct() {

    	$this->plugin_url = plugins_url( '', __FILE__ );

        // Options to pass to the widget
        $this->widget_options = array(
            'classname'   => 'simple-social-media-icons',
            'description' => __( 'A row of social media icon links', 'simple-social-media-icons' ),
        );

        $this->plugin_options = get_option( 'simple_social_media_icons_options' );

        // Build the widget
    	parent::__construct(
    		'simple_social_media_icons_widget',
    		__( 'Simple Social Media Icons', 'simple-social-media-icons' ),
    		$this->widget_options
    	);
    }

    /**
	 * Build the widget settings form.
	 *
	 * @since   1.0.0
	 *
	 * @param   array  $instance  The widget options
	 */
    public function form( $instance ) {

		global $simple_social_media_icons;
		$social_networks = $simple_social_media_icons->get_social_networks();

        $defaults           = array(
                                'title' 			=> '',
								'default_background_color'			=> '',
								'default_icon_color'			=> '',
        						'icon_style' 		=> 'default'
							);
		
		foreach ( $social_networks as $social_network ) {
			$defaults[ $social_network->id . '_link' ] = '';
			$defaults[ $social_network->id . '_background_color' ] = '';
			$defaults[ $social_network->id . '_icon_color' ] = '';
		}

        $instance           = wp_parse_args( $instance, $defaults );
        $title              = $instance['title'];
		$default_background_color          = $instance['default_background_color'];
		$default_icon_color          = $instance['default_icon_color'];
        $icon_style         = $instance['icon_style'];

        ?>
        <p>
            <label for="simple_social_media_icons_title"><?php _e( 'Title', 'simple-social-media-icons' ); ?>:</label>
            <input type="text" class="widefat" id="simple_social_media_icons_title" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="simple_social_media_icons_default_background_color"><?php _e( 'Background Color for All Social Networks', 'simple-social-media-icons' ); ?>:</label>
			<input type="text" class="widefat" id="simple_social_media_icons_default_background_color" name="<?php echo $this->get_field_name( 'default_background_color' ); ?>" value="<?php echo esc_attr( $default_background_color ); ?>" />
			<i><?php _e( 'Use this field to make all icons of the same background color.', 'simple-social-media-icons' ); ?></i>
		</p>
        <p>
            <label for="simple_social_media_icons_default_icon_color"><?php _e( 'Icon Color for All Social Networks', 'simple-social-media-icons' ); ?>:</label>
			<input type="text" class="widefat" id="simple_social_media_icons_default_icon_color" name="<?php echo $this->get_field_name( 'default_icon_color' ); ?>" value="<?php echo esc_attr( $default_icon_color ); ?>" />
			<i><?php _e( 'Use this field to make all icons of the same icon color.', 'simple-social-media-icons' ); ?></i>
        </p>
		<?php
		foreach ( $social_networks as $social_network ) {
			if ( isset( $this->plugin_options[ 'include_' . $social_network->id . '_icon' ] ) ) {
				?>
				<p>
					<label for="simple_social_media_icons_<?php echo $social_network->id; ?>_link"><?php echo sprintf( __( '%s Link', 'simple-social-media-icons' ), $social_network->name ); ?>:</label>
					<input type="text" class="widefat" id="simple_social_media_icons_<?php echo $social_network->id; ?>_link" name="<?php echo $this->get_field_name( $social_network->id . '_link' ); ?>" value="<?php echo esc_url( $instance[ $social_network->id . '_link' ] ); ?>" />
				</p>
				<p>
					<label for="simple_social_media_icons_<?php echo $social_network->id; ?>_background_color"><?php echo sprintf( __( '%s Background Color', 'simple-social-media-icons' ), $social_network->name ); ?>:</label>
					<input type="text" class="widefat" id="simple_social_media_icons_<?php echo $social_network->id; ?>_background_color" name="<?php echo $this->get_field_name( $social_network->id . '_background_color' ); ?>" value="<?php echo esc_attr( $instance[ $social_network->id . '_background_color' ] ); ?>" />
					<i><?php echo sprintf( __( 'Default is %s', 'simple-social-media-icons' ), $social_network->brand_color ); ?></i>
				</p>
				<p>
					<label for="simple_social_media_icons_<?php echo $social_network->id; ?>_icon_color"><?php echo sprintf( __( '%s Icon Color', 'simple-social-media-icons' ), $social_network->name ); ?>:</label>
					<input type="text" class="widefat" id="simple_social_media_icons_<?php echo $social_network->id; ?>_icon_color" name="<?php echo $this->get_field_name( $social_network->id . '_icon_color' ); ?>" value="<?php echo esc_attr( $instance[ $social_network->id . '_icon_color' ] ); ?>" />
					<i><?php echo sprintf( __( 'Default is %s', 'simple-social-media-icons' ), '#FFF' ); ?></i>
				</p>
				<?php
			}
		}
		?>
        <p>
            <label for="simple_social_media_icons_icon_style"><?php _e( 'Icon Style', 'simple-social-media-icons' ) ?>:</label>
            <select id="simple_social_media_icons_icon_style" name="<?php echo $this->get_field_name( 'icon_style' ); ?>">
                <option<?php if ( 'default' == $instance['icon_style'] ) echo ' selected="selected"'; ?>>default</option>
                <option<?php if ( '1' == $instance['icon_style'] ) echo ' selected="selected"'; ?>>1</option>
                <option<?php if ( '2' == $instance['icon_style'] ) echo ' selected="selected"'; ?>>2</option>
                <option<?php if ( '3' == $instance['icon_style'] ) echo ' selected="selected"'; ?>>3</option>
                <option<?php if ( '4' == $instance['icon_style'] ) echo ' selected="selected"'; ?>>4</option>
            </select>
        </p>
        <p>Visit the <a href="<?php echo admin_url( 'options-general.php?page=simple-social-media-icons' ) ?>">Settings Page</a> to see the icon styles</p>
        <?php
    }

    /**
	 * Update the widget settings.
	 *
	 * @since   1.0.0
	 *
	 * @param   array  $new_instance  The new option values to be saved
	 * @param   array  $old_instance  The previous option values
	 * @return  array  $instance  The updated option values for the widget instance
	 */
    public function update( $new_instance, $old_instance ) {
		global $simple_social_media_icons;
		$social_networks = $simple_social_media_icons->get_social_networks();

        $instance                       = $old_instance;
        $instance['title']              = sanitize_text_field( $new_instance['title'] );
		$instance['default_background_color']          = sanitize_text_field( $new_instance['default_background_color'] );
		$instance['default_icon_color']          = sanitize_text_field( $new_instance['default_icon_color'] );
		foreach ( $social_networks as $social_network ) {
			$instance[ $social_network->id . '_link' ]      = sanitize_text_field( $new_instance[ $social_network->id . '_link' ] );
			$instance[ $social_network->id . '_background_color' ]      = sanitize_text_field( $new_instance[ $social_network->id . '_background_color' ] );
			$instance[ $social_network->id . '_icon_color' ]      = sanitize_text_field( $new_instance[ $social_network->id . '_icon_color' ] );
		}
        $instance['icon_style']         = $new_instance['icon_style'];

        return $instance;
    }

    /**
	 * Display the widget.
     *
	 * @since   1.0.0
	 *
	 * @param   array  $args  The arguments passed into the widget
	 * @param   array  $instance  All of the options for the widget instance
	 */
    public function widget( $args, $instance ) {
		global $simple_social_media_icons;
		$social_networks = $simple_social_media_icons->get_social_networks();

        // Handle the issue where newly initialized widgets are being previewed in the customizer but don't yet have a proper $instance
        if ( ! $instance ) {
        	$defaults = array(
                'title' 			=> '',
				'default_background_color' 		=> '',
				'default_icon_color' 		=> '',
        		'icon_style' 		=> 'default',
        		'is_widget' 		=> 'true'
			);
			foreach ( $social_networks as $social_network ) {
				$defaults[ $social_network->id . '_link' ] = '';
				$defaults[ $social_network->id . '_background_color' ] = '';
				$defaults[ $social_network->id . '_icon_color' ] = '';
			}
        	$instance           = wp_parse_args( $instance, $defaults );
        }

        $title              = apply_filters( 'widget_title', $instance['title'] );
		$default_background_color          = ( isset( $instance['default_background_color'] ) ) ? $instance['default_background_color'] : '';
		$default_icon_color          = ( isset( $instance['default_icon_color'] ) ) ? $instance['default_icon_color'] : '';
        $icon_style         = $instance['icon_style'];

		$shortcode = '[ssmi default_background_color="' . $default_background_color . '" default_icon_color="' . $default_icon_color . '" ';
		foreach ( $social_networks as $social_network ) {
			if ( isset( $instance[ $social_network->id . '_link' ] ) ) {
				$shortcode .= $social_network->id . '_link="' . $instance[ $social_network->id . '_link' ] . '" ';
			}
			if ( isset( $instance[ $social_network->id . '_background_color' ] ) ) {
				$shortcode .= $social_network->id . '_background_color="' . $instance[ $social_network->id . '_background_color' ] . '" ';
			}
			if ( isset( $instance[ $social_network->id . '_icon_color' ] ) ) {
				$shortcode .= $social_network->id . '_icon_color="' . $instance[ $social_network->id . '_icon_color' ] . '" ';
			}
		}
		$shortcode .= 'icon_style="' . $icon_style . '" is_widget="true"]';

        echo $args['before_widget'];

        if ( !empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];

        // Use the shortcode to render the widget
        echo do_shortcode( $shortcode );

        echo $args['after_widget'];
    }
}
