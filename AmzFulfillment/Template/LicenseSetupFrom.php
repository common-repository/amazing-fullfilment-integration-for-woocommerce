<p><?php echo __("License Key"); ?></p>
<input type="text" id="key" name="key" value="<?php if(isset($_REQUEST['key'])) echo $_REQUEST['key']; ?>" placeholder="" />

<p><?php echo __("Activation Email Address"); ?></p>
<input type="text" id="email" name="email" value="<?php if(isset($_REQUEST['email'])) echo $_REQUEST['email']; ?>" placeholder="" />

<input type="submit" name="<?php echo AmzFulfillment_Panel_Tab_License::ACTIVATE_ACTION; ?>" value="<?php echo __("Activate"); ?>" class="button button-primary" />
