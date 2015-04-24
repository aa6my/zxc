<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 6525 ae190c43422d2b2a7c6934e00e8b8686
  * Envato: 82b7b3b1-2e56-457f-aac2-ff4d410e1e54
  * Package Date: 2014-10-02 15:05:08 
  * IP Address: 203.125.187.204
  */
switch($display_mode){
    case 'iframe':
        ?>
         </div> <!-- end .inner -->
         </div> <!-- end .outer -->
         </div> <!-- end .content -->
        </body>
        </html>
        <?php
        module_debug::push_to_parent();
        break;
    case 'ajax':

        break;
    case 'normal':
    default:
        ?>

         </div> <!-- end .inner -->
         </div> <!-- end .outer -->
         </div> <!-- end .content -->

        </div>
        <!-- /#wrap -->


        <div id="footer">
            <p>&copy; <?php echo module_config::s('admin_system_name','Ultimate Client Manager'); ?>
              - <?php echo date("Y"); ?>
              - Version: <?php echo module_config::current_version(); ?>
              - Time: <?php echo round(microtime(true)-$start_time,5);?>
            </p>
        </div>

        </body>
        </html>
        <?php
        break;
}