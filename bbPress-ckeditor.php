<?php

/**
 * Plugin Name: Visual Editor For BBPress
 * Plugin URI:  https://www.dylan-lea.com/plugins/bbpress-ckeditor
 * Description: Replace the regular bbPress editor with the more advanced CKEditor.
 * Author:      Dylan Lea
 * Author URI:  https://www.dylan-lea.com/
 * Version:     1.0.2
 * Text Domain: bbpress-ckeditor
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'bbpress_ckeditor' ) ) {


    final class bbpress_ckeditor
    {

        private static $domain = 'bbp-ckeditor';

        public static function instance() {

            static $instance = null;

            if ( null === $instance ) {
                $instance = new bbpress_ckeditor();
                $instance->setup_hooks();
            }

            return $instance;
        }

        private function setup_hooks() {
            add_action( 'wp_enqueue_scripts', array($this, 'load_scripts'), 10 );
            add_filter( 'bbp_get_the_content', array($this, 'replace_bbp_new_topic_textarea'), 10, 3 );
        }

        public function load_scripts() {
            switch(bbpress_ckeditor_admin()->get_option('ckeditor_version')) {
                case "4_basic":
                    wp_enqueue_script($this->get_domain('js'), plugin_dir_url(__FILE__) . 'assets/js/ckeditor/4/basic.js', null);
                    wp_enqueue_script($this->get_domain('4'), plugin_dir_url(__FILE__) . 'assets/js/ckeditor/4/ckeditor.js', ['jquery', $this->get_domain('js')], null);
                    break;
                case "4_standard":
                    wp_enqueue_script($this->get_domain('js'), plugin_dir_url(__FILE__) . 'assets/js/ckeditor/4/standard.js', ['jquery'], null);
                    wp_enqueue_script($this->get_domain('4'), plugin_dir_url(__FILE__) . 'ckeditor.js', ['jquery', $this->get_domain('js')], null);
                    break;
                case "4_full":
                    wp_enqueue_script($this->get_domain('js'), plugin_dir_url(__FILE__) . 'assets/js/ckeditor/4/full.js', ['jquery'], null);
                    wp_enqueue_script($this->get_domain('4'), plugin_dir_url(__FILE__) . 'ckeditor.js', ['jquery', $this->get_domain('js')], null);
                    break;
            }
        }


        public function replace_bbp_new_topic_textarea($output, $args, $post_content) {
            if(bbpress_ckeditor_admin()->get_option('ckeditor_version') !== "") {
                return '<textarea name="bbp_topic_content" id="editor" rows="10" cols="80">
                    '.$post_content.'
                </textarea>';
            } else {
                return $output;
            }
        }

        public function get_domain($string = null) {
            if(is_null($string)) {
                return self::$domain;
            }
            return self::$domain . '-' . $string;
        }

        public function get_template($template_file, $args = []) {

        }

        public function get_file() {
            return __FILE__;
        }

    }
}

function bbpress_ckeditor() {
    return bbpress_ckeditor::instance();
}

bbpress_ckeditor();
require_once('bbpress-ckeditor-admin.php');
bbpress_ckeditor_admin();

