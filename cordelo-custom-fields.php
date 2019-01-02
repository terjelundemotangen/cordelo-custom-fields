<?php
/*
Plugin Name:  Cordelo custom fields
Plugin URI:   https://github.com/terjelundemotangen/cordelo-custom-fields
Description:  Adds custom fields to Events
Version:      1.0.0
Author:       Terje Lundemo Tangen
Author URI:   https://cordelo.com/
License:      GPL3
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
Text Domain:  cordelo
Domain Path:  /languages
*/

// check if The Events Calendar is in play

if ( in_array( 'the-events-calendar/the-events-calendar.php', apply_filters('active_plugins', get_option( 'active_plugins' )))) {
    
    // display wanted data on the page
    function cordelo_display_selected_custom_fields( $content ) {
        
        $cordelo_event_contact = get_post_meta( get_the_ID(), '_event_contact', true );
        $cordelo_event_contact_phone = get_post_meta( get_the_ID(), '_event_contact_phone', true );
        $cordelo_event_contact_email = get_post_meta( get_the_ID(), '_event_contact_email', true );
        if ( $cordelo_event_contact ) {
            $custom_fields = '<h3>Custom fields</h3>';
            $custom_fields .= '<p>' . $cordelo_event_contact . '<br>' . $cordelo_event_contact_phone . '<br><a mailto="' . $cordelo_event_contact_email . '">' . $cordelo_event_contact_email . '</a></p>';
        }
        
        return $content . $custom_fields;
    }
    // add_filter( 'the_content', 'cordelo_display_selected_custom_fields');
    
    // inject info from custom fields
    function cordelo_inject_info( $content ) {
        $cordelo_event_contact = get_post_meta( get_the_ID(), '_event_contact', true );
        $cordelo_event_contact_phone = get_post_meta( get_the_ID(), '_event_contact_phone', true );
        $cordelo_event_contact_email = get_post_meta( get_the_ID(), '_event_contact_email', true );
        if ( $cordelo_event_contact ) {
            $text_to_display = '<div class="tribe-events-meta-group tribe-events-meta-group-details">';
            $text_to_display .= '<h2 class="tribe-events-single-section-title">Primary contact</h2>';
            $text_to_display .= '<p>' . $cordelo_event_contact . '<br>' . $cordelo_event_contact_phone . '<br><a mailto="' . $cordelo_event_contact_email . '">' . $cordelo_event_contact_email . '</a></p>';
            $text_to_display .= '</div>';
            
            echo $text_to_display;
        }
    }
    add_action( 'tribe_post_get_template_part_modules/meta/organizer', 'cordelo_inject_info' );
    
    // register meta box
    function cordelo_add_meta_box() {
        
        $post_types = array( 'tribe_events' );
        
        foreach ( $post_types as $post_type ) {
            
            add_meta_box(
                'cordelo_event_contact_meta_box',
                'Event contact information',
                'cordelo_event_contact_display_meta_box',
                $post_type
            );
        }
    }
    
    add_action( 'add_meta_boxes', 'cordelo_add_meta_box' );
    
    // display meta box
    function cordelo_event_contact_display_meta_box( $post ) {
        
        $cordelo_contact_name = get_post_meta( $post->ID, '_event_contact', true );
        $cordelo_contact_phone = get_post_meta( $post->ID, '_event_contact_phone', true);
        $cordelo_contact_email = get_post_meta( $post->ID, '_event_contact_email', true);
        
        wp_nonce_field( basename( __FILE__ ), 'cordelo_meta_box_nonce' );
        
        ?>
        
        <p></p><label for="cordelo-event-contact-meta-box">Event contact name</label><br>
        <input type="text" id="cordelo-event-contact-meta-box" name="cordelo-event-contact-meta-box" size="50" value="<?php echo $cordelo_contact_name ?>"</input></p>
        <p></p><label for="cordelo-event-contact-phone-meta-box">Event contact phone</label><br>
        <input type="tel" id="cordelo-event-contact-phone-meta-box" name="cordelo-event-contact-phone-meta-box" value="<?php echo $cordelo_contact_phone ?>"></input></p>
        <p></p><label for="cordelo-event-contact-email-meta-box">Event contact email</label><br>
        <input type="email" id="cordelo-event-contact-email-meta-box" name="cordelo-event-contact-email-meta-box" size="50" value="<?php echo $cordelo_contact_email ?>"></input></p>
        <?php
    }
    
    // save meta box values
    function cordelo_save_meta_box( $post_id ) {
        
        $is_autosave = wp_is_post_autosave( $post_id );
        $is_revision = wp_is_post_revision( $post_id );
        
        $is_valid_nonce = false;
        
        if ( isset( $_POST[ 'cordelo_meta_box_nonce' ] ) ) {
            if ( wp_verify_nonce( $_POST[ 'cordelo_meta_box_nonce' ], basename( __FILE__ ) ) ) {
                $is_valid_nonce = true;
            }
        }
        
        if ( $is_autosave || $is_revision || !$is_valid_nonce ) return;
        
        // contact name
        if ( array_key_exists( 'cordelo-event-contact-meta-box', $_POST ) ) {
            
            update_post_meta(
                $post_id,
                '_event_contact',
                sanitize_text_field( $_POST[ 'cordelo-event-contact-meta-box' ] )
            );
        }
        // contact phone
        if ( array_key_exists( 'cordelo-event-contact-phone-meta-box', $_POST ) ) {
            
            update_post_meta(
                $post_id,
                '_event_contact_phone',
                sanitize_text_field( $_POST[ 'cordelo-event-contact-phone-meta-box' ] )
            );
        }
        // contact email
        if ( array_key_exists( 'cordelo-event-contact-email-meta-box', $_POST ) ) {
            
            update_post_meta(
                $post_id,
                '_event_contact_email',
                sanitize_text_field( $_POST[ 'cordelo-event-contact-email-meta-box' ] )
            );
        }
    }
    add_action( 'save_post', 'cordelo_save_meta_box');
	
}