<?php
defined('ABSPATH') || exit;

class VMW_Settings
{
    private static ?VMW_Settings $instance = null;

    public static function instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_post_vmw_save_settings',    [$this, 'handle_save_settings']);
        add_action('admin_post_vmw_test_connection',  [$this, 'handle_test_connection']);
    }

    // ------------------------------------------------------------------ menu
    public function add_menu(): void
    {
        add_options_page(
            __('Vodafone More', 'vodafone-more-woo'),
            __('Vodafone More', 'vodafone-more-woo'),
            'manage_options',
            'vmw-settings',
            [$this, 'render_page']
        );
    }

    // ------------------------------------------------------------------ register settings
    // Only non-sensitive fields go through the WP Settings API
    public function register_settings(): void
    {
        register_setting('vmw_settings_group', 'vmw_env',           ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('vmw_settings_group', 'vmw_dev_username',  ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('vmw_settings_group', 'vmw_prod_username', ['sanitize_callback' => 'sanitize_text_field']);
    }

    // ------------------------------------------------------------------ encryption
    public static function encrypt(string $value): string
    {
        $key = defined('AUTH_KEY') ? AUTH_KEY : 'vmw_fallback_key';
        return base64_encode($value ^ str_repeat($key, ceil(strlen($value) / strlen($key))));
    }

    public static function decrypt(string $value): string
    {
        if (empty($value)) return '';
        $key     = defined('AUTH_KEY') ? AUTH_KEY : 'vmw_fallback_key';
        $decoded = base64_decode($value);
        return $decoded ^ str_repeat($key, ceil(strlen($decoded) / strlen($key)));
    }

    // ------------------------------------------------------------------ getters (used by VMW_API)
    public static function get_env(): string
    {
        return get_option('vmw_env', 'development');
    }

    public static function get_username(string $env): string
    {
        return get_option("vmw_{$env}_username", '');
    }

    public static function get_password(string $env): string
    {
        return self::decrypt(get_option("vmw_{$env}_password", ''));
    }

    // ------------------------------------------------------------------ save settings handler
    // Passwords are saved manually here, bypassing the WP sanitize callback entirely
    public function handle_save_settings(): void
    {
        check_admin_referer('vmw_save_settings');
        if (!current_user_can('manage_options')) wp_die('Unauthorized');

        // Save non-sensitive fields
        update_option('vmw_env',           sanitize_text_field($_POST['vmw_env'] ?? 'development'));
        update_option('vmw_dev_username',  sanitize_text_field($_POST['vmw_dev_username'] ?? ''));
        update_option('vmw_prod_username', sanitize_text_field($_POST['vmw_prod_username'] ?? ''));

        // Save passwords only if provided
        $dev_pass = $_POST['vmw_dev_password'] ?? '';
        if (!empty($dev_pass)) {
            update_option('vmw_dev_password', self::encrypt($dev_pass));
        }

        $prod_pass = $_POST['vmw_prod_password'] ?? '';
        if (!empty($prod_pass)) {
            update_option('vmw_prod_password', self::encrypt($prod_pass));
        }

        set_transient('vmw_save_result', '✅ <strong>Settings saved.</strong>', 60);
        wp_safe_redirect(admin_url('options-general.php?page=vmw-settings'));
        exit;
    }

    // ------------------------------------------------------------------ render page
    public function render_page(): void
    {
        $active_env = self::get_env();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Vodafone More – Settings', 'vodafone-more-woo'); ?></h1>

            <?php if ($msg = get_transient('vmw_save_result')) : delete_transient('vmw_save_result'); ?>
                <div class="notice notice-success is-dismissible"><p><?php echo wp_kses_post($msg); ?></p></div>
            <?php endif; ?>

            <?php if ($msg = get_transient('vmw_test_result')) : delete_transient('vmw_test_result'); ?>
                <div class="notice notice-info is-dismissible"><p><?php echo wp_kses_post($msg); ?></p></div>
            <?php endif; ?>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('vmw_save_settings'); ?>
                <input type="hidden" name="action" value="vmw_save_settings">

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="vmw_env"><?php esc_html_e('Active Environment', 'vodafone-more-woo'); ?></label>
                        </th>
                        <td>
                            <select name="vmw_env" id="vmw_env">
                                <option value="development" <?php selected($active_env, 'development'); ?>>
                                    <?php esc_html_e('Development', 'vodafone-more-woo'); ?>
                                </option>
                                <option value="production" <?php selected($active_env, 'production'); ?>>
                                    <?php esc_html_e('Production', 'vodafone-more-woo'); ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                </table>

                <h2><?php esc_html_e('Development Credentials', 'vodafone-more-woo'); ?></h2>
                <table class="form-table" role="presentation">
                    <tr>
                        <th><label for="vmw_dev_username"><?php esc_html_e('Username', 'vodafone-more-woo'); ?></label></th>
                        <td>
                            <input type="text" name="vmw_dev_username" id="vmw_dev_username"
                                value="<?php echo esc_attr(self::get_username('dev')); ?>"
                                class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="vmw_dev_password"><?php esc_html_e('Password', 'vodafone-more-woo'); ?></label></th>
                        <td>
                            <input type="password" name="vmw_dev_password" id="vmw_dev_password"
                                value="" class="regular-text" autocomplete="new-password"
                                placeholder="<?php esc_attr_e('Leave blank to keep current', 'vodafone-more-woo'); ?>">
                            <?php if (get_option('vmw_dev_password')) : ?>
                                <p class="description">✅ <?php esc_html_e('Password is saved.', 'vodafone-more-woo'); ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <h2><?php esc_html_e('Production Credentials', 'vodafone-more-woo'); ?></h2>
                <table class="form-table" role="presentation">
                    <tr>
                        <th><label for="vmw_prod_username"><?php esc_html_e('Username', 'vodafone-more-woo'); ?></label></th>
                        <td>
                            <input type="text" name="vmw_prod_username" id="vmw_prod_username"
                                value="<?php echo esc_attr(self::get_username('prod')); ?>"
                                class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="vmw_prod_password"><?php esc_html_e('Password', 'vodafone-more-woo'); ?></label></th>
                        <td>
                            <input type="password" name="vmw_prod_password" id="vmw_prod_password"
                                value="" class="regular-text" autocomplete="new-password"
                                placeholder="<?php esc_attr_e('Leave blank to keep current', 'vodafone-more-woo'); ?>">
                            <?php if (get_option('vmw_prod_password')) : ?>
                                <p class="description">✅ <?php esc_html_e('Password is saved.', 'vodafone-more-woo'); ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Save Settings', 'vodafone-more-woo')); ?>
            </form>

            <hr>
            <h2><?php esc_html_e('Test Connection', 'vodafone-more-woo'); ?></h2>
            <p><?php printf(
                esc_html__('Tests a login using the active environment (%s).', 'vodafone-more-woo'),
                '<strong>' . esc_html($active_env) . '</strong>'
            ); ?></p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('vmw_test_connection'); ?>
                <input type="hidden" name="action" value="vmw_test_connection">
                <?php submit_button(__('Test Connection', 'vodafone-more-woo'), 'secondary', 'submit', false); ?>
            </form>
        </div>
        <?php
    }

    // ------------------------------------------------------------------ test connection
    public function handle_test_connection(): void
    {
        check_admin_referer('vmw_test_connection');
        if (!current_user_can('manage_options')) wp_die('Unauthorized');

        $api    = VMW_API::instance();
        $result = $api->login();

        if (200 === (int) ($result['status_code'] ?? 0)) {
            $msg = '✅ <strong>Connection successful!</strong> Logged in as: ' . esc_html($result['data']['fullname'] ?? '');
        } else {
            $msg = '❌ <strong>Connection failed:</strong> ' . esc_html($result['status_message'] ?? 'Unknown error');
        }

        set_transient('vmw_test_result', $msg, 60);
        wp_safe_redirect(admin_url('options-general.php?page=vmw-settings'));
        exit;
    }
}