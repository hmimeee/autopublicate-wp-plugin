<div class="page-body pb-3">
    <div class="sub-title">
        <h2>Wallet</h2>
    </div>


    <div class="content-page">
        <form method="post" action="<?= ap_route('wallet.payout-request') ?>">
            <?php wp_nonce_field(); ?>
            <div class="row ps-5 pe-5">
                <label class="fw-bold pb-2">Choose Payout Method:</label>
                <div class="form-group col-md-6">
                    <div class="gateway-box">
                        <input class="gateway-input payout-method" id="bank" type="radio" name="method" value="bank">
                        <label class="gateway-label" for="bank">Bank Transfer</label>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <div class="gateway-box">
                        <input class="gateway-input payout-method" id="paypal" type="radio" name="method" value="paypal">
                        <label class="gateway-label" for="paypal">PayPal</label>
                    </div>
                </div>
            </div>

            <div class="row ps-5 pe-5 d-none" id="paypal-box">
                <div class="form-group col-md-6">
                    <label>PayPal Holder's Name</label>
                    <input type="text" name="paypal_holder" class="form-control" placeholder="Enter Holder's Name" />
                </div>
                <div class="form-group col-md-6">
                    <label>PayPal Email</label>
                    <input type="email" name="paypal_email" class="form-control" placeholder="Enter Account Email" />
                </div>
            </div>

            <div class="row ps-5 pe-5 d-none" id="bank-box">
                <div class="form-group col-md-6">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" placeholder="Enter Bank Name" />
                </div>

                <div class="form-group col-md-6">
                    <label>Account Type</label>
                    <select name="bank_type" class="form-control">
                        <option>Choose Account Type</option>
                        <option value="individual">Individual</option>
                        <option value="business">Business</option>
                    </select>
                </div>

                <div class="form-group col-md-12">
                    <label>Account Holder's Name</label>
                    <input type="text" name="bank_holder" class="form-control" placeholder="Enter Account Holder's Name" />
                </div>

                <div class="form-group col-md-6">
                    <label>Routing Number</label>
                    <input type="number" name="bank_routing" class="form-control" placeholder="Enter Routing Number" />
                </div>

                <div class="form-group col-md-6">
                    <label>Account Number</label>
                    <input type="number" name="bank_account" class="form-control" placeholder="Enter Account Number" />
                </div>
            </div>

            <div class="row ps-5 pe-5">
                <div class="form-group col-md-12 d-flex justify-content-end">
                    <button class="btn btn-primary">Submit Request</button>
                </div>
            </div>
        </form>
    </div>
</div>