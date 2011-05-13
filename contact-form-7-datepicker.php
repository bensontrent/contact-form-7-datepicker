<?php
/*
Plugin Name: Contact Form 7 Datepicker
Plugin URI: 
Description: Javascript Calendar based on jsdatepick script
Author: Aurel Canciu
Version: 0.1
Author URI: 
*/

define('CF7_DATE_PICKER_VERSION', '0.1');

function DatePickerPluginActivate(){
	global $wpdb, $blog_id;
	$table = $wpdb->prefix."options";
	$query = "INSERT INTO $table (blog_id, option_name, option_value)
				VALUES (".$blog_id.",'cf7datepicker','1;true;false;beige;%m-%d-%Y;1;ltr') ";
	$result = $wpdb->query( $query );
}
	
function DatePickerPluginDeactivate(){
	global $wpdb, $blog_id;
	$table = $wpdb->prefix."options";
		
	$query = " DELETE FROM $table WHERE blog_id=".$blog_id." AND option_name='cf7datepicker' ";
	$result = $wpdb->query( $query );
		
}

function DatePickerLoadSettings(){
	global $wpdb, $blog_id;
	$table = $wpdb->prefix."options";
	return $wpdb->get_row( "SELECT * FROM $table WHERE blog_id=".$blog_id." AND option_name='cf7datepicker' ");
}
	
function DatePickerUpdateSettings($dataupdate){
	global $wpdb, $blog_id;
	$table = $wpdb->prefix."options";
	$query = " UPDATE $table SET option_value = '".$dataupdate[0].";".$dataupdate[1].";".$dataupdate[2].";".$dataupdate[3].";".$dataupdate[4].";".$dataupdate[5].";".$dataupdate[6]."'  WHERE blog_id=".$blog_id." AND option_name='cf7datepicker' ";
	$result = $wpdb->query( $query );
}
	
function DatePickerRegAdminSettings() {
	if (function_exists('add_submenu_page')) {
		add_submenu_page('wpcf7',__('Datepicker Settings', 'contact-form-7-datepicker'),__('Datepicker Settings', 'contact-form-7-datepicker'),
                         'edit_themes',
                         basename(__FILE__),
                         'DatePickerAdminSettingsHTML');
	}	
}
	
function DatePickerReadScheme() {
	$path = ABSPATH.'/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/img/';
	if ($handle = opendir($path)) {
		$themes = array() ;
		while (false !== ($file = readdir($handle))) {
			if (is_dir($path.$file) && $file != "." && $file != "..") {
				array_push($themes, $file);
			}
		}
	}
	closedir($handle);
	return $themes;
}
	
function DatePickerGetSchemeImages($scheme) {
	$path = ABSPATH.'/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/img/'.$scheme.'/';
	if ($handle = opendir($path)) {
		$schemeimg = array();
		while (false !== ($file = readdir($handle))) {
			if (is_file($path.$file) && preg_match('/\.gif$/i', $file)) {
				array_push($schemeimg, '/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/img/'.$scheme.'/'.$file);
			}
		}
	}
	closedir($handle);
	return $schemeimg;
}
	
function DatePickerAdminSettingsHTML() {
	if(isset($_POST['datepickersave'])) {
		$dataupdate = array($_POST['useMode'], $_POST['isStripped'], $_POST['limitToToday'], $_POST['cellColorScheme'], $_POST['dateFormat'], $_POST['weekStartDay'], $_POST['directionality']);
		DatePickerUpdateSettings($dataupdate);
	}
		
	$loadsetting = DatePickerLoadSettings();
	$setting = explode(";",$loadsetting->option_value);
	$useMode = array(1,2);
	$limitToToday = $isStripped = array('true','false');
	$cellColorScheme = DatePickerReadScheme();
	$weekStartDay = array(__('Sunday', 'contact-form-7-datepicker'),__('Monday', 'contact-form-7-datepicker'));
	$directionality = array(__('Left to right', 'contact-form-7-datepicker'),__('Right to left', 'contact-form-7-datepicker'));
	
	?>
        <div class="wrap"> 
            <h2>Contact Form 7 Datepicker</h2>
         
        <form method="post"> 
         
            <table class="widefat"> 
                <tbody> 
                    <tr> 
						<th style="width:20%">
							<label><?php echo __('Color scheme', 'contact-form-7-datepicker'); ?></label>
						</th>
						<td colspan="2">
						<?php
						foreach($cellColorScheme as $scheme) {
							if($scheme==$setting[3])
								$checked = "checked='checked'";
							else
								$checked = ""; ?>
							<div style="float: left; display: block; width: 150px; margin: 0 50px 50px 0;">
								<div style="float: left;"><?php
								foreach(DatePickerGetSchemeImages($scheme) as $img) { ?>
									<img src="<?php echo get_option('siteurl') . $img; ?>" style="padding: 2px; background: #fff; border: 1px solid #ccc; margin: 5px; " /><br />
								<?php } ?>
								</div>
								<div style="float: right; vertical-align: middle;">
									<input name="cellColorScheme" type="radio" value="<?php echo $scheme; ?>" <?php echo $checked; ?> /><label><?php echo $scheme; ?></label>
								</div>
							</div>
						<?php } ?>
						</td>
					</tr>
                    
					<tr> 
						<th>
							<label><?php echo __('Use Mode', 'contact-form-7-datepicker'); ?></label>
						</th>
						<td>
						<select name="useMode">
						<?php
						foreach($useMode as $row) {
							if($row==$setting[0])
								$selected = "selected";
							else
								$selected = "";
							echo "<option value='".$row."' ".$selected." >".$row."</option>";
						} ?>
						</select>
						</td>
						<td>
							<?php echo __('<p>1 – The calendar\'s HTML will be directly appended to the field supplied by target<br />
							2 – The calendar will appear as a popup when the field with the id supplied in target is clicked.</p>', 'contact-form-7-datepicker'); ?>
						</td>
					</tr>
					
					<tr> 
						<th>
							<label><?php echo __('Sripped', 'contact-form-7-datepicker'); ?></label>
						</th>
						<td>
						<select name="isStripped">
						<?php
						foreach($isStripped as $row) {
							if($row==$setting[1])
								$selected = "selected";
							else
								$selected = "";
							echo "<option value='".$row."' ".$selected." >".$row."</option>";
						} ?>
						</select>
						</td>
						<td>
							<?php echo __('<p>When set to true the calendar appears without the visual design - usually used with \'Use Mod\' 1.</p>','contact-form-7-datepicker'); ?>
						</td>
					</tr>
					
					<tr> 
						<th>
							<label><?php echo __('Limit To Today', 'contact-form-7-datepicker'); ?></label>
						</th>
						<td>
						<select name="limitToToday">
						<?php
						foreach($limitToToday as $row) {
							if($row==$setting[2])
								$selected = "selected";
							else
								$selected = "";
							echo "<option value='".$row."' ".$selected." >".__($row,'contact-form-7-datepicker')."</option>";
						} ?>
						</select>
						</td>
						<td>
							<?php echo __('<p>Enables you to limit the possible picking days to today\'s date.</p>','contact-form-7-datepicker'); ?>
						</td>
					</tr>
							
					<tr> 
						<th>
							<label><?php echo __('Week Start Day', 'contact-form-7-datepicker'); ?></h2></label>
						</th>
						<td>
						<select name="weekStartDay">
						<?php
						foreach($weekStartDay as $row) {
							if ($row == __('Sunday','contact-form-7-datepicker'))
								$val = 0;
							else
								$val = 1;
							if($val == $setting[5])
								$selected = "selected";
							else
								$selected = "";
							echo "<option value='".$val."' ".$selected." >".__($row,'contact-form-7-datepicker')."</option>";
						} ?>
						</select>
						</td>
						<td></td>
					</tr>
					
					<tr> 
						<th>
							<label><?php echo __('Text Direction', 'contact-form-7-datepicker'); ?></h2></label>
						</th>
						<td>
						<select name="directionality">
						<?php
						foreach($directionality as $row) {
							if ($row == __('Left to right','contact-form-7-datepicker'))
								$val = "ltr";
							else
								$val = "rtl";
							if($val == $setting[6])
								$selected = "selected";
							else
								$selected = "";
							echo "<option value='".$val."' ".$selected." >".__($row,'contact-form-7-datepicker')."</option>";
						} ?>
						</select>
						</td>
						<td></td>
					</tr>		
					
					<tr> 
						<th>
							<label><?php echo __('Date Format', 'contact-form-7-datepicker'); ?></label>
						</th>
						<td>
							<input name="dateFormat" id="dateFormat" type="text" value="<?php echo $setting[4]; ?>" />
						</td>
						<td>
<?php echo __('<p>Possible values to use in the date format:<br />
<br />
%d - Day of the month, 2 digits with leading zeros<br />
%j - Day of the month without leading zeros<br />
%m - Numeric representation of a month, with leading zeros<br />
%M - A short textual representation of a month, three letters<br />
%n - Numeric representation of a month, without leading zeros<br />
%F - A full textual representation of a month, such as January or March<br />
%Y - A full numeric representation of a year, 4 digits<br />
%y - A two digit representation of a year<br />
<br />
You can of course put whatever divider you want between them.<br /></p>', 
'contact-form-7-datepicker'); ?>
						</td>
					</tr>
					<tr> 
						<td></td>
						<td>
							<input name="datepickersave" id="datepickersave" type="submit" value="<?php echo __('Save Setting', 'contact-form-7-datepicker'); ?>" class="button" />
						</td>
						<td></td>
                    </tr>
                 </tbody>
            </table>
       </form>
        
    <?php
}
	
function DatePickerLoads(){
	$loadsetting = DatePickerLoadSettings();
	$setting = explode(";",$loadsetting->option_value);
	
	if( is_admin() )
		return; ?>
	<link rel="stylesheet" type="text/css" href="<?php echo plugins_url( '/css/jsDatePick_'.(($setting[6] != "") ? $setting[6] : "ltr").'.min.css', __FILE__ ); ?>" />
	<script type="text/javascript" src="<?php echo plugins_url( '/js/jsDatePick.jquery.min.1.3.js', __FILE__ ); ?>"></script><?php
}
	
function page_text_filter($content) {
	$regex = '/\[datepicker\s(.*?)\]/';
	return preg_replace_callback($regex, 'page_text_filter_callback', $content);
}

function page_text_filter_callback($matches) {
	$loadsetting = DatePickerLoadSettings();
	$setting = explode(";",$loadsetting->option_value);
			
	$string = "<input type=\"text\" name=\"".$matches[1]."\" id=\"".$matches[1]."\" />
	<script type=\"text/javascript\"> 
		jQuery(document).ready(function() {
			DatePicker_".$matches[1]." = new JsDatePick({
				useMode:".$setting[0].",
				isStripped:".$setting[1].",
				target:\"".$matches[1]."\",
				limitToToday:".$setting[2].",
				cellColorScheme:\"".$setting[3]."\",
				dateFormat:\"".$setting[4]."\",
				imgPath:\"".plugins_url( '/img/'.$setting[3].'/', __FILE__ )."\",
				weekStartDay:".$setting[5].",
				directionality:\"".$setting[6]."\"
			});
		});
	</script>";
	return($string);
}

function wpfc7_DatePickerShortcodeHandler( $tag ) {
	global $wpcf7_contact_form;

	if ( ! is_array( $tag ) )
		return '';

	$type = $tag['type'];
	$name = $tag['name'];
	$options = (array) $tag['options'];
	$values = (array) $tag['values'];
	
	if ( empty( $name ) )
		return '';

	$atts = '';
	$id_att = '';
	$class_att = '';
	$size_att = '';
	$maxlength_att = '';

	if ( 'date*' == $type )
		$class_att .= ' wpcf7-validates-as-required';

	foreach ( $options as $option ) {
		if ( preg_match( '%^id:([-0-9a-zA-Z_]+)$%', $option, $matches ) ) {
			$id_att = $matches[1];

		} elseif ( preg_match( '%^class:([-0-9a-zA-Z_]+)$%', $option, $matches ) ) {
			$class_att .= ' ' . $matches[1];

		} elseif ( preg_match( '%^([0-9]*)[/x]([0-9]*)$%', $option, $matches ) ) {
			$size_att = (int) $matches[1];
			$maxlength_att = (int) $matches[2];
		}
	}

	if ( $id_att )
		$atts .= ' id="' . trim( $id_att ) . '"';

	if ( $class_att )
		$atts .= ' class="' . trim( $class_att ) . '"';

	if ( $size_att )
		$atts .= ' size="' . $size_att . '"';
	else
		$atts .= ' size="40"'; // default size

	if ( $maxlength_att )
		$atts .= ' maxlength="' . $maxlength_att . '"';

	// Value
	if ( is_a( $wpcf7_contact_form, 'WPCF7_ContactForm' ) && $wpcf7_contact_form->is_posted() ) {
		if ( isset( $_POST['_wpcf7_mail_sent'] ) && $_POST['_wpcf7_mail_sent']['ok'] )
			$value = '';
		else
			$value = $_POST[$name];
	} else {
		$value = $values[0];
	}

	$html = page_text_filter_callback(array('',$name));
	$validation_error = '';
	if ( is_a( $wpcf7_contact_form, 'WPCF7_ContactForm' ) )
		$validation_error = $wpcf7_contact_form->validation_error( $name );

	$html = '<span class="wpcf7-form-control-wrap ' . $name . '">' . str_replace('<p>','',$html) . $validation_error . '</span>';

	return $html;
}

if ( ! function_exists( 'wpcf7_add_shortcode' ) ) {
	if( is_file( WP_PLUGIN_DIR."/contact-form-7/includes/shortcodes.php" ) ) {
		include WP_PLUGIN_DIR."/contact-form-7/includes/shortcodes.php";
		wpcf7_add_shortcode( 'date', 'wpfc7_DatePickerShortcodeHandler', true );
		wpcf7_add_shortcode( 'date*', 'wpfc7_DatePickerShortcodeHandler', true );
	}
}

/* Validation filter */

function wpcf7_DatePickerValidationFilter( $result, $tag ) {
	global $wpcf7_contact_form;

	$type = $tag['type'];
	$name = $tag['name'];

	$_POST[$name] = trim( strtr( (string) $_POST[$name], "\n", " " ) );

	if ( 'date*' == $type ) {
		if ( '' == $_POST[$name] ) {
			$result['valid'] = false;
			$result['reason'][$name] = $wpcf7_contact_form->message( 'invalid_required' );
		}
	}

	return $result;
}

register_activation_hook( __FILE__, 'DatePickerPluginActivate' );
register_deactivation_hook( __FILE__, 'DatePickerPluginDeactivate' );

add_action('admin_menu', 'DatePickerRegAdminSettings');
add_action('wp_head', 'DatePickerLoads', 1002);

add_filter( 'wpcf7_validate_date', 'wpcf7_DatePickerValidationFilter', 10, 2 );
add_filter( 'wpcf7_validate_date*', 'wpcf7_DatePickerValidationFilter', 10, 2 );

/* L10N */

add_action( 'init', 'DatePickerLoadPluginTextDomain' );

function DatePickerLoadPluginTextDomain() {
	load_plugin_textdomain( 'contact-form-7-datepicker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

?>