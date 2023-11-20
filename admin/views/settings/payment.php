<form method="post" action="<?= ap_admin_route('settings', ['tab' => 'payment']) ?>">
    <?php wp_nonce_field(); ?>

    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="ap_payment_settings[provider_charge]">Provider Charge</label></th>
                <td>
                    <input style="min-width: 40%;" name="ap_payment_settings[provider_charge]" type="number" step="any" id="ap_payment_settings[provider_charge]" value="<?= $settings['ap_payment_settings']['provider_charge'] ?? 0 ?>" class="ltr">
                    <select style="max-width: 10%; vertical-align: top;" name="ap_payment_settings[provider_charge_type]" id="ap_payment_settings[provider_charge_type]" class="ltr small">
                    <option <?= isset($settings['ap_payment_settings']['provider_charge_type']) && $settings['ap_payment_settings']['provider_charge_type'] = '%' ? 'selected' : '' ?>>%</option>
                        <option <?= isset($settings['ap_payment_settings']['provider_charge_type']) && $settings['ap_payment_settings']['provider_charge_type'] = '€' ? 'selected' : '' ?>>€</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ap_payment_settings[buyer_charge]">Buyer Charge</label></th>
                <td>
                    <input style="min-width: 40%;" name="ap_payment_settings[buyer_charge]" type="number" step="any" id="ap_payment_settings[buyer_charge]" value="<?= $settings['ap_payment_settings']['buyer_charge'] ?? 0 ?>" class="ltr">
                    <select style="max-width: 10%; vertical-align: top;" name="ap_payment_settings[buyer_charge_type]" id="ap_payment_settings[buyer_charge_type]" class="ltr small">
                        <option <?= isset($settings['ap_payment_settings']['buyer_charge_type']) && $settings['ap_payment_settings']['buyer_charge_type'] = '%' ? 'selected' : '' ?>>%</option>
                        <option <?= isset($settings['ap_payment_settings']['buyer_charge_type']) && $settings['ap_payment_settings']['buyer_charge_type'] = '€' ? 'selected' : '' ?>>€</option>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>

    <h2 class="title">PayPal Settings</h2>
    <p>Please collect the API credentials from the PayPal and place them below as asked.</p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="ap_payment_settings[paypal_environment]">Environment</label></th>
                <td>
                    <select name="ap_payment_settings[paypal_environment]" id="ap_payment_settings[paypal_environment]" class="regular-text ltr">
                        <option <?= isset($settings['ap_payment_settings']['paypal_environment']) && $settings['ap_payment_settings']['paypal_environment'] = 'Sandbox' ? 'selected' : '' ?> value="test">Sandbox</option>
                        <option <?= isset($settings['ap_payment_settings']['paypal_environment']) && $settings['ap_payment_settings']['paypal_environment'] = 'Live' ? 'selected' : '' ?> value="prod">Live</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ap_payment_settings[paypal_client_id]">Client ID</label></th>
                <td>
                    <input name="ap_payment_settings[paypal_client_id]" type="text" id="ap_payment_settings[paypal_client_id]" value="<?= $settings['ap_payment_settings']['paypal_client_id'] ?? '' ?>" placeholder="PayPal Client ID" class="regular-text ltr">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ap_payment_settings[client_secret]">Client Secret</label></th>
                <td>
                    <input name="ap_payment_settings[paypal_client_secret]" type="text" id="ap_payment_settings[paypal_client_secret]" value="<?= $settings['ap_payment_settings']['paypal_client_secret'] ?? '' ?>" placeholder="PayPal Client Secret" class="regular-text ltr">
                </td>
            </tr>
        </tbody>
    </table>

    <h2 class="title">Stripe Settings</h2>
    <p>Please collect the API credentials from the Stripe and place them below as asked.</p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="ap_payment_settings[stripe_client_secret]">Secret Key</label></th>
                <td>
                    <input name="ap_payment_settings[stripe_client_secret]" type="text" id="ap_payment_settings[stripe_client_secret]" value="<?= $settings['ap_payment_settings']['stripe_client_secret'] ?? '' ?>" placeholder="Stripe Secret Key" class="regular-text ltr">
                </td>
            </tr>
        </tbody>
    </table>

    <p class="submit"><button class="button button-primary">Save Changes</button></p>
</form>