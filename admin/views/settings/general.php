<form method="post" action="<?= ap_admin_route('settings', ['tab' => 'general']) ?>">
    <?php wp_nonce_field(); ?>

    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="ap_settings[mail_sender_email]">Sender Email</label></th>
                <td>
                    <input name="ap_settings[mail_sender_email]" type="text" id="ap_settings[mail_sender_email]" value="<?= $settings['ap_settings']['mail_sender_email'] ?? 'contacto@autopublicate.com' ?>" class="regular-text ltr">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ap_settings[mail_sender_name]">Sender Name</label></th>
                <td>
                    <input name="ap_settings[mail_sender_name]" type="text" id="ap_settings[mail_sender_name]" value="<?= $settings['ap_settings']['mail_sender_name'] ?? 'Autopublícate®' ?>" class="regular-text ltr">
                    <p>This field will change the site name as well, please be aware about it.</p>
                </td>
            </tr>
        </tbody>
    </table>

    <h2 class="title">Advance Feature</h2>
    <p><span class="caution">!</span> Please don't change anything if you're not a developer, changes of this section can break the plugin/site functionalities.</p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="ap_settings[ap_route_action_hook]">Route Action Hook</label></th>
                <td>
                    <input name="ap_settings[ap_route_action_hook]" type="text" id="ap_settings[ap_route_action_hook]" value="<?= $settings['ap_settings']['ap_route_action_hook'] ?? 'elementor/theme/before_do_single' ?>" class="regular-text ltr">
                    <p>
                        To make it default: <kbd>elementor/theme/before_do_single</kbd>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ap_settings[ap_title_filter_hook]">Title Filter Hook</label></th>
                <td>
                    <input name="ap_settings[ap_title_filter_hook]" type="text" id="ap_settings[ap_title_filter_hook]" value="<?= $settings['ap_settings']['ap_title_filter_hook'] ?? 'document_title_parts' ?>" class="regular-text ltr">
                    <p>
                        To make it default: <kbd>document_title_parts</kbd>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
</form>