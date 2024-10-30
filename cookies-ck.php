<?php
/**
 * Plugin Name: Cookies CK
 * Plugin URI: https://www.ceikay.com
 * Description: Cookies CK creates a popup to inform the visitor that the website uses cookies
 * Version: 1.1.0
 * Author: Cédric KEIFLIN
 * Author URI: https://www.ceikay.com
 * License: GPL2
 * Text Domain: cookies-ck
 * Domain Path: /language
 */

Namespace Cookiesck;

defined('ABSPATH') or die;

if (! defined('CK_LOADED')) define('CK_LOADED', 1);
if (! defined('COOKIESCK_PLATFORM')) define('COOKIESCK_PLATFORM', 'wordpress');
if (! defined('COOKIESCK_PATH')) define('COOKIESCK_PATH', dirname(__FILE__));
if (! defined('COOKIESCK_MEDIA_PATH')) define('COOKIESCK_MEDIA_PATH', COOKIESCK_PATH);
if (! defined('COOKIESCK_ADMIN_GENERAL_URL')) define('COOKIESCK_ADMIN_GENERAL_URL', admin_url('', 'relative') . 'options-general.php?page=cookies-ck');
if (! defined('COOKIESCK_MEDIA_URL')) define('COOKIESCK_MEDIA_URL', plugins_url('', __FILE__));
if (! defined('COOKIESCK_SITE_ROOT')) define('COOKIESCK_SITE_ROOT', ABSPATH);
if (! defined('COOKIESCK_URI_ROOT')) define('COOKIESCK_URI_ROOT', site_url());
if (! defined('COOKIESCK_URI_BASE')) define('COOKIESCK_URI_BASE', admin_url('', 'relative'));
if (! defined('COOKIESCK_VERSION')) define('COOKIESCK_VERSION', '1.1.0');
if (! defined('COOKIESCK_PLUGIN_NAME')) define('COOKIESCK_PLUGIN_NAME', 'cookies-ck');
if (! defined('COOKIESCK_SETTINGS_FIELD')) define('COOKIESCK_SETTINGS_FIELD', 'cookies-ck_options');
if (! defined('COOKIESCK_WEBSITE')) define('COOKIESCK_WEBSITE', 'http://www.ceikay.com/plugins/cookies-ck/');

class Cookiesck {

//	public $pluginname, $pluginurl, $plugindir, $settings_field, $options, $fields;
	public $default_settings = array(
		'readmorelink' => '#'
		);

	private static $instance;

	static function getInstance() { 
		if (!isset(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function admin_init() {
		register_setting(COOKIESCK_SETTINGS_FIELD, COOKIESCK_SETTINGS_FIELD);
	}

	public function admin_menu() {
		// add a new submenu to the standard Settings panel
		$this->pagehook = add_options_page(
		__('Cookies CK'), __('Cookies CK'), 
		'administrator', COOKIESCK_PLUGIN_NAME, array($this,'render_options') );

		// load the assets for the admin plugin page only
		add_action( 'admin_head-' . $this->pagehook, array($this, 'load_admin_assets'));
	}

	public function load_admin_assets() {
		wp_enqueue_style( 'ckframework', COOKIESCK_MEDIA_URL . '/assets/ckframework.css' );
		wp_enqueue_style( 'cookiesck-admin', COOKIESCK_MEDIA_URL . '/assets/admin.css' );
	}

	public function get_value( $key, $default = null, $allowEmpty = false ) {
		if (isset($this->options[$key]) && ($allowEmpty == true || ($allowEmpty == false && $this->options[$key] !== ''))) {
			return $this->options[$key];
		} else {
			if ($default == null && isset($this->default_settings[$key])) 
				return $this->default_settings[$key];
		}
		return $default;
	}

	public function render_options() {
	// set the entry in the database options table if not exists
	add_option(COOKIESCK_SETTINGS_FIELD, $this->default_settings );

	$this->options = get_option(COOKIESCK_SETTINGS_FIELD);
	?>
	<div id="ckoptionswrapper" class="ckinterface">
		<a href="<?php echo COOKIESCK_WEBSITE ?>" target="_blank" style="text-decoration:none;"><img src="<?php echo COOKIESCK_MEDIA_URL ?>/images/logo_cookiesck_64.png" style="margin: 5px;" class="cklogo" /><span class="cktitle">Cookies CK</span></a>
		<div style="clear:both;"></div>
		<?php //$this->show_message(); ?>
		<form method="post" action="options.php">
			<div class="metabox-holder">
				<div class="postbox-container" style="width: 99%;">
					<div>
						<label for="readmorelink"><?php _e('Readmore Link', 'cookies-ck'); ?></label>
						<img class="ckicon" src="<?php echo COOKIESCK_MEDIA_URL ?>/images/link.png" />
						<input type="text" name="cookies-ck_options[readmorelink]" id="readmorelink" value="<?php echo $this->get_value('readmorelink') ?>"/>
					</div>
					<div>
						<label for="messagetext"><?php _e('Message text', 'cookies-ck'); ?></label>
						<img class="ckicon" src="<?php echo COOKIESCK_MEDIA_URL ?>/images/text_signature.png" />
						<input type="text" name="cookies-ck_options[messagetext]" id="messagetext" value="<?php echo $this->get_value('messagetext') ?>"/>
						<span class="ckdesc"><?php _e('Default text') ?> : "By visiting our website you agree that we are using cookies to ensure you to get the best experience."</span>
					</div>
				</div>
				<div style="clear:both;"></div>
			</div>
			<div style="margin: 5px 0;">
				<input type="submit" class="button button-primary" name="save_options" value="<?php _e('Save Settings', 'cookies-ck'); ?>" />
			</div>
			<?php
			settings_fields(COOKIESCK_SETTINGS_FIELD);
			?>
		</form>
		<?php echo $this->copyright(); ?>
	</div>
	<?php }

	public function copyright() {
		$html = array();
		$html[] = '<hr style="margin:10px 0;clear:both;" />';
		$html[] = '<div class="ckpoweredby"><a href="https://www.ceikay.com" target="_blank">https://www.ceikay.com</a></div>';
		$html[] = '<div class="ckproversioninfo"><div class="ckproversioninfo-title"><a href="' . COOKIESCK_WEBSITE . '" target="_blank">' . __('Get the Pro version', 'cookies-ck') . '</a></div>
		<div class="ckproversioninfo-content">
			
<p>Multiple positions</p>
<p>Custom cookie duration</p>
<p>Custom duration</p>
<p>Read more attributes</p>
<p>Styling interface</p>
<div class="ckproversioninfo-button"><a href="' . COOKIESCK_WEBSITE . '" target="_blank">' . __('Get the Pro version', 'cookies-ck') . '</a></div>
		</div>';

		return implode($html);
	}

	function load_textdomain() {
		load_plugin_textdomain( 'cookies-ck', false, dirname( plugin_basename( __FILE__ ) ) . '/language/'  );
	}

	function load_assets_files() {
		wp_enqueue_script( 'jquery' );
	}

	function load_assets() {
		$this->options = get_option(COOKIESCK_SETTINGS_FIELD);
		// create the JS to manage the action
		$readmorelink = $this->get_value('readmorelink', '#');
		$messagetext = $this->get_value('messagetext', 'By visiting our website you agree that we are using cookies to ensure you to get the best experience.');
		$linkrel = '';
		$js = '	function cookiesckSetCookie(c_name,value,exdays)
				{
					var exdate=new Date();
					exdate.setDate(exdate.getDate() + exdays);
					var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString()) + "; path=/";
					document.cookie=c_name + "=" + c_value;
				}

				function cookiesckReadCookie(name) {
					var nameEQ = name + "=";
					var cooks = document.cookie.split(\';\');
					for(var i=0;i < cooks.length;i++) {
						var c = cooks[i];
						while (c.charAt(0)==\' \') c = c.substring(1,c.length);
							if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
						}
					return null;
				}
				jQuery(document).ready(function($){
					// cookiesckSetCookie("cookiesck","no",365);
					var cookiesck = cookiesckReadCookie(\'cookiesck\');
					if (cookiesck != "yes"){
						$("body").append("<div id=\"cookiesck\"></div>");
						$("#cookiesck").append("<span class=\"cookiesck_inner\">'.__($messagetext, 'cookies-ck').'</span>")
							' . ($readmorelink ? '.append("<a href=\"' . $readmorelink . '\" ' . $linkrel . ' id=\"cookiesck_readmore\">'.__('Read more', 'cookies-ck').'</a>")' : '') . '
							.append("<div id=\"cookiesck_accept\">'.__('I understand !', 'cookies-ck').'</div>")
							.append("<div style=\"clear:both;\"></div>")
							.show();

						jQuery(\'#cookiesck_accept\').click(function(){
							cookiesckSetCookie("cookiesck","yes",365);
							jQuery.post(document.location, \'set_cookie=1\', function(){});
							jQuery(\'#cookiesck\').slideUp(\'slow\');
						});
					}
				});
		';
		?>
		<script type="text/javascript"> <!--
		<?php echo $js; ?>
		//--> </script>
		<?php
		// add styling
		$css = "
			#cookiesck {
				position: fixed;
				left:0;
				right: 0;
				bottom: 0;
				z-index: 999;
				min-height: 30px;
				color: #eee;
				background: rgba(0,0,0,0.5);
				border-top: 1px solid #666;
				text-align: center;
				font-size: 14px;
				line-height: 14px;
			}
			#cookiesck .cookiesck_inner {
				padding: 10px 0;
				display: inline-block;
			}
			#cookiesck_readmore {
				float:right;
				padding:10px;
				border-radius: 3px;
				color: #ddd;
			}
			#cookiesck_readmore:hover {
				color: red;
			}
			#cookiesck_accept{
				float:right;
				padding:10px;
				margin: 1px;
				border-radius: 3px;
				background: #000;
				cursor: pointer;
				-webkit-transition: all 0.2s;
				transition: all 0.2s;
			}
			
			#cookiesck_accept:hover{
				background: green;
			}
		";
		?>
		<style type="text/css">
		<?php echo $css; ?>
		</style>
		<?php
	}
}

// force cookies alert to not be shown for special popups/views
if (isset($_GET['nocookies']) && $_GET['nocookies'] === '1') {
	return;
} else {
	$Cookiesck = Cookiesck::getInstance();
	add_action('wp_enqueue_scripts', array($Cookiesck, 'load_assets_files'));

	add_action('wp_footer', array($Cookiesck, 'load_assets'));
}

// load the process
$Cookiesck = Cookiesck::getInstance();

// load the translation
add_action('plugins_loaded', array($Cookiesck, 'load_textdomain'));

add_action('admin_init', array($Cookiesck, 'admin_init'));

add_action('admin_menu', array($Cookiesck, 'admin_menu' ), 20);

