<?php
/*
Plugin Name: MailDepot
Plugin URI: https://a1local.com.au/
Description: MailDepot is created to help increase email signups by letting visitors who want to download a file (PDF, XLS, XLSX, PPT, PPTX, TXT, ZIP, RAR) from your website subscribe to an email list first.  
Author: A1 Local
Version: 1
Author URI: https://a1local.com.au
License: GPLv2
*/

function maildepot_scripts_with_jquery(){
    
    wp_register_script( 'maildepotmailchimp-script', plugins_url( '/js/maildepotmailchimp.js', __FILE__ ), array( 'jquery' ) );
    
    wp_localize_script( 'maildepotmailchimp-script', 'maildepot', array(
        "ajaxurl"   =>  admin_url('admin-ajax.php'),
        "mailchimp_nnc"   =>  wp_create_nonce('mailchimp_subscribe'),
        "download_ref_nnc"   =>  wp_create_nonce('download_referrer')
    ) );
    wp_enqueue_script( 'maildepotmailchimp-script' );
    
}
add_action( 'wp_enqueue_scripts', 'maildepot_scripts_with_jquery' );



function maildepot_styles_with_the_lot(){
    wp_register_style( 'maildepotmailchimp-style', plugins_url( '/css/maildepotmailchimp.css', __FILE__ ), array(), '20120208', 'all' );
    wp_enqueue_style( 'maildepotmailchimp-style' );
}
add_action( 'wp_enqueue_scripts', 'maildepot_styles_with_the_lot' );




 
 
 
/**
 * custom option and settings
 */
function maildepotmailchimp_settings_init() {
    // register a new setting for "maildepotmailchimp" page
    register_setting( 'maildepotmailchimp', 'maildepotmailchimp_options' );
    
    // register a new section in the "maildepotmailchimp" page
    add_settings_section(
        'maildepotmailchimp_section_developers',
        __( 'MailChimp API.', 'maildepotmailchimp' ),
        'maildepotmailchimp_section_developers_cb',
        'maildepotmailchimp'
    );
    
    // register a new field in the "maildepotmailchimp_section_developers" section, inside the "maildepotmailchimp" page
    add_settings_field(
        'mailchimp_api_key', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __( 'API KEY', 'maildepotmailchimp' ),
        'maildepot_mailchimp_api_key_cb',
        'maildepotmailchimp',
        'maildepotmailchimp_section_developers',
        [
            'label_for' => 'mailchimp_api_key',
            'class' => 'maildepotmailchimp_row',
            'maildepotmailchimp_custom_data' => 'custom',
        ]
    );
    
    add_settings_field(
        'mailchimp_list_id', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __( 'List ID', 'maildepotmailchimp' ),
        'maildepot_mailchimp_list_id_cb',
        'maildepotmailchimp',
        'maildepotmailchimp_section_developers',
        [
            'label_for' => 'mailchimp_list_id',
            'class' => 'maildepotmailchimp_row',
            'maildepotmailchimp_custom_data' => 'custom',
        ]
    );
    
    add_settings_field(
        'mailchimp_link_text', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __( 'Default link text', 'maildepotmailchimp' ),
        'maildepot_mailchimp_link_text_cb',
        'maildepotmailchimp',
        'maildepotmailchimp_section_developers',
        [
            'label_for' => 'mailchimp_link_text',
            'class' => 'maildepotmailchimp_row',
            'maildepotmailchimp_custom_data' => 'custom',
        ]
    ); 
}
add_action( 'admin_init', 'maildepotmailchimp_settings_init' );
 
function maildepotmailchimp_section_developers_cb( $args ) {
 ?><p><span class="wysiwyg-font-size-large"><strong>How to Find or Generate Your Mailchimp API Key</strong></span></p>
<p>In Mailchimp, users with Manager <a href="http://kb.mailchimp.com/accounts/multi-user/manage-user-permissions" target="_blank">permissions</a> can generate and view their own API keys. Users with Admin permissions can also see API keys for other account users. </p> 
	<ol>
		<li>Click your profile name to expand the Account Panel, and choose <strong><em>Account</em></strong>.</li>
		<li>Click the <strong><em>Extras</em></strong> drop-down menu and choose <strong><em>API keys</em></strong>.</li>
		<li>Copy an existing API key or click the <strong><em>Create A Key</em></strong> button.</li>
		<li>Name your key descriptively, so you know what application uses that key.</li>
		<li><strong><em>Shortcode example:</strong></em> [maildepot file='http://www.yoursite.com/test.pdf']. Please use the WordPress Media Library to host these files.</li>
		<li>You can use the following file extensions: PDF, XLS, XLSX, PPT, PPTX, TXT, ZIP, RAR.</li>
		<li>Change the default "download" text by changing the shortcode like this: [maildepot file='http://yoursite.com/test.pdf' <strong>text='Link TEXT'</strong>]</li>
<br>
		<span>Feel free to reach out if you have any issues. Here's our <a href="https://a1local.com.au/contact/" target="_blank">Contact Form</a>.</span>
	</ol>
 <?php
}
 
function maildepot_mailchimp_api_key_cb($args) {
	$options = get_option( 'maildepotmailchimp_options' );
	$content = "";
	if( isset( $options[ $args['label_for'] ]) ){
	    $content = $options[ $args['label_for'] ];
	}
?>
 <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" data-custom="<?php echo esc_attr( $args['maildepotmailchimp_custom_data'] ); ?>"  name="maildepotmailchimp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr($content);?>">
<?php
}
 
function maildepot_mailchimp_link_text_cb( $args ) {
	$options = get_option( 'maildepotmailchimp_options' );
	$content = "";
	if( isset( $options[ $args['label_for'] ]) ){
	    $content = $options[ $args['label_for'] ];
	}
?>
 <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" data-custom="<?php echo esc_attr( $args['maildepotmailchimp_custom_data'] ); ?>"  name="maildepotmailchimp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $content );?>">

<?php
}
 
function maildepot_mailchimp_list_id_cb($args) {
	$options = get_option( 'maildepotmailchimp_options' );
 
	// Query String Perameters are here
	// for more reference please vizit http://developer.mailchimp.com/documentation/mailchimp/reference/lists/
	$data = array(
		'fields' => 'lists', // total_items, _links
	);
	 
	if( isset( $options['mailchimp_api_key'] ) ){
    	$api_key = $options['mailchimp_api_key'];
    	$url = 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/';
    	$result = json_decode( maildepot_mailchimp_curl_connect( $url, 'GET', $api_key, $data) );
    	
    	if( !empty($result->lists) ) {
    		echo '<select id="'.esc_attr( $args['label_for'] ).'" name="maildepotmailchimp_options['.esc_attr( $args['label_for'] ).']">';
    		echo '<option value="">Select list</option>';
    		foreach( $result->lists as $list ){
    		    if( isset( $options[ $args['label_for'] ] ) ){
        			echo '<option value="' . esc_attr($list->id) . '" '.selected( $options[ $args['label_for'] ], $list->id, false ).'>' . $list->name . ' (' . $list->stats->member_count . ')</option>';
        			// you can also use $list->date_created, $list->stats->unsubscribe_count, $list->stats->cleaned_count or vizit MailChimp API Reference for more parameters (link is above)
    		    }else{
    		        echo '<option value="' . esc_attr($list->id) . '" >' . $list->name . ' (' . $list->stats->member_count . ')</option>';
    		    }
    		        
    	    }
    		echo '</select>';
    	} elseif ( isset( $result->status ) && is_int( $result->status ) ) { // full error glossary is here http://developer.mailchimp.com/documentation/mailchimp/guides/error-glossary/
    		echo '<strong>' . $result->title . ':</strong> ' . $result->detail;
    	}else{
    	    echo '<strong>Invalid API Key!</strong>';
    	}
    }
} 
 
 
/**
 * top level menu
 */
function maildepotmailchimp_options_page() {
 // add top level menu page
    add_menu_page(
        'MailDepot',
        'MailDepot',
        'manage_options',
        'maildepot',
        'maildepotmailchimp_options_page_html'
    );
}
 
/**
 * register our maildepotmailchimp_options_page to the admin_menu action hook
 */
add_action( 'admin_menu', 'maildepotmailchimp_options_page' );
 
 
 
/**
 * top level menu:
 * callback functions
 */
function maildepotmailchimp_options_page_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    // add error/update messages
    
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
        // add settings saved message with the class of "updated"
        add_settings_error( 'maildepotmailchimp_messages', 'maildepotmailchimp_message', __( 'Settings Saved', 'maildepotmailchimp' ), 'updated' );
    }
    
    // show error/update messages
    settings_errors( 'maildepotmailchimp_messages' );
    ?><div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "maildepotmailchimp"
            settings_fields( 'maildepotmailchimp' );
            // output setting sections and their fields
            // (sections are registered for "maildepotmailchimp", each field is registered to a specific section)
            do_settings_sections( 'maildepotmailchimp' );
            // output save settings button
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div><?php
}

function maildepot_mailchimp_curl_connect( $url, $request_type, $api_key, $data = array() ) {
	
	$headers = array(
		"Content-Type"  =>  "application/json",
		"Authorization" =>  "Basic ".base64_encode( 'user:'. $api_key )
	);
    
    $resulatant = false;
    
    if( $request_type == 'GET' ){
        
		$url .= '?' . http_build_query($data);
        
        $response = wp_remote_request( $url, array(
            "headers"   =>  $headers,
            "timeout"   =>  10,
            "sslverify" =>  false,
            "method"    =>  "GET" // according to MailChimp API: POST/GET/PATCH/PUT/DELETE
        ));
        
        
    }else if( $request_type == 'POST' ){
        
        $response = wp_remote_request( $url, array(
            "headers"   =>  $headers,
            "body"      =>  wp_json_encode( $data ),
            "timeout"   =>  10,
            "sslverify" =>  false,
            "method"    => "POST" // according to MailChimp API: POST/GET/PATCH/PUT/DELETE
        ));
        
    }
    
	
	if ( is_array( $response ) && ! is_wp_error( $response ) ) {
        $headers = $response['headers']; // array of http header lines
        $resulatant = $response['body']; // use the content
    }
 
	return $resulatant;
}

function maildepot_mailchip_pdf_func( $atts = array() ) {
	
	$atts = wp_parse_args( $atts, array(
	    "file"  =>  "",
	    "text"  =>  ""
	) );
	
	$div_id = 'mailchimp-popup-'.maildepot_randomString();
	$options = get_option( 'maildepotmailchimp_options' );
	
	if(!empty($atts['text'])){
		$shortcode_text = $atts['text'];
	}else{
		$shortcode_text = $options['mailchimp_link_text'];
	}
	ob_start();
		add_thickbox(); ?><span id="my-content-<?php echo $div_id; ?>" style="display:none;">
			<input type="hidden" value="<?php echo $atts['file'];?>" name="mailchimp_file">
			<b>Downloading this file will automatically subscribe you to the newsletter, but you can unsubscribe at any time!</b> <input type="text" name="mailchimp_email" class="mailchimp_email">			
			<span>
				<input type="button" class="mailchimp_subscribe" value="Download and subscribe">
				<span id="mailchimp_error" class="mailchimp_error"></span>
			</span>
		</span><a href="#TB_inline?width=600&height=200&inlineId=my-content-<?php echo $div_id; ?>" class="thickbox"><?php echo $shortcode_text; ?></a><?php 
	$html = ob_get_contents();
	ob_end_clean();
	
	return $html;
}
add_shortcode( 'maildepot', 'maildepot_mailchip_pdf_func' );

function maildepot_randomString($length = 6) {
	$str = "";
	$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
	$max = count($characters) - 1;
	for ($i = 0; $i < $length; $i++) {
		$rand = mt_rand(0, $max);
		$str .= $characters[$rand];
	}
	return $str;
}



add_action('wp_ajax_mailchimp_subscribe', 'maildepot_mailchimp_subscribe_callback');
add_action('wp_ajax_nopriv_mailchimp_subscribe', 'maildepot_mailchimp_subscribe_callback');

function maildepot_mailchimp_subscribe_callback(){

	global $wpdb;
	$request = array('success'=>0, 'error'=>0, 'error_message' =>'');
	$error = false;
	
	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'mailchimp_subscribe' )) {
		$error = true;
		$request['error'] = 1;
		$request['error_message'] .= 'Security error';
	}
	
	/* Directly passing this email to mailchimp and not saving into database  */
	$mailchimp_email = sanitize_email( $_REQUEST['mailchimp_email'] );
	
	if(empty($mailchimp_email)){
		$error = true;
		$request['error'] = 1;
		$request['error_message'] .= 'Email is required';
	}	
	
	
	if ( $error === false ) {
	
		$options = get_option( 'maildepotmailchimp_options' );
		
		if( isset( $options['mailchimp_api_key'], $options['mailchimp_list_id'] ) ){
    		$api_key = $options['mailchimp_api_key'];
    		$list_id = $options['mailchimp_list_id'];
    	
    		$data = array(
    			'email_address' => $mailchimp_email,
    			'status'        => 'subscribed'
    		);
    		/* Possible fields
    		'merge_fields'  => [
                    'FNAME'     => $fname,
                    'LNAME'     => $lname
                ]
    		 */
    		
    		
    		$url = 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/'.$list_id.'/members';
    		$result = json_decode( maildepot_mailchimp_curl_connect( $url, 'POST', $api_key, $data) );
  
    		if(!empty($result->id) && $result->email_address == $mailchimp_email){
    			$request['success'] = 1;
    		}else{
    			$request['error'] = 1;
    			$request['error_message'] = '<strong>'.$result->title.':</strong> '.str_replace("Use PUT to insert or update list members.", "", $result->detail);
    		}
		}else{
		    $request['error'] = 1;
			$request['error_message'] = '<strong>Error: Contact this website administrator to setup MailDepot properly!</strong>';
		}
	}
	
	echo json_encode($request);	
	exit();
}

// Force Download functions

add_action('init', 'maildepot_force_download_init');
function maildepot_force_download_init(){

	if(isset($_GET['action'], $_REQUEST['d_nonce']) && $_GET['action'] == 'force_download' && !empty($_GET['file'])){
	    
	    if ( wp_verify_nonce( $_REQUEST['d_nonce'], 'download_referrer' )) {
    	    
    		$file = esc_url_raw( $_GET['file'] );
    		
    		if( $file ){
        		
        		$allowed_extensions = array(
        		    "pdf"   => "application/pdf",
        		    "xls"   => "application/vnd.ms-excel",
        		    "xlsx"  => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        		    "ppt"   => "application/vnd.ms-powerpoint",
        		    "pptx"  => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        		    "txt"   => "text/plain",
        		    "zip"   => "application/zip",
        		    "rar"   => "application/vnd.rar"
        		);
        		
        		$check = wp_check_filetype( $file ); //Retrieve the file type from the file name.
        		
        		if( in_array( strtolower($check["ext"]), array_keys($allowed_extensions) ) || in_array( $check["type"], array_values($allowed_extensions) ) ){
        	
            		$dfile_content = wp_remote_get( $file );
            		
            		if ( is_array( $dfile_content ) && ! is_wp_error( $dfile_content ) ) {
                        $headers = $dfile_content['headers']; // array of http header lines
                        
                        $resulatant = $dfile_content['body']; // use the content
                		
                		header("Content-Disposition: attachment; filename=\"".basename($file)."\""); 
                		header('Content-Type: application/force-download');
                		header("Content-Type: application/octet-stream");
                		
                		echo $resulatant;
                		exit();
            		}
        		}else{
        		    exit("Requested file type to download is not allowed!!!");
        		}
    		}else{
    		    exit("Invalid URL");
    		}
	    }else{
	        exit("Invalid nonce!!!");
	    }
	}

}