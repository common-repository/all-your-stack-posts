<?php

class B5F_AYSP_Metabox
{
	private $plugin_path;
	private $plugin_url;
    private $se_sort_orders;
    private $se_post_types;
	
	public function __construct( $path, $url ) 
	{
		$this->plugin_path = $path;
		$this->plugin_url = $url;
        $this->se_sort_orders = array(
			'asc'       => __('Ascending', 'aysp'),
			'desc'     => __('Descending', 'aysp')
		);
        $this->se_post_types = array(
			'answers'       => __('Answers', 'aysp'),
			'questions'     => __('Questions', 'aysp'),
			'favorites'     => __('Favorites', 'aysp')
		);

		add_action( 'add_meta_boxes', array( $this, 'add_custom_box' ) );
		add_action( 'save_post', array( $this, 'save_postdata' ) );
		add_action( 'admin_head-post.php', array( $this, 'script_enqueuer' ) );
	}



	/* Adds a box to the main column on the Post and Page edit screens */
	public function add_custom_box() 
	{
		add_meta_box(
			'b5f_se_metabox_qas_id',
			__( "All user posts in a Stack site", 'aysp' ), 
			array( $this, 'inner_qas_box' ),
			'page',
			'side'
		);
	}


	/**
     * Prints the Q&A box content 
     */
	public function inner_qas_box($post)
	{
		wp_nonce_field( plugin_basename( __FILE__ ), 'b5f_se_metabox_nonce' );
		
		# https://github.com/marghoobsuleman/ms-Dropdown
		# combobox_output.php : added this to GetHTML()
		# $meta_icon = ' data-image="'. $item['favicon_url'] .'"';
		wp_enqueue_script( 'dd_js', $this->plugin_url . 'js/jquery.dd.min.js', array('jquery'));
		wp_enqueue_script( 'dd_fire', $this->plugin_url . 'js/dd-fire.js', array('jquery'));
		wp_enqueue_style( 'dd_style', $this->plugin_url . 'css/dd.css' );

		require_once $this->plugin_path.'includes/config-stackphp.php';
		require_once $this->plugin_path.'includes/stackphp/output_helper.php';
		
		# Sites list
		$se_site_saved = get_post_meta( $post->ID, 'se_site', true);
		if( !$se_site_saved )
			$se_site_saved = 'stackoverflow';
		
		$combo = OutputHelper::CreateCombobox( API::Sites(), 'se_site' );
		$site_html = $combo->FetchMultiple()->SetIndices('name', 'api_site_parameter')->SetCurrentSelection( $se_site_saved )->GetHTML();
		
		printf(
				'<p><strong>%s</strong><br />%s</p>',
				__( 'Select site', 'aysp' ),
				$site_html
		);
		
		# Post types
		$se_post_type_saved = get_post_meta( $post->ID, 'se_post_type', true);
		if( !$se_post_type_saved )
			$se_post_type_saved = 'questions';
		printf(
				'<p><label for="se_post_type" class="mbox-label"><strong>%s</strong></label> <select name="se_post_type" id="se_post_type">',
				__( 'Type of posts', 'aysp' )
				
		);
		
		foreach ( $this->se_post_types as $key => $label ) 
		{
			printf(
				'<option value="%s" %s> %s</option>',
				esc_attr($key),
				selected( $se_post_type_saved, $key, false),
				esc_html($label)
			);
		}
		echo '</select></p>';
		
		# Sorting
		
		$se_sort_order_saved = get_post_meta( $post->ID, 'se_sort_order', true);
		if( !$se_sort_order_saved )
			$se_sort_order_saved = 'asc';
		printf(
				'<p><label for="se_sort_order" class="mbox-label"><strong>%s</strong></label> <select name="se_sort_order" id="se_sort_order">',
				__( 'Sorting', 'aysp' )
		);
		foreach ( $this->se_sort_orders as $key => $label ) 
		{
			printf(
				'<option value="%s" %s> %s</option>',
				esc_attr($key),
				selected( $se_sort_order_saved, $key, false),
				esc_html($label)
			);
		}
		echo '</select></p>';
		
		# User ID
		$se_user_id_saved = get_post_meta( $post->ID, 'se_user_id', true);
		if( !$se_user_id_saved )
			$se_user_id_saved = '';
		printf(
				"<p><label for='se_user_id' class='mbox-label'><strong>%s</strong></label> <input type='text' size='6' name='se_user_id' id='se_user_id' value='%s' /></p>",
				__( 'User ID', 'aysp' ),
				esc_attr( $se_user_id_saved )
		);
		
		# Posts per page
		$se_per_page = get_post_meta( $post->ID, 'se_per_page', true);
		printf(
				"<p><label for='se_per_page' class='mbox-label'><strong>%s</strong></label> <input type='text' size='6' name='se_per_page' id='se_per_page' value='%s' /></p>",
				__( 'Posts per page', 'aysp' ),
				esc_attr( $se_per_page )
		);
		
		# Cache
		$se_cached = get_post_meta( $post->ID, 'se_cached', true);
		printf(
			'<p><label for="se_cached" class="mbox-label"><strong>%s</strong></label> <input name="se_cached" id="se_cached" type="checkbox" %s />',
				__( 'Cache results', 'aysp' ),
			checked( $se_cached, 'on', false)
		);

		# Referrer ID
		$se_referrer_id_saved = get_post_meta( $post->ID, 'se_referrer_id', true);
		if( !$se_referrer_id_saved )
			$se_referrer_id_saved = '';
		printf(
				"<hr /><p><label for='se_referrer_id' class=''><strong>%s</strong><br />%s</label><br /> <input type='text' size='6' name='se_referrer_id' id='se_referrer_id' value='%s' /></p>",
				__( 'Referral ID', 'aysp' ),
				__( "enter a <em>user ID</em> to use as <a href='http://blog.stackoverflow.com/2010/09/announcer-booster-and-publicist-badges/' target='_blank'>referral link</a>,<br />leave empty for no referrer", 'aysp' ),
				esc_attr( $se_referrer_id_saved )
		);
    }

    
	/**
     * Save post action 
     */
	public function save_postdata( $post_id ) 
	{
        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
              || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) 
            return;

        if ( !isset( $_POST['b5f_se_metabox_nonce'] ) ||  !wp_verify_nonce( $_POST['b5f_se_metabox_nonce'], plugin_basename( __FILE__ ) ) )
            return;

        if ( isset($_POST['se_site']) )
              update_post_meta( 
                      $post_id, 
                      'se_site', 
                      esc_sql( $_POST['se_site'] )
              );

        if ( isset($_POST['se_favorites_site']) )
              update_post_meta( 
                      $post_id, 
                      'se_favorites_site', 
                      esc_sql( $_POST['se_favorites_site'] )
              );

        if ( isset($_POST['se_post_type']) )
              update_post_meta( 
                      $post_id, 
                      'se_post_type', 
                      esc_sql( $_POST['se_post_type'] )
              );

        if ( isset($_POST['se_sort_order']) )
              update_post_meta( 
                      $post_id, 
                      'se_sort_order', 
                      esc_sql( $_POST['se_sort_order'] )
              );

        if ( isset($_POST['se_user_id']) && $_POST['se_user_id'] != "" )
              update_post_meta( 
                      $post_id, 
                      'se_user_id', 
                      intval( stripslashes( strip_tags( $_POST['se_user_id'] ) ) ) 
              );
        if ( isset($_POST['se_referrer_id']) && $_POST['se_referrer_id'] != "" )
              update_post_meta( 
                      $post_id, 
                      'se_referrer_id', 
                      intval( stripslashes( strip_tags( $_POST['se_referrer_id'] ) ) ) 
              );
        else 
            delete_post_meta( $post_id, 'se_referrer_id' );

        if ( isset($_POST['se_cached']) && $_POST['se_cached'] != "" )
              update_post_meta( 
                      $post_id, 
                      'se_cached', 
                      esc_sql( $_POST['se_cached'] )
              );
        else
            delete_post_meta( $post_id, 'se_cached' );

        if ( isset($_POST['se_per_page']) && $_POST['se_per_page'] != "" )
        {
            $total = intval( stripslashes( strip_tags( $_POST['se_per_page'] ) ) );
            if( $total > 100 )
                $total = 100;
              update_post_meta( 
                      $post_id, 
                      'se_per_page', 
                       $total
              );
        }

	}

    
	public function script_enqueuer() 
	{
		global $typenow;

		if( 'page' != $typenow ) 
			return;

		echo <<<HTML
<script type="text/javascript">
jQuery(document).ready( function($) {

	/**
	 * Adjust visibility of the meta box at startup
	*/
	if($('#page_template').val() == 'template-stackapp.php') {
		// show the meta box
		$('#b5f_se_metabox_qas_id').show();
		$("form#adv-settings label[for='b5f_se_metabox_qas_id-hide']").show();
	} else {
		// hide your meta box
		$('#b5f_se_metabox_qas_id').hide();
		$("form#adv-settings label[for='b5f_se_metabox_qas_id-hide']").hide();
	}

	/**
	 * Live adjustment of the meta box visibility
	*/
	$('#page_template').live('change', function(){
        if($(this).val() == 'template-stackapp.php') {
			// show the meta box
			$('#b5f_se_metabox_qas_id').show();
			$("form#adv-settings label[for='b5f_se_metabox_qas_id-hide']").show();
		} else {
			// hide your meta box
			$('#b5f_se_metabox_qas_id').hide();
			$("form#adv-settings label[for='b5f_se_metabox_qas_id-hide']").hide();
		}
	});					
});    
</script>
HTML;
	}

	/**
	 * Zero, one or more votes
	 * @param string $score
	 * @return string
	 */
	public function get_score( $score, $prefix='', $suffix='' )
	{
		switch( $score )
		{
			case '0':
			null:
				$score = '';
			break;
			case '1':
				$score = $prefix.'1 vote'.$suffix;
			break;
			default:
				$score = $prefix . $score . ' votes'.$suffix;
			break;
		}
		return $score;
	}
	
}