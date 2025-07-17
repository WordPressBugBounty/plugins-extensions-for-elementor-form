<?php

namespace Cool_FormKit\Admin\Register_Menu_Dashboard;

class CFKEF_Dashboard
{
    /**
     * The parent slug for the menu page.
     *
     * @var string
     */
    private $parent_slug = 'elementor';

    /**
     * The capability required to access the menu page.
     *
     * @var string
     */
    private $capability = 'manage_options';
    
    /**
     * The plugin name.
     *
     * @var string
     */
    private $plugin_name;
    
    /**
     * The version of the plugin.
     *
     * @var string
     */
    private $version;

    /**
     * The allowed pages.
     *
     * @var array
     */
    private static $allowed_pages = array(
        'cool-formkit',
        'cfkef-entries',
    );

    /**
     * The instance of the class.
     *
     * @var object
     */
    private static $instance = null;

    /**
     * Get the instance of the class.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of the plugin.
     * @return object The instance of the class.
     */
    public static function get_instance($plugin_name, $version)
    {
        if (null === self::$instance) {
            self::$instance = new self($plugin_name, $version);
        }
        return self::$instance;
    }

    /**
     * Constructor for the class.
     * 
     * @param callable $dashboard_callback The callback function for the dashboard page.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $dashboard_pages = array(
            // 'cool-formkit' => array(
            //     'title' => 'Cool FormKit Lite',
            //     'position' => 45,
            //     'slug' => 'cool-formkit',
            // ),
            'cfkef-entries' => array(
                'title' => '↳ Entries',
                'position' => 46,
                // 'slug' => 'edit.php?post_type=cfkef-entries', // Retained the original slug with post-new.php?post_type=
                'slug' => 'cfkef-entries', // Retained the original slug with post-new.php?post_type=
            )
        );

        $dashboard_pages = apply_filters('cfkef_dashboard_pages', $dashboard_pages);

        foreach (self::$allowed_pages as $page) {
            if (isset($dashboard_pages[$page]['slug']) && isset($dashboard_pages[$page]['title']) && isset($dashboard_pages[$page]['position'])) {
                $this->add_menu_page($dashboard_pages[$page]['slug'], $dashboard_pages[$page]['title'], isset($dashboard_pages[$page]['callback']) ? $dashboard_pages[$page]['callback'] : [$this, 'render_page'], $dashboard_pages[$page]['position']);
            }
        }

        add_action('elementor/admin-top-bar/is-active', [$this, 'hide_elementor_top_bar']);
        add_action('admin_print_scripts', [$this, 'hide_unrelated_notices']);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
    }

    /**
     * Add a menu page.
     *
     * @param string $slug The slug of the menu page.
     * @param string $title The title of the menu page.
     * @param callable $callback The callback function for the menu page.
     * @param int $position The position of the menu page.
     */
    public function add_menu_page($slug, $title, $callback, $position = 99)
    {
        add_action('admin_menu', function () use ($slug, $title, $callback, $position) {
            add_submenu_page(
                $this->parent_slug,
                str_replace('↳ ', '', $title),
                esc_html($title),
                $this->capability,
                $slug,
                $callback,
                $position
            );
        }, 999);
    }

    /**
     * Get the allowed pages.
     *
     * @return array The allowed pages.
     */
    public static function get_allowed_pages()
    {
        $allowed_pages = self::$allowed_pages;

        $allowed_pages = apply_filters('cfkef_dashboard_allowed_pages', $allowed_pages);

        return $allowed_pages;
    }

    /**
     * Check if the current screen is the given slug.
     *
     * @param string $slug The slug to check.
     * @return bool True if the current screen is the given slug, false otherwise.
     */ 
    public static function current_screen($slug, $tag_slug = null)
    {
        $slug = sanitize_text_field($slug);
        return self::cfkef_current_page($slug, $tag_slug);
    }

    /**
     * Check if the current page is the given slug.
     *
     * @param string $slug The slug to check.
     * @return bool True if the current page is the given slug, false otherwise.
     */
    private static function cfkef_current_page($slug, $tag_slug = null)
    {
        $current_page = isset($_REQUEST['page']) ? esc_html($_REQUEST['page']) : (isset($_REQUEST['post_type']) ? esc_html($_REQUEST['post_type']) : '');
        $status=false;

        $slug = $slug==='cfkef-entries' && !isset($_GET['tab']) && $current_page !== 'cfkef-entries' ? 'cool-formkit' : $slug;

        if (in_array($current_page, self::get_allowed_pages()) && $current_page === $slug) {
            $status=true;
        }

        if(function_exists('get_current_screen') && in_array($slug, self::get_allowed_pages())){
            $screen = get_current_screen();

            if($screen && property_exists($screen, 'id') && $screen->id && $screen->id === $slug){
                $status=true;
            }
        }

        if(isset($tag_slug)){
            $tag_slug = sanitize_text_field($tag_slug);
            $tab=isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : false;

            if(!isset($tab) || $tab !== $tag_slug){
                $status=false;
            }
        }

        return $status;
    }

    /**
     * Render the page.
     */
    public function render_page()
    {
        echo '<div class="cfkef-wrapper">';
        ?>
        <div class="cfkef-header">
                <div class="cfkef-header-logo">
                    <a href="?page=cool-formkit">
                        <img src="<?php echo esc_url(CFL_PLUGIN_URL . 'assets/images/logo-cool-formkit.png'); ?>" alt="Cool FormKit Logo">
                    </a>
                    <span>Lite</span>
                    <a class="button button-primary upgrade-pro-btn" target="_blank" href="https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=get-pro&utm_content=plugins-dashboard#pricing">
                        <img class="crown-diamond-pro" src="<?php echo esc_url(CFL_PLUGIN_URL . 'admin/assets/images/crown-diamond-pro.png'); ?>" alt="Cool FormKit Logo">
                        <?php esc_html_e('Upgrade To Pro', 'cool-formkit'); ?>
                    </a>
                </div>
                <div class="cfkef-header-buttons">
                    <p><?php esc_html_e('Advanced Elementor Form Builder.', 'cool-formkit'); ?></p>
                    <a href="https://docs.coolplugins.net/plugin/cool-formkit-for-elementor-form/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=doc&utm_content=setting-page-header" class="button" target="_blank"><?php esc_html_e('Check Docs', 'cool-formkit'); ?></a>
                    <a href="https://coolplugins.net/cool-formkit-for-elementor-forms/?utm_source=cfkl_plugin&utm_medium=inside&utm_campaign=view-demo&utm_content=setting-page-header" class="button button-secondary" target="_blank"><?php esc_html_e('View Form Demos', 'cool-formkit'); ?></a>
                </div>
            </div>
        <?php

        $this->render_tabs();

        echo '<div class="tab-content">';

        if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
            ?>
            <p>
                <?php esc_html_e( 'Form submissions submitted through', 'cool-formkit' ); ?> 
                <strong><?php esc_html_e( 'Elementor Pro Form Widget', 'cool-formkit' ); ?></strong> 
                <?php esc_html_e( 'are not shown here. You can view them in the', 'cool-formkit' ); ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=e-form-submissions' ) ); ?>" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e( 'Elementor Form Submissions section', 'cool-formkit' ); ?>
                </a>.
            </p>
            <?php
        }


        if(get_option('cfkef_enable_hello_plus',true) || get_option('cfkef_enable_formkit_builder',true)){
            do_action('cfkef_render_menu_pages', $this);
        }else{
            echo '<p style="margin:20px auto;
                    width: 500px;
                    padding: 50px;
                    background-color: white;
                    text-align: center;
            ">Sorry, you are not allowed on this page</p>';
        }        

        echo '</div></div>';
    }

    public function render_tabs(){
        $tabs = $this->cfkef_get_tabs();

        echo '<h2 class="nav-tab-wrapper cfkef-dashboard-tabs">';
        foreach ($tabs as $tab) {
            $active_class = self::current_screen($tab['slug']) ? ' nav-tab-active' : '';
            echo '<a href="' . esc_url(admin_url('admin.php?page=' . $tab['slug'])) . '" class="nav-tab ' . esc_attr($active_class) . '">' . esc_html($tab['title']) . '</a>';
        }
        echo '</h2>';
    }

    public function cfkef_get_tabs(){
        $default_tabs = array(
            array(
                'title' => 'Form Elements',
                'position' => 1,
                'slug' => 'cool-formkit',
            ),
            array(
                'title' => 'Settings',
                'position' => 3,
                'slug' => 'cool-formkit&tab=settings',
            ),
            array(
                'title' => 'License',
                'position' => 4,
                'slug' => 'cool-formkit&tab=license',
            ),
        );

        $tabs = apply_filters('cfkef_dashboard_tabs', $default_tabs);
        // Set the index of tabs based on their position
        usort($tabs, function($a, $b) {
            return $a['position'] <=> $b['position'];
        });

        return $tabs;
    }

    /**
     * Enqueue admin styles and scripts.
     *
     * @since    1.0.0
     */
    public function enqueue_admin_styles() {
        if (isset($_GET['page']) && self::current_screen($_GET['page'])) {
            wp_enqueue_style('cfkef-admin-style', CFL_PLUGIN_URL . 'assets/css/admin-style.css', array(), $this->version, 'all');
            wp_enqueue_style('dashicons');
            wp_enqueue_script('cfkef-admin-script', CFL_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), $this->version, true);
        }
    }

    /**
     * Hide the Elementor top bar.
     *
     * @param bool $is_active Whether the Elementor top bar is active.
     * @return bool Whether the Elementor top bar is active.
     */ 
    public function hide_elementor_top_bar($is_active)
    {
        foreach (self::$allowed_pages as $page) {
            if (self::current_screen($page)) {
                return false;
            }
        }

        return $is_active;
    }

    /**
     * Hide unrelated notices
     */
    public function hide_unrelated_notices()
    { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.NestingLevel.MaxExceeded
        $cfkef_pages = false;
        foreach (self::$allowed_pages as $page) {

            if (self::current_screen($page)) {
                $cfkef_pages = true;
                break;
            }
        }

        if ($cfkef_pages) {
            global $wp_filter;

            // Define rules to remove callbacks.
            $rules = [
                'user_admin_notices' => [], // remove all callbacks.
                'admin_notices'      => [],
                'all_admin_notices'  => [],
                'admin_footer'       => [
                    'render_delayed_admin_notices', // remove this particular callback.
                ],
            ];

            $notice_types = array_keys($rules);

            foreach ($notice_types as $notice_type) {
                if (empty($wp_filter[$notice_type]->callbacks) || ! is_array($wp_filter[$notice_type]->callbacks)) {
                    continue;
                }

                $remove_all_filters = empty($rules[$notice_type]);

                foreach ($wp_filter[$notice_type]->callbacks as $priority => $hooks) {
                    foreach ($hooks as $name => $arr) {
                        if (is_object($arr['function']) && is_callable($arr['function'])) {
                            if ($remove_all_filters) {
                                unset($wp_filter[$notice_type]->callbacks[$priority][$name]);
                            }
                            continue;
                        }

                        $class = ! empty($arr['function'][0]) && is_object($arr['function'][0]) ? strtolower(get_class($arr['function'][0])) : '';

                        // Remove all callbacks except WPForms notices.
                        if ($remove_all_filters && strpos($class, 'wpforms') === false) {
                            unset($wp_filter[$notice_type]->callbacks[$priority][$name]);
                            continue;
                        }

                        $cb = is_array($arr['function']) ? $arr['function'][1] : $arr['function'];

                        // Remove a specific callback.
                        if (! $remove_all_filters) {
                            if (in_array($cb, $rules[$notice_type], true)) {
                                unset($wp_filter[$notice_type]->callbacks[$priority][$name]);
                            }
                            continue;
                        }
                    }
                }
            }
        }

        add_action( 'admin_notices', [ $this, 'display_admin_notices' ], PHP_INT_MAX );
    }

    /**
     * Display admin notices.
     */
    public function display_admin_notices() {
        do_action('cfkef_admin_notices');
    }
}
