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

if(!module_config::can_i('view','Settings')){
    redirect_browser(_BASE_HREF);
}
if(!module_config::can_i('edit','Settings')){
    redirect_browser(_BASE_HREF);
}
if(!module_language::can_i('edit','Language')){
    redirect_browser(_BASE_HREF);
}

$module->page_title = _l('Language');

if(isset($_REQUEST['language_id'])) {

	include(module_theme::include_ucm('includes/plugin_language/pages/language_translations.php'));

}else{

	print_heading( array(
		'title'  => 'Language and Translations',
		'type'   => 'h2',
		'main'   => true,
		'button' => array(
			'url'   => htmlspecialchars( $_SERVER['REQUEST_URI'] ) . ( strpos( $_SERVER['REQUEST_URI'], '?' ) === false ? '?' : '&' ) . 'language_id=new',
			'title' => 'Add New Language',
			'type'  => 'add',
		),
	) );

	$languages_attributes = array();

	?>

	<form action="" method="post">

		<table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_rows">
			<thead>
			<tr class="title">
				<th><?php echo _l( 'Language Code' ); ?></th>
				<th><?php echo _l( 'Language Name' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
			$c = 0;
			foreach ( module_language::get_languages_attributes() as $language_code => $language) {
				if(!isset($language['language_id'])||!$language['language_id']){
					echo "Language $language_code failed to update insert into database.. Please report this bug to Support.";
					continue;
				}
				$languages_attributes[$language_code] = $language['language_name'];
				?>
				<tr class="<?php echo ( $c ++ % 2 ) ? "odd" : "even"; ?>">
					<td class="row_action">
						<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']) . ( strpos( $_SERVER['REQUEST_URI'], '?' ) === false ? '?' : '&' ); ?>language_id=<?php echo $language['language_id']; ?>"><?php echo htmlspecialchars( $language['language_code'] ); ?></a>
						<?php if ( $language['language_code'] == module_config::c( 'default_language', 1 ) ) {
							_e( '(default)' );
						} ?>
					</td>
					<td>
						<?php echo htmlspecialchars( $language['language_name'] ); ?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</form>
    <form action="" method="post" id="language_reset">
        <?php module_form::print_form_auth();?>
        <input type="hidden" name="_process" value="language_reset">
        <input type="hidden" name="really" value="yes">
    </form>
    <form action="" method="post" id="language_duplicate">
        <?php module_form::print_form_auth();?>
        <input type="hidden" name="_process" value="language_duplicate_remove">
        <input type="hidden" name="really" value="yep">
    </form>

	<?php

	$settings = array(
		array(
			'key'         => 'default_language',
			'default'     => 'en',
			'type'        => 'select',
			'description' => 'Default language to use throughout the system',
			'options'     => $languages_attributes,
		),
		array(
			'key'         => 'clear_language',
			'type'        => 'html',
			'description' => 'Reset the language database/translations',
			'html'     => '<a href="#" onclick="if(confirm(\''._l('Really reset the language database?').'\'))$(\'#language_reset\')[0].submit();" class="error_text">Reset All</a>',
            'help' => 'This will clear ALL translations from the system. Make a backup first!'
		),
		array(
			'key'         => 'remove_duplicates',
			'type'        => 'html',
			'description' => 'Remove duplicate words',
			'html'     => '<a href="#" onclick="if(confirm(\''._l('Really remove any duplicate words from the database?').'\'))$(\'#language_duplicate\')[0].submit();">Remove Duplicate</a>',
            'help' => 'If you have duplicate words in the translation list click this button to remove them. Make a backup first!'
		),
	);

	module_config::print_settings_form(
		$settings
	);
}