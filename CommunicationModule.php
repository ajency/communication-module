<?php
/**
 * Communication Module
 *
 * @package   communication-module
 * @author    Team Ajency <talktous@ajency.in>
 * @license   GPL-2.0+
 * @link      http://ajency.in
 * @copyright 7-24-2014 Ajency.in
 */

/**
 * Communication Module class.
 *
 * @package CommunicationModule
 * @author  Team Ajency <talktous@ajency.in>
 */
class CommunicationModule{
	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   0.1.0
	 *
	 * @var     string
	 */
	protected $version = "0.1.0";

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = "communication-module";

	/**
	 * Instance of this class.
	 *
	 * @since    0.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = '';

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     0.1.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action("init", array($this, "load_plugin_textdomain"));

		// Add the options page and menu item.
		add_action("admin_menu", array($this, "add_plugin_admin_menu"));

		// Load admin style sheet and JavaScript.
		add_action("admin_enqueue_scripts", array($this, "enqueue_admin_styles"));
		add_action("admin_enqueue_scripts", array($this, "enqueue_admin_scripts"));

		// Load public-facing style sheet and JavaScript.
		add_action("wp_enqueue_scripts", array($this, "enqueue_styles"));
		add_action("wp_enqueue_scripts", array($this, "enqueue_scripts"));

		// Define custom functionality. Read more about actions and filters: http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		add_action("TODO", array($this, "action_method_name"));
		add_filter("TODO", array($this, "filter_method_name"));

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn"t been set, set it now.
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    0.1.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate($network_wide) {
        
                global $wpdb;
            
                //create tables logic on plugin activation
                $communication_tbl=$wpdb->prefix."ajcm_communications";
                $communication_sql="CREATE TABLE `{$communication_tbl}` (
                               `id` int(11) NOT NULL primary key AUTO_INCREMENT,           
                               `component` varchar(75) NOT NULL,
                               `communication_type` varchar(75) NOT NULL,
                               `user_id` int(11) DEFAULT NULL,
                               `priority` varchar(25) NOT NULL,
                               `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                               `processed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
                                );";

                $communication_meta_tbl=$wpdb->prefix."ajcm_communication_meta";            
                $communication_meta_sql="CREATE TABLE `{$communication_meta_tbl}` (
                                `id` int(11) NOT NULL primary key AUTO_INCREMENT,
                                `communication_id` int(11) DEFAULT NULL,
                                `meta_key` varchar(255) NOT NULL,
                                `meta_value` longtext
                                 );";

                $reciepients_tbl=$wpdb->prefix."ajcm_recipients";            
                $reciepients_sql="CREATE TABLE `{$reciepients_tbl}` (
                                `id` int(11) NOT NULL primary key AUTO_INCREMENT,
                                `communication_id` int(11) DEFAULT NULL,
                                `user_id` int(11) DEFAULT NULL,
                                `type` varchar(25) NOT NULL,
                                `value` varchar(25) NOT NULL,
                                `thirdparty_id` int(11) DEFAULT NULL,
                                `status` varchar(25) NOT NULL
                                 );";   

                $email_preferences_tbl=$wpdb->prefix."ajcm_emailpreferences";            
                $email_preferences_sql="CREATE TABLE `{$email_preferences_tbl}` (
                                `id` int(11) NOT NULL primary key AUTO_INCREMENT,
                                `user_id` int(11) DEFAULT NULL,
                                `communication_type` varchar(255) NOT NULL,
                                `preference` varchar(25) NOT NULL
                                 );";   


                //reference to upgrade.php file
                require_once(ABSPATH.'wp-admin/includes/upgrade.php');
                dbDelta($communication_sql);
                dbDelta($communication_meta_sql);
                dbDelta($reciepients_sql);
                dbDelta($email_preferences_sql);
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    0.1.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate($network_wide) {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters("plugin_locale", get_locale(), $domain);

		load_textdomain($domain, WP_LANG_DIR . "/" . $domain . "/" . $domain . "-" . $locale . ".mo");
		load_plugin_textdomain($domain, false, dirname(plugin_basename(__FILE__)) . "/lang/");
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     0.1.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if (!isset($this->plugin_screen_hook_suffix)) {
			return;
		}

		$screen = get_current_screen();
		if ($screen->id == $this->plugin_screen_hook_suffix) {
			wp_enqueue_style($this->plugin_slug . "-admin-styles", plugins_url("css/admin.css", __FILE__), array(),
				$this->version);
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     0.1.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if (!isset($this->plugin_screen_hook_suffix)) {
			return;
		}

		$screen = get_current_screen();
		if ($screen->id == $this->plugin_screen_hook_suffix) {
			wp_enqueue_script($this->plugin_slug . "-admin-script", plugins_url("js/communication-module-admin.js", __FILE__),
				array("jquery"), $this->version);
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style($this->plugin_slug . "-plugin-styles", plugins_url("css/public.css", __FILE__), array(),
			$this->version);
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script($this->plugin_slug . "-plugin-script", plugins_url("js/public.js", __FILE__), array("jquery"),
			$this->version);
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1.0
	 */
	public function add_plugin_admin_menu() {
		$this->plugin_screen_hook_suffix = add_plugins_page(__("Communication Module - Administration", $this->plugin_slug),
			__("Communication Module", $this->plugin_slug), "read", $this->plugin_slug, array($this, "display_plugin_admin_page"));
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.1.0
	 */
	public function display_plugin_admin_page() {
		include_once("views/admin.php");
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        WordPress Actions: http://codex.wordpress.org/Plugin_API#Actions
	 *        Action Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    0.1.0
	 */
	public function action_method_name() {
		// TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        WordPress Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Filter Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    0.1.0
	 */
	public function filter_method_name() {
		// TODO: Define your filter hook callback here
	}
        
        /*
         * add a communication
         *    @param array $args {
         *     An array of arguments.
         *     @type int|bool $id  Default: false.
         *     @type string $component 
         *     @type string $communication_type
         *     @type int $user_id 
         *     @type string $priority (high,medium,low)
         *     @type datetime $created
         *     @type datetime $processed
         *     }
         * @return int|false comm_id on successful add.
         */
        public function communication_add ( $args = '' ) {
            global $wpdb;
            $defaults = array(
                    'id'                  => false,
                    'component'           => '',    
                    'communication_type'  => '',                  
                    'user_id'             => '',    
                    'priority'            => '',
                    'created'             => current_time( 'mysql', true ),
                    'processed'           => ''
            );
            $params = wp_parse_args( $args, $defaults );
            extract( $params, EXTR_SKIP );
            
            if(!$id){
                $q = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}ajcm_communications ( component, communication_type, "
                . "user_id, priority, created, processed) VALUES ( %s, %s, %d, %s, %s, %s)", 
                        $component, $communication_type, $user_id, $priority, $created, $processed);
                        if ( false === $wpdb->query( $q ) )
                            return false;
                        
                $comm_id = $wpdb->insert_id;
                return $comm_id;
            }
            
        }
        
         /*
         * add a communication meta
         * @param int $comm_id
         * @param string $meta_key 
         * @param string $meta_value 
         * 
         * @return int|false recipient_id on successful add.
         */       
        public function communication_meta_add ( $comm_id, $meta_key ,$meta_value ) {
            global $wpdb;
            
            if (!$meta_key )
                return false;
            
            if ( !$comm_id = absint($comm_id) )
                return false;
            
            	$meta_key = wp_unslash($meta_key);
                $meta_value = wp_unslash($meta_value);
                
                $meta_value = maybe_serialize( $meta_value );
                
                $table = $wpdb->prefix."ajcm_communication_meta";
                $result = $wpdb->insert( $table, array(
                                            'communication_id' => $comm_id,
                                            'meta_key' => $meta_key,
                                            'meta_value' => $meta_value
                                        ) );

                if ( ! $result )
                    return false;

                $mid = (int) $wpdb->insert_id;
                return $mid;
        }
 
         /*
         * add a communication recipient
         * @param int $comm_id
         * @param array $args {
         *     An array of arguments.
         *     @type int|bool $user_id.
         *     @type string $type (email|phone) 
         *     @type string $value values of $type
         *     @type int $thirdparty_id 
         *     @type string $status
         *     }
         * @return int|false recipient_id on successful add.
         */       
        public function recipient_add ( $comm_id ,$args = '' ) {
            global $wpdb;
            
              if ( !$comm_id = absint($comm_id) )
                return false;
              
            $defaults = array(
                    'id'                  => false,
                    'user_id'             => '',    
                    'type'                => '',                  
                    'value'               => '',    
                    'thirdparty_id'       => '',
                    'status'              => ''
            );
            
            $params = wp_parse_args( $args, $defaults );
            extract( $params, EXTR_SKIP );
            
            if(!$id){
                $q = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}ajcm_recipients ( communication_id, user_id, "
                . "type, value, thirdparty_id, status) VALUES ( %d, %d, %s, %s, %d, %s)", 
                        $comm_id, $user_id, $type, $value, $thirdparty_id, $status);
                        if ( false === $wpdb->query( $q ) )
                            return false;
                        
                $comm_id = $wpdb->insert_id;
                return $recipient_id;
            }            
        }

}