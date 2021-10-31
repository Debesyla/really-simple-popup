<?php
/**
* Plugin Name: Really simple popup
* Description: Popupas puslapiui. Užsikrauna visuose puslapiuose. Galite pasiredaguoti kaip tik norite.
* Version: 1.5
* Author: Danielius Goriunovas
**/

/* Base */
function really_simple_register_settings() {
   add_option( 'really_simple_option_nuoroda' );
   register_setting( 'really_simple_options_group', 'really_simple_option_nuoroda', 'really_simple_callback' );
}
add_action( 'admin_init', 'really_simple_register_settings' );



/* Admin menu */
add_action( 'admin_menu', 'really_simple_popup_add_admin_menu' );

function really_simple_popup_add_admin_menu() { 
	add_menu_page( 'Really simple popup', 'Really simple popup', 'manage_options', 'really_simple_popup', 'really_simple_popup_settings_page_callback' );
}

function really_simple_popup_settings_page_callback() {
	
	// Save attachment ID
	if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['image_attachment_id'] ) ) :
		update_option( 'really_simple_popup_attachment_id', absint( $_POST['image_attachment_id'] ) );
	endif;
	
	wp_enqueue_media();
	?>
	
		<h1>Nuotrauka</h1>
		<form method='post'>
			<div class='image-preview-wrapper'>
				<img id='image-preview' src='<?php echo wp_get_attachment_url( get_option( 'really_simple_popup_attachment_id' ) ); ?>' height='100'>
			</div>
			<input id="upload_image_button" type="button" class="button" value="<?php _e( 'Įkelkite nuotrauką' ); ?>" />
			<input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?php echo get_option( 'really_simple_popup_attachment_id' ); ?>'>
			<input type="submit" name="submit_image_selector" value="Išsaugoti nuotrauką" class="button-primary">
		</form>
		
		<br>
		<br>
		
		<h1>Nuoroda</h1>
		
		<form method="post" action="options.php">
			<?php settings_fields( 'really_simple_options_group' ); ?>
			<input type="text" id="really_simple_option_nuoroda" name="really_simple_option_nuoroda" value="<?php echo get_option('really_simple_option_nuoroda'); ?>" />
			<p style="margin-bottom: -1em;">Įveskite pilną nuorodą. Pvz. 'https://www.google.com/', ne 'google.com'.</p>
			<?php submit_button('Išsaugoti nuorodą'); ?>
		</form>
	
	<?php
}

add_action( 'admin_footer', 'really_simple_popup_print_scripts' );
function really_simple_popup_print_scripts() {
	$my_saved_attachment_post_id = get_option( 'really_simple_popup_attachment_id', 0 );
	?><script type='text/javascript'>
		jQuery( document ).ready( function( $ ) {
			// Uploading files
			var file_frame;
			var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
			var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this
			jQuery('#upload_image_button').on('click', function( event ){
				event.preventDefault();
				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					// Set the post ID to what we want
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					// Open frame
					file_frame.open();
					return;
				} else {
					// Set the wp.media post id so the uploader grabs the ID we want when initialised
					wp.media.model.settings.post.id = set_to_post_id;
				}
				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					title: 'Select a image to upload',
					button: {
						text: 'Use this image',
					},
					multiple: false	// Set to true to allow multiple files to be selected
				});
				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					attachment = file_frame.state().get('selection').first().toJSON();
					// Do something with attachment.id and/or attachment.url here
					$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
					$( '#image_attachment_id' ).val( attachment.id );
					// Restore the main post ID
					wp.media.model.settings.post.id = wp_media_post_id;
				});
					// Finally, open the modal
					file_frame.open();
			});
			// Restore the main ID when the add media button is pressed
			jQuery( 'a.add_media' ).on( 'click', function() {
				wp.media.model.settings.post.id = wp_media_post_id;
			});
		});
	</script><?php
}



/* Add holder div to <body> */
add_action( 'wp_footer', 'really_simple_popup_div' );
function really_simple_popup_div() {
	
	if( get_option( 'really_simple_popup_attachment_id' ) ) {
		echo '<div class="really_simple__popup" id="rs-popup">
				<div class="really_simple__background really_simple__popup-close"></div>
				<div class="really_simple__popup-inner">
					<div class="really_simple__box">
						<img class="really_simple__popup-close" src="' . plugin_dir_url( __FILE__ ) . 'x.svg">';
						
						if( get_option( 'really_simple_option_nuoroda' ) ) {
							echo '<a href="' . get_option( 'really_simple_option_nuoroda' ) . '">';
						};
						
						echo '<img class="popup-img" src="' . wp_get_attachment_url( get_option( 'really_simple_popup_attachment_id' ) ) . '">';
						
						
						if( get_option( 'really_simple_option_nuoroda' ) ) {
							echo '</a>';
						};
						
						echo '
					</div>
				</div>
			</div>
		';
	};
	
}



/* And enqueue js (needs jquery) and css */
function adface_enqueue_script() {   
	wp_enqueue_script( 'js', plugin_dir_url( __FILE__ ) . 'j.js', array('jquery'), true, true );
	wp_enqueue_script('cookie', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js', array(), false, true);
	wp_enqueue_style( 'css', plugin_dir_url( __FILE__ ) . 'style.css' );
}
add_action('wp_enqueue_scripts', 'adface_enqueue_script');
