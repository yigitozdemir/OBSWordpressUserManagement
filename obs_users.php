<?php

/**
 * @package com.obs.users
 * @version 0.0.1
 */
/*
Plugin Name: Public user management for Wordpress
Plugin URI: https://yigitnot.com/
Description: A plugin for adding user membership to wordpress
Author: Yigit Ozdemir
Version: 0.0.1
Author URI: https://yigitnot.com/
*/

//Backup database:
// /opt/lampp/bin/mysqldump wpvue -u root > /opt/lampp/htdocs/wordpress/wp-content/plugins/obs_users/backup.sql
//Drop database:
// echo "drop database wpvue" | /opt/lampp/bin/mysql -u root
//Restore database:
// echo "create database wpvue" | /opt/lampp/bin/mysql -u root
// /opt/lampp/bin/mysql wpvue -u root < /opt/lampp/htdocs/wordpress/wp-content/plugins/obs_users/backup.sql

define( 'OBS_ROLE_NAME', 'obs_user' );
define( 'OBS_REGISTER_PAGE_SHORTCODE', 'obsregister' );
define( 'OBS_PROFILE_PAGE_SHORTCODE', 'obsProfile');
define( 'OBS_REGISTER_PAGE_ID_SETTING', 'obsRegisterPageId' );
define( 'OBS_PROFILE_PAGE_ID_SETTING', 'obsProfilePageId' );

register_activation_hook( __FILE__, 'obsUserActivationHook' );
register_deactivation_hook( __FILE__, 'obsUserDeactivationHook' );

//1. Add a new form element...
add_action( 'register_form', 'myplugin_register_form' );
function myplugin_register_form() {

    $first_name = ( ! empty( $_POST['first_name'] ) ) ? sanitize_text_field( $_POST['first_name'] ) : '';
        
        ?>
        <p>
            <label for="first_name"><?php _e( 'First Name', 'mydomain' ) ?><br />
                <input type="text" name="first_name" id="first_name" class="input" value="<?php echo esc_attr(  $first_name  ); ?>" size="25" /></label>
        </p>
        <?php
    }

/**
 * Render the registration page shortcode
 */
function obsRenderRegisterPage()
{
    echo 'This is registration page';
}

function obsRenderProfilePage()
{
    echo '<h1> This is profile page </h1>';
}

add_shortcode( OBS_REGISTER_PAGE_SHORTCODE, 'obsRenderRegisterPage' );
add_shortcode( OBS_PROFILE_PAGE_SHORTCODE, 'obsRenderProfilePage' );

function obsUserActivationHook()
{
    $roleCapabilities = array('read' => true);
    add_role( OBS_ROLE_NAME, 'OBS User', $roleCapabilities );

    obsRegistrationPageOperations(true);
    obsProfilePageOperations( true );
}

function obsUserDeactivationHook()
{
    remove_role( OBS_ROLE_NAME );

    
    obsRegistrationPageOperations(false);
    remove_shortcode( OBS_REGISTER_PAGE_SHORTCODE );
    obsRegistrationPageOperations(false);
    delete_option( OBS_REGISTER_PAGE_ID_SETTING );
    obsProfilePageOperations( false );
    delete_option( OBS_PROFILE_PAGE_ID_SETTING );
}



/**
 * Registration page add remove
 *
 * @param boolean $create true for create, false for delete page
 * @return void
 */
function obsRegistrationPageOperations($create = true)
{
    if($create)
    {
        $postId = wp_insert_post(
            array(
            'comment_status' => 'close',
            'ping_status'    => 'close',
            'post_author'    => 1,
            'post_title'     => 'Register',
            'post_name'      => 'OBS Register',
            'post_status'    => 'publish',
            'post_content'   => '['. OBS_REGISTER_PAGE_SHORTCODE .'/]',
            'post_type'      => 'page',
            )
        );

        add_option( OBS_REGISTER_PAGE_ID_SETTING, $postId );
    }
    else
    {
        wp_delete_post( get_option( OBS_REGISTER_PAGE_ID_SETTING ) );
    }
}


function obsProfilePageOperations($create = false)
{
    if ( $create )
    {
        $postId = wp_insert_post(
            array(
            'comment_status' => 'close',
            'ping_status'    => 'close',
            'post_author'    => 1,
            'post_title'     => 'Profile',
            'post_name'      => 'OBS Profile',
            'post_status'    => 'publish',
            'post_content'   => '['. OBS_PROFILE_PAGE_SHORTCODE .'/]',
            'post_type'      => 'page',
            )
        );

        add_option( OBS_PROFILE_PAGE_ID_SETTING, $postId );
    }
    else
    {
        wp_delete_post( get_option( OBS_PROFILE_PAGE_ID_SETTING ) );
    }
}

