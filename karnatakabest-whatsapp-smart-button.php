<?php
/**
 * Plugin Name: KarnatakaBest WhatsApp Smart Button
 * Plugin URI:  https://karnatakabest.com
 * Description: Add WhatsApp Smart Buttons (Group, Channel, Contact) with icon/text toggle, floating mode, and Kannada-English fallback.
 * Version:     1.0
 * Author:      KarnatakaBest.com
 * Author URI:  https://karnatakabest.com
 * License:     GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add menu page
add_action('admin_menu', 'kb_whatsapp_smart_button_menu');
function kb_whatsapp_smart_button_menu() {
    add_options_page(
        'WhatsApp Smart Button Settings',
        'WhatsApp Smart Button',
        'manage_options',
        'kb-whatsapp-smart-button',
        'kb_whatsapp_smart_button_settings_page'
    );
}

// Register settings
add_action('admin_init', 'kb_whatsapp_smart_button_register_settings');
function kb_whatsapp_smart_button_register_settings() {
    register_setting('kb_whatsapp_smart_button_settings', 'kb_whatsapp_group_url');
    register_setting('kb_whatsapp_smart_button_settings', 'kb_whatsapp_channel_url');
    register_setting('kb_whatsapp_smart_button_settings', 'kb_whatsapp_contact_url');
    register_setting('kb_whatsapp_smart_button_settings', 'kb_whatsapp_show_group');
    register_setting('kb_whatsapp_smart_button_settings', 'kb_whatsapp_show_channel');
    register_setting('kb_whatsapp_smart_button_settings', 'kb_whatsapp_show_contact');
    register_setting('kb_whatsapp_smart_button_settings', 'kb_whatsapp_icon_only');
    register_setting('kb_whatsapp_smart_button_settings', 'kb_whatsapp_floating_mode');
    register_setting('kb_whatsapp_smart_button_settings', 'kb_whatsapp_fallback_text_group');
    register_setting('kb_whatsapp_smart_button_settings', 'kb_whatsapp_fallback_text_channel');
    register_setting('kb_whatsapp_smart_button_settings', 'kb_whatsapp_fallback_text_contact');
}

// Admin settings page
function kb_whatsapp_smart_button_settings_page() { ?>
    <div class="wrap">
        <h1>WhatsApp Smart Button Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('kb_whatsapp_smart_button_settings'); ?>
            <?php do_settings_sections('kb_whatsapp_smart_button_settings'); ?>

            <h2>Button URLs</h2>
            <p><input type="text" name="kb_whatsapp_group_url" value="<?php echo esc_attr(get_option('kb_whatsapp_group_url')); ?>" placeholder="Group URL" size="50"></p>
            <p><input type="text" name="kb_whatsapp_channel_url" value="<?php echo esc_attr(get_option('kb_whatsapp_channel_url')); ?>" placeholder="Channel URL" size="50"></p>
            <p><input type="text" name="kb_whatsapp_contact_url" value="<?php echo esc_attr(get_option('kb_whatsapp_contact_url')); ?>" placeholder="Contact URL" size="50"></p>

            <h2>Show/Hide Buttons</h2>
            <p><label><input type="checkbox" name="kb_whatsapp_show_group" value="1" <?php checked(1, get_option('kb_whatsapp_show_group')); ?>> Show Group Button</label></p>
            <p><label><input type="checkbox" name="kb_whatsapp_show_channel" value="1" <?php checked(1, get_option('kb_whatsapp_show_channel')); ?>> Show Channel Button</label></p>
            <p><label><input type="checkbox" name="kb_whatsapp_show_contact" value="1" <?php checked(1, get_option('kb_whatsapp_show_contact')); ?>> Show Contact Button</label></p>

            <h2>Display Options</h2>
            <p><label><input type="checkbox" name="kb_whatsapp_icon_only" value="1" <?php checked(1, get_option('kb_whatsapp_icon_only')); ?>> Icon Only Mode</label></p>
            <p><label><input type="checkbox" name="kb_whatsapp_floating_mode" value="1" <?php checked(1, get_option('kb_whatsapp_floating_mode')); ?>> Floating Mode (☰ Toggle)</label></p>

            <h2>Fallback Text</h2>
            <p>Group: <input type="text" name="kb_whatsapp_fallback_text_group" value="<?php echo esc_attr(get_option('kb_whatsapp_fallback_text_group', 'Join Group')); ?>"></p>
            <p>Channel: <input type="text" name="kb_whatsapp_fallback_text_channel" value="<?php echo esc_attr(get_option('kb_whatsapp_fallback_text_channel', 'Join Channel')); ?>"></p>
            <p>Contact: <input type="text" name="kb_whatsapp_fallback_text_contact" value="<?php echo esc_attr(get_option('kb_whatsapp_fallback_text_contact', 'Contact Us')); ?>"></p>

            <?php submit_button(); ?>
        </form>
    </div>
<?php }

// Frontend display
add_action('wp_footer', 'kb_whatsapp_smart_button_display');
function kb_whatsapp_smart_button_display() {
    $group_url   = esc_url(get_option('kb_whatsapp_group_url'));
    $channel_url = esc_url(get_option('kb_whatsapp_channel_url'));
    $contact_url = esc_url(get_option('kb_whatsapp_contact_url'));

    $show_group   = get_option('kb_whatsapp_show_group');
    $show_channel = get_option('kb_whatsapp_show_channel');
    $show_contact = get_option('kb_whatsapp_show_contact');

    $icon_only   = get_option('kb_whatsapp_icon_only');
    $floating    = get_option('kb_whatsapp_floating_mode');

    $text_group   = esc_html(get_option('kb_whatsapp_fallback_text_group', 'Join Group'));
    $text_channel = esc_html(get_option('kb_whatsapp_fallback_text_channel', 'Join Channel'));
    $text_contact = esc_html(get_option('kb_whatsapp_fallback_text_contact', 'Contact Us'));

    if ( !$show_group && !$show_channel && !$show_contact ) return;
    ?>
    <style>
    .kb-whatsapp-buttons { position: fixed; bottom: 20px; right: 20px; z-index: 9999; }
    .kb-whatsapp-buttons a { display: block; margin: 5px 0; padding: 10px 15px; background: #25D366; color: #fff; border-radius: 5px; text-decoration: none; font-size: 16px; }
    .kb-whatsapp-buttons a span { margin-left: 8px; }
    .kb-floating-toggle { position: fixed; bottom: 80px; right: 20px; background: #333; color: #fff; padding: 10px 15px; border-radius: 5px; cursor: pointer; }
    </style>

    <?php if ( $floating ) : ?>
        <div class="kb-floating-toggle" onclick="document.querySelector('.kb-whatsapp-buttons').classList.toggle('open')">☰</div>
    <?php endif; ?>

    <div class="kb-whatsapp-buttons" style="<?php echo $floating ? 'display:none;' : ''; ?>">
        <?php if ( $show_group && $group_url ): ?>
            <a href="<?php echo $group_url; ?>" target="_blank">👥 <?php if(!$icon_only) echo '<span>'.$text_group.'</span>'; ?></a>
        <?php endif; ?>
        <?php if ( $show_channel && $channel_url ): ?>
            <a href="<?php echo $channel_url; ?>" target="_blank">📢 <?php if(!$icon_only) echo '<span>'.$text_channel.'</span>'; ?></a>
        <?php endif; ?>
        <?php if ( $show_contact && $contact_url ): ?>
            <a href="<?php echo $contact_url; ?>" target="_blank">📞 <?php if(!$icon_only) echo '<span>'.$text_contact.'</span>'; ?></a>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggle = document.querySelector('.kb-floating-toggle');
        if(toggle){
            toggle.addEventListener('click', function(){
                document.querySelector('.kb-whatsapp-buttons').style.display =
                    document.querySelector('.kb-whatsapp-buttons').style.display === 'none' ? 'block' : 'none';
            });
        }
    });
    </script>
    <?php
}
