
<style type="text/css">
    .nav{
        display: none;
    }
    #header_logo{
        float:none;
        text-align: center;
    }
</style>



<div class="login_wrap">
    <div class="final_content_wrap">

        <?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 6525 ae190c43422d2b2a7c6934e00e8b8686
  * Envato: 82b7b3b1-2e56-457f-aac2-ff4d410e1e54
  * Package Date: 2014-10-02 15:05:08 
  * IP Address: 203.125.187.204
  */ if(isset($_REQUEST['reset'])){ ?>
        <h2><?php echo _l('Forgot password'); ?> </h2>
        <p><?php echo _l('Please enter your email address below to reset your password.'); ?></p>


        <form action="" method="post">
            <input type="hidden" name="_process_reset" value="true">
            <table width="100%" class="tableclass">
                <tr>
                    <th class="width1">
                        <label for="email"><?php echo _l('Email'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="email" name="email" value="<?php echo (defined('_DEMO_MODE') && _DEMO_MODE)?'admin@example.com':''; ?>" style="width:185px;" />
                    </td>
                </tr>
                <?php if(class_exists('module_captcha',false)){ ?>
                <tr>
                    <th>
                    </th>
                    <td>
                        <?php echo module_captcha::display_captcha_form(); ?>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <th>

                    </th>
                    <td>
                        <input type="submit" class="submit_button" name="reset" value="<?php echo _l('Reset Password'); ?>">
                    </td>
                </tr>
            </table>
            <?php hook_handle_callback('forgot_password_screen'); ?>
        </form>
        <?php }else{ ?>

	<h2><?php echo _l('Please Login'); ?> </h2>
	<p align="center"><?php echo _l('Welcome to %s - Please Login Below',module_config::s('admin_system_name')); ?></p>




      <table align="center">
        <tr>
            <td width="65" valign="top">
            <img src="<?php echo _BASE_HREF;?>images/lock.png" alt="lock" />
            </td>
            <td>
                <form action="" method="post">
                    <input type="hidden" name="_process_login" value="true">
                    <table width="100%" class="tableclass">
                        <tr>
                            <th class="width1">
                                <label for="email"><?php echo _l('Username'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="email" name="email" value="<?php echo (defined('_DEMO_MODE') && _DEMO_MODE)?'admin@example.com':''; ?>" style="width:185px;" />
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="password"><?php echo _l('Password'); ?></label>
                            </th>
                            <td>
                                <input type="password" name="password" id="password" value="<?php echo (defined('_DEMO_MODE') && _DEMO_MODE)?'password':''; ?>" style="width:185px;" />
                            </td>
                        </tr>
                        <?php if(module_config::c('login_recaptcha',0)){ ?>
                        <tr>
                            <td colspan="2">
                                <?php echo module_captcha::display_captcha_form(); ?>
                            </td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="2" align="center">
                                <p>
                                    <input type="submit" class="submit_button" name="login" value="<?php echo _l('Login'); ?><?php echo (defined('_DEMO_MODE') && _DEMO_MODE)?' to demo':''; ?>">
                                </p>
                                <p>
                                    <a href="?reset"><?php _e('Forgot Password?');?></a>
                                </p>
                            </td>
                        </tr>
                    </table>
                    <?php hook_handle_callback('login_screen'); ?>
                </form>
            </td>
        </tr>
      </table>

        <script type="text/javascript">
            $(function(){
                $('#email')[0].focus();
                setTimeout(function(){
                    if($('#email').val() != ''){
                        $('#password')[0].focus();
                    }
                },100);
            });
        </script>
    </div>

    <?php } ?>
</div>
