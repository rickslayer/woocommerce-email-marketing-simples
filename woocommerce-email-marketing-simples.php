<?php
/*
 * Plugin Name: WooCommerce Email Marketing Simples
 * Plugin URI: 
 * Description: Envie e-mail marketing de forma simples
 * Version:  1.10
 * Author: @rickslayer
 * Author URI: http://www.actio.net.br
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


class woocomerce_email_marketing_simples
{
    public function __construct()
    {
        $this->wems_checkWoocommerceActive();

        add_action( 'admin_menu', array( $this, 'wems_resgister_submenu_page' ) );
       // add_action( 'wp_ajax_store_admin_data', array( $this, 'storeAdminData' ) );
       // add_action( 'admin_enqueue_scripts', array( $this, 'addAdminScripts' ) );
    }

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
        add_submenu_page( 'woocommerce', 'Email Marketing Simples', 'Email Marketing Simples', 'manage_options', 'woocommerce-email-marketing-simples', array($this, 'wems_register_submenu_page_callback' )); 
    }

    private $option_name = 'wems_data';

    private function wems_getData()
    {
        return get_option($this->option_name, array());
    }

    public function wems_register_submenu_page_callback() 
    {

        $data = $this->wems_getData();

        ?>
        <div class="wrap">
        <h2>WooCommerce Email Marketing Simples</h2>
        <form action="post">
            <table class="form-table">
                <tbody>
                    <tr>
                        <td scope="row">
                            <label> <?php _e('Assunto', 'woocomerce_email_marketing_simples')?></label>
                        </td>
                        <td>
                            <input type="text" name="wems_assunto" id="wems_assunto" class="regular-text"  value="<?php echo (isset($data['wems_assunto'])) ? $data['wems_assunto'] : ''; ?>"/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        </div>
        <?php
    }
   

}

new woocomerce_email_marketing_simples();