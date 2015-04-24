<div class="file_<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 6525 ae190c43422d2b2a7c6934e00e8b8686
  * Envato: 82b7b3b1-2e56-457f-aac2-ff4d410e1e54
  * Package Date: 2014-10-02 15:05:08 
  * IP Address: 203.125.187.204
  */ echo $owner_table;?>_<?php echo $owner_id;?>">
    <?php
    foreach($file_items as $file_item){
        $file_item = self::get_file($file_item['file_id']);
        ?>
        
        <div style="width:110px; height:90px; overflow:hidden; ">

            <?php
            // /display a thumb if its supported.
            if(preg_match('/\.(\w\w\w\w?)$/',$file_item['file_name'],$matches)){
                switch(strtolower($matches[1])){
                    case 'jpg':
                    case 'jpeg':
                    case 'gif':
                    case 'png':
                        ?>
                            <img src="<?php echo _BASE_HREF . nl2br(htmlspecialchars($file_item['file_path']));?>" width="100" alt="preview" border="0">
                        <?php
                        break;
                    default:
                        echo 'n/a';
                }
            }
            ?>
        </div>
    <?php
    }
    ?>
</div>

