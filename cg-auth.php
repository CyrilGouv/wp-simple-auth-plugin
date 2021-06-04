<?php
/**
 * Plugin Name: CG Authentication
 * Description: A simple Authentication plugin
 * Version: 1.0.0
 * Author: Cyril Gouv
 * Text Domain: cg-auth
 */


if ( !defined('ABSPATH') ) {
    echo 'WTF !!!';
    exit;
}


class CgAuth {
    public function __construct() {

        // Add Assets
        add_action( 'wp_enqueue_scripts', [$this, 'load_assets'] );

        // Ajax
        add_action( 'wp_ajax_nopriv_cg_login', [$this, 'login'] );

        // Add output to all page
        add_action( 'wp_head', [$this, 'output'] );
    }


    public function load_assets() {
        if ( is_user_logged_in() ) return;

        // CSS
        wp_enqueue_style( 'cg-auth-css', plugin_dir_url( __FILE__ ) . '/css/cg-auth.css', [], 1, 'all' );
        
        //JS
        wp_enqueue_script( 'jQuery', '//code.jquery.com/jquery-3.6.0.min.js' );
        wp_enqueue_script( 'cg-auth-js', plugin_dir_url( __FILE__ ) . '/js/cg-auth.js', ['jQuery'], 1, true );
    }

    public function login() {
        check_ajax_referer( 'ajax-login-nonce', 'cg_auth' );

        $infos = [];
        $infos['user_login'] = $_POST['username'];
        $infos['user_password'] = $_POST['password'];
        $infos['remember'] = true;

        $user_signon = wp_signon( $infos );

        if ( is_wp_error( $user_signon ) ) {
            echo json_encode([
                'status' => false,
                'message' => 'Wrong username or password'
            ]);

            die();
        }

        echo json_encode([
            'status' => true,
            'message' => 'Login successful, redirecting...'
        ]);

        die();
    }


    public function output() {
        if ( is_user_logged_in() ) return;

        ?>
        <form id="cg-auth-form" data-url="<?= admin_url('admin-ajax.php') ?>">
            <div class="auth-btn">
                <input type="button" value="Login" id="cg-show-auth-form">
            </div>

            <div id="cg-auth-container" class="auth-container">
                <a class="close" id="cg-auth-close" href="#">&times;</a>
                <h2>Site Login</h2>
                <label for="username">Username</label>
                <input type="text" id="username" name="username">

                <label for="password">Password</label>
                <input type="text" id="password" name="password">

                <input type="submit" value="Login" class="submit_button" name="submit">
                <p class="status" data-message="status"></p>

                <p class="actions">
                    <a href="<?= wp_lostpassword_url() ?>">Forgot Password ?</a> - 
                    <a href="<?= wp_registration_url() ?>">Register</a>
                </p>

                <input type="hidden" name="action" value="cg_login">
                <?php wp_nonce_field( 'ajax-login-nonce', 'cg_auth' ); ?>
            </div>
        </form>
        <?php
    }
}

new CgAuth;