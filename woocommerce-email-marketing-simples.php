<?php
/*
 * Plugin Name: WooCommerce Email Marketing Simples
 * Plugin URI: 
 * Description: Envie e-mail marketing de forma simples
 * Version:  1.10
 * Author: @rickslayer
 * Author URI:https://www.github.com/rickslayer
 * Developer: Paulo Ricardo Almeida
 * Developer URI: https://www.github.com/rickslayer
 * Text Domain: rickslayer
 * License: GPLv2
 *
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License, version 2, as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
 
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Constants
 */
if(!defined('WEMS_URL')) {
    define('WEMS_URL', plugin_dir_url(__FILE__));
}
if(!defined('WEMS_PATH')) {
	define('WEMS_PATH', plugin_dir_path( __FILE__ ));
}

require_once('woocommerce-email-marketing-simples-data.php');
require_once('wems-config.php');

class woocomerce_email_marketing_simples
{
    private $table_success;
    private $wems_data;

    public function __construct()
    {
        $this->wems_data = new WEMSData();
    
        $token= $this->wems_getData()['wems_token'];
        
        validateToken::check($token);

        $this->wems_checkWoocommerceActive();

        register_activation_hook(__FILE__,array( $this, 'criandoTabelas'));
      
        add_action( 'admin_menu', array( $this, 'wems_resgister_submenu_page' ) );
        
        new WEMSConfig();

        add_action( 'admin_init', array($this, 'wems_settings_fields'));
        add_action( 'admin_enqueue_scripts', array($this, 'wemsAddScripts'));
        add_action( 'wp_ajax_buscaClientes', array( $this, 'buscaClientes' ) );
        add_action( 'wp_ajax_admin_buscaClientes', array( $this, 'buscaClientes' ) );
        add_action( 'wp_ajax_enviaEmails', array( $this, 'enviaEmails' ) );
        add_action( 'wp_ajax_admin_enviaEmails', array( $this, 'enviaEmails' ) ); 
        add_action( 'wp_ajax_getEmailData', array( $this, 'getEmailData' ) );
        add_action( 'wp_ajax_admin_getEmailData', array( $this, 'getEmailData' ) ); 
        add_action( 'wp_ajax_enviaEmailsTeste', array( $this, 'enviaEmailsTeste' ) );
        add_action( 'wp_ajax_admin_enviaEmailsTeste', array( $this, 'enviaEmailsTeste' ) ); 
        add_action( 'wp_ajax_check_smtp_config', array( $this, 'check_smtp_config' ) );
        add_action( 'wp_ajax_admin_check_smtp_config', array( $this, 'check_smtp_config' ) ); 
        
    }
    /**
     * Função responsável por verificar se o Woocommerce está ativo
     */
    private function wems_checkWoocommerceActive()
    {
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {    
	 	    
        } else {
            add_action( 'admin_notices', array($this,'woocommerce_fallback_notice'  ));
        
        }

    }

    public function woocommerce_fallback_notice() {
        echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Email Marketing Simples depende do %s para funcionar!', 'woocommerce-email-marketing-simples' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">' . __( 'WooCommerce', 'woocommerce-email-marketing-simples' ) . '</a>' ) . '</p></div>';
    }

    public function wems_resgister_submenu_page() {
        add_menu_page("Email Marketing Simples", "Email Marketing Simples","manage_options","woocommerce-email-marketing-simples",array($this, "wems_register_submenu_page_callback"), 'dashicons-email-alt');
        
    }
  

    private function wems_getData()
    {
        $this->wems_data->wemsSetOptionsPlugin('wems_data');
        return $this->wems_data->wemsGetOptionsPlugin();
    }

    public function wems_register_submenu_page_callback() 
    {
        ?>
        <form action='options.php' method='post'>

            <?php
            settings_fields( 'wemsPagina' );
            do_settings_sections( 'wemsPagina' );
            submit_button();
            ?>

        </form>
        <?php
     }
   

    public function wems_settings_fields()
    {
        register_setting('wemsPagina', 'wems_data');

        add_settings_section('wems_settings_section',__('WooCommerce Email Marketing Simples','woocomerce_email_marketing_simples'), '','wemsPagina');
        
        add_settings_field('wems_token', __('Token','woocommerce_email_marketing_simples'), array($this,'wems_field_render_token'),'wemsPagina', 'wems_settings_section');

        add_settings_field('wems_ativar', __('Ativar Email Marketing','woocommerce_email_marketing_simples'), array($this,'wems_field_render_ativar'),'wemsPagina', 'wems_settings_section');

        add_settings_field('wems_assunto', __('Assunto do e-mail','woocommerce_email_marketing_simples'), array($this,'wems_field_render_assunto'),'wemsPagina', 'wems_settings_section');

        add_settings_field('wems_corpo', __('Corpo do e-mail', 'woocommerce_email_marketing_simples'), array($this, 'wems_field_render_corpo'), 'wemsPagina', 'wems_settings_section');
        
    }
    
    public function wems_field_render_token()
    {
        $options = $this->wems_getData();
        ?>
        <input type='text' name=wems_data[wems_token]' value='<?php echo (isset($options['wems_token']) ? $options['wems_token'] : ''); ?>' style="width: 400px;" maxlength="40">
        <?php
    }
 

    public function wems_field_render_ativar()
    {
        $options = $this->wems_getData();
        ?>
        <select name='wems_data[wems_ativar]' id="wems_ativar" >
            <option value='1' <?php selected( (isset($options['wems_ativar']) ? $options['wems_ativar'] : ''), 1 ); ?>>Sim</option>
            <option value='0' <?php selected( (isset($options['wems_ativar']) ? $options['wems_ativar'] : ''), 0 ); ?>>Não</option>
        </select>

    <?php
    }
       
    public function wems_field_render_assunto()
    {
        $options = $this->wems_getData();
        ?>
        <input type='text' name=wems_data[wems_assunto]' value='<?php echo (isset($options['wems_assunto']) ? $options['wems_assunto'] : ''); ?>' style="width: 400px;">
        <?php
    }

    public function wems_field_render_corpo()
    {
        $options = $this->wems_getData();
        $content = (isset($options['wems_corpo']) ? $options['wems_corpo'] : '');
        $content_id = 'wems_corpo';
        $args = array('textarea_name' => 'wems_data[wems_corpo]');
        wp_editor($content, $content_id, $args);
    }

    private $_nonce = 'wems_admin_nonce';

    public function wemsAddScripts()
    {
        wp_enqueue_script('wems-admin', WEMS_URL . 'assets/js/admin.js', array(), 1.0);

        $admin_options = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            '_nonce'   => wp_create_nonce($this->_nonce)
        );

        wp_localize_script('wems-admin', 'wems_ex', $admin_options);

    }

    public function buscaClientes()
    {
        $objdata = new WEMSData();
        return $objdata->getEmailsClientes();
    }

    public function enviaEmails()
    {
        $objdata = new WEMSData();
        return $objdata->wemsSendEmails();
    }

    public function enviaEmailsTeste()
    {
        $post = $_POST;
        $objdata = new WEMSData();
        return $objdata->wemsSentTest($post['email']);
    }

    public function getEmailData()
    {
        $objdata = new WEMSData();
        return $objdata->getEmailToSend();
    }

    public function check_smtp_config()
    {
        $objConfig = new WEMSConfig();
        return $objConfig->wems_check_smtp_config();
    }

    public function criandoTabelas()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "wems_emails";
        $charset_collate = $wpdb->get_charset_collate();
        $db_version = '1.0.0';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT NOT NULL AUTO_INCREMENT,
            email VARCHAR(150) NULL,
            status VARCHAR(1) NULL,
            PRIMARY KEY (id)
            )$charset_collate;";

           require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
          
           $this->table_success = dbDelta($sql); 
           add_option('my_db_version', $db_version);
    }
   
}

new woocomerce_email_marketing_simples();