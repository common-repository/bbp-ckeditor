<?php

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'bbpress_ckeditor_admin' ) ) {


    final class bbpress_ckeditor_admin
    {

        private $options;

        public static function instance() {

            static $instance = null;

            if ( null === $instance ) {
                $instance = new bbpress_ckeditor_admin();
                $instance->setup_hooks();
            }

            return $instance;
        }

        private function setup_hooks() {
            add_action('admin_menu', array($this, 'admin_menu'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'), 10);
            add_filter( 'plugin_action_links', array( $this, 'add_setting_link_to_actions' ), 10, 2 );
        }

        public function enqueue_scripts() {
            wp_enqueue_style(bbpress_ckeditor()->get_domain('admin-css'), plugin_dir_url(__FILE__) . 'assets/css/admin.css');
        }

        public function admin_menu()
        {
            add_options_page(
                    __('bbPress CKEditor', bbpress_ckeditor()->get_domain()),
                    __('bbPress CKEditor', bbpress_ckeditor()->get_domain()),
                    'manage_options',
                    bbpress_ckeditor()->get_domain(),
                    array($this, 'options_page'));
            add_action('admin_init', array($this, 'page_init'));
        }

        public function page_init() {

            register_setting(
                bbpress_ckeditor()->get_domain(), // Option group
                bbpress_ckeditor()->get_domain(), // Option name
                array("sanitize_callback" => array($this, 'sanitize')) // Sanitize
            );

            add_settings_section(
                'ckeditor_version', // ID
                __('CKEditor Version', bbpress_ckeditor()->get_domain()), // Title
                null, // Callback
                bbpress_ckeditor()->get_domain('main-page')
            );

            add_settings_field(
                'ckeditor_version', // ID
                __('CKEditor Version', bbpress_ckeditor()->get_domain()), // Title
                array($this, 'ckeditor_version'), // Callback
                bbpress_ckeditor()->get_domain('main-page'),
                'ckeditor_version' // Section
            );

        }

        public function ckeditor_version() {
            ?>
            <select name="<?php _e($this->get_option_name('ckeditor_version')) ?>" id="<?php _e($this->get_option_name('ckeditor_version')) ?>">
                <option value="" <?php _e( selected("", $this->get_option("ckeditor_version")), bbpress_ckeditor()->get_domain()) ?>>Please Select</option>
                <optgroup label="CKEditor 4">
                    <option value="4_basic" <?php _e( selected("4_basic", $this->get_option("ckeditor_version")), bbpress_ckeditor()->get_domain()) ?>>Basic</option>
                    <option value="4_standard" <?php _e( selected("4_standard", $this->get_option("ckeditor_version")), bbpress_ckeditor()->get_domain()) ?>>Standard</option>
                    <option value="4_full" <?php _e( selected("4_full", $this->get_option("ckeditor_version")), bbpress_ckeditor()->get_domain()) ?>>Full</option>
                </optgroup>
            </select>
            <?php
        }

        public function options_page() {

            // Set class property
            ?>
            <div class="wrap">
                <h1><?php _e('bbPress CKEditor', bbpress_ckeditor()->get_domain()); ?></h1>

                <div class="bbpcke-main">
                    <form method="post" action="options.php">
                        <?php
                        // This prints out all hidden setting fields
                        settings_fields(bbpress_ckeditor()->get_domain());
                        do_settings_sections(bbpress_ckeditor()->get_domain('main-page'));
                        submit_button();
                        ?>
                    </form>
                </div>
                <div class="bbpcke-side">
                    <div class="bbpcke-box">
                        <h3><?php _e('Do you like Visual Editor For BBPress?', bbpress_ckeditor()->get_domain()); ?></h3>
                        <p><?php _e('If you\'re happy with this plugin, let others know:', bbpress_ckeditor()->get_domain()); ?></p>
                        <ul>
                            <li><a href="https://wordpress.org/support/plugin/bbp-ckeditor/reviews/#new-post" target="_blank"><?php _e('Give a good rating on WordPress.org', bbpress_ckeditor()->get_domain()); ?></a></li>
                        </ul>

                    </div>
                </div>
            </div>
            <?php
        }

        public function sanitize($input) {

            if($input['ckeditor_version'] == "4_basic" || $input['ckeditor_version'] == "4_full" || $input['ckeditor_version'] == "4_standard" || $input['ckeditor_version'] == "")
                return $input;
            return false;


        }

        public static function add_setting_link_to_actions( $links, $file ) {

            // Return normal links if not bbPress
            if ( plugin_basename( bbpress_ckeditor()->get_file() ) !== $file ) {
                return $links;
            }

            $links['settings'] = '<a href="' . esc_url( add_query_arg( array( 'page' => 'bbp-ckeditor'   ), admin_url( 'options-general.php' ) ) ) . '">' . esc_html__( 'Settings', 'bbpress' ) . '</a>';

            // Add a few links to the existing links array
            return $links;
        }

        public function get_option($name, $default = null)
        {
            if (is_null($this->options))
            {
                $this->options = get_option(bbpress_ckeditor()->get_domain(), array());
            }

            if (!isset($this->options[$name]))
            {
                if (is_null($default))
                {
                    return '';
                }
                else
                {
                    return $default;
                }
            }

            return $this->options[$name];
        }

        public function get_option_name($name) {
            return bbpress_ckeditor()->get_domain().'['.$name.']';
        }

    }
}

function bbpress_ckeditor_admin() {
    return bbpress_ckeditor_admin::instance();
}

