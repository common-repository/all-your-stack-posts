<?php
/* Plugin Name: All Your Stack Posts
 * Description: Grab Questions or Answers from a given user in a given Stack Exchange site and display them in a simple page ready to print (using your system capabilities).
 * Plugin URI: http://wordpress.org/plugins/all-your-stack-posts
 * Version:     1.1
 * Author:      Rodolfo Buaiz
 * Author URI:  http://brasofilo.com
 * Text Domain: aysp
 * Domain Path: /languages
 * License: GPLv2 or later
 */

/*
All Stack Posts
Copyright (C) 2013  Rodolfo Buaiz

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
 
add_action(
	'plugins_loaded',
	array ( B5F_All_Your_Stack_Posts::get_instance(), 'plugin_setup' )
);
register_activation_hook( 
		__FILE__, 
		array( 'B5F_All_Your_Stack_Posts', 'copy_theme_template' ) 
);
register_deactivation_hook( 
		__FILE__, 
		array( 'B5F_All_Your_Stack_Posts', 'delete_theme_template' ) 
);

class B5F_All_Your_Stack_Posts
{
	protected static $instance = NULL;
	public $plugin_url = NULL;
	public $plugin_path = NULL;
	public $plugin_slug = NULL;
	public $locale_slug = NULL;
	public $frontend;
	
	public static function get_instance()
	{
		NULL === self::$instance and self::$instance = new self;
		return self::$instance;
	}

	public function plugin_setup()
	{
		$this->plugin_url    = plugins_url( '/', __FILE__ );
		$this->plugin_path   = plugin_dir_path( __FILE__ );
		$this->plugin_slug   = plugin_basename(__FILE__);
		$this->locale_slug = dirname( plugin_basename( __FILE__ ) );

		include_once('includes/class-aysp-metabox.php');
		new B5F_AYSP_Metabox( $this->plugin_path, $this->plugin_url );
		
		# Description translation
		$desc = __( 'Grab Questions or Answers from a given user in a given Stack Exchange site and display them in a simple page ready to print (using your system capabilities).', 'aysp' );
		
		# Load translation files
		$this->plugin_locale( 'aysp' );

		if( !is_admin() )
		{
			include_once('includes/class-aysp-frontend.php');
			$this->frontend = new B5F_AYSP_Frontend( $this->plugin_path, $this->plugin_url );
		}
		
		add_filter( 'upgrader_post_install', array( $this, 'refresh_template' ), 10, 3 );
        add_filter( 'plugin_row_meta', array( $this, 'donate_link' ), 10, 4 );
        add_action( 'switch_theme', array( $this, 'switching_theme' ), 10, 2 );
    }
	
	
	public function __construct() {}
	

	public function refresh_template(  $true, $hook_extra, $result )
	{
		if( isset( $hook_extra['plugin'] ) && $this->plugin_slug == $hook_extra['plugin'] )
		{
			self::delete_theme_template();
			self::copy_theme_template();
		}
		return $true; 
	}
	
	
    public function switching_theme( $new_name, $new_theme )
    {
        $old_theme = get_option( 'theme_switched' );
        
        $template_path_app = get_theme_root( $old_theme ) . "/$old_theme/template-stackapp.php";    
		if( file_exists( $template_path_app ) )
			unlink( $template_path_app );
        
        self::copy_theme_template();
    }
	/**
	 * Remove the plugin template from old theme directory after theme switch
	 * 
	 * @return boolean 
	 */
	public static function delete_old_theme_template()
	{
		if( file_exists( self::get_template_destination( 'stackapp' ) ) )
			return unlink( self::get_template_destination( 'stackapp' ) );

		return true;
	}
	
	/**
	 * Call copy template function on Plugin Activation
	 * 
	 */
	public static function copy_theme_template()
	{ 
		// Get source and destination for copying from the plugin to the theme directory
		$destination = self::get_template_destination( 'stackapp' );
		$source = self::get_template_source( 'stackapp' );

        // Copy the template file from the plugin to the destination
		self::copy_page_template( $source, $destination );
	}
	
	/**
	 * Remove the plugin template from theme directory
	 * 
	 * Returns TRUE if the file not exists, or if the removal is successful
	 * Returns FALSE if the file exists, but was not removed
	 * 
	 * @return boolean 
	 */
	public static function delete_theme_template()
	{
        if( file_exists( self::get_template_destination( 'stackapp' ) ) )
			return unlink( self::get_template_destination( 'stackapp' ) );

		return true;
	}
	
	/**
	 * The destination to the plugin directory relative to the currently active theme
	 * 
	 * From page-template-plugin
	 * 
	 * @return string 
	 */
	private static function get_template_destination( $suffix ) 
	{
		return get_stylesheet_directory() . "/template-$suffix.php";
	} 

	/**
	 * The path to the template file relative to the plugin.
	 * 
	 * From page-template-plugin
	 * 
	 * @return string 
	 */
	private static function get_template_source( $suffix ) 
	{
		return dirname( __FILE__ ) . "/includes/template-$suffix.php";
	} 
	
    
    
    /**
     * Add donate link to plugin description in /wp-admin/plugins.php
     * 
     * @param array $plugin_meta
     * @param string $plugin_file
     * @param string $plugin_data
     * @param string $status
     * @return array
     */
    public function donate_link( $plugin_meta, $plugin_file, $plugin_data, $status ) 
	{
		if( plugin_basename( __FILE__ ) == $plugin_file )
			$plugin_meta[] = '&hearts; <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=JNJXKWBYM9JP6&lc=ES&item_name=All%20Your%20Stack%20Posts%20%3a%20Rodolfo%20Buaiz&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted">Buy me a beer :o)</a>';
		return $plugin_meta;
	}

    
    /**
	 * Translation
	 *
	 * @uses    load_plugin_textdomain, plugin_basename
	 * @since   2.0.0
	 * @return  void
	 */
	public function plugin_locale( $domain )
	{
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		$mo_name = $domain . '-' . $locale . '.mo';
		$mo_path = WP_LANG_DIR . '/plugins/' . $this->locale_slug . '/' . $mo_name;

		load_textdomain( $domain, $mo_path );
		load_plugin_textdomain( $domain, FALSE, $this->locale_slug . '/languages' );
	}
	
	/**
	 * Does the actual copy from plugin to template directory
	 * 
	 * From https://github.com/tommcfarlin/page-template-example/
	 *
	 * @param string $source
	 * @param string $destination
	 */
	private static function copy_page_template( $source, $destination )	
	{
		// Check if template already exists. If so don't copy it; otherwise, copy if
		if( ! file_exists( $destination ) ) 
		{
			// Create an empty version of the file
			touch( $destination );
			
			// Read the source file starting from the beginning of the file
			if( null != ( $handle = @fopen( $source, 'r' ) ) ) 
			{
				// Read the contents of the file into a string. 
				// Read up to the length of the source file
				if( null != ( $content = fread( $handle, filesize( $source ) ) ) ) 
				{
					// Relinquish the resource
					fclose( $handle );
				} 
			} 
						
			// Now open the file for reading and writing
			if( null != ( $handle = @fopen( $destination, 'r+' ) ) ) 
			{
				// Attempt to write the contents of the string
				if( null != fwrite( $handle, $content, strlen( $content ) ) ) 
				{
					// Relinquish the resource
					fclose( $handle );
				} 
			} 
		} 
	} 	
}