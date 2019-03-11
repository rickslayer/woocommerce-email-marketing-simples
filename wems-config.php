<?php
require_once('woocommerce-email-marketing-simples-data.php');
class WEMSConfig 
{
    private $wems_data;
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'wems_register_submenu_page_enviar'));
        add_action('sd_settings_tab', array($this, 'wems_enviar_email_tab'));
        add_action( 'sd_settings_content', array($this, 'wems_enviar_email_content' ));
        add_action('sd_settings_tab', array($this, 'wems_config_tab'));
        add_action( 'sd_settings_content', array($this, 'wems_config_content' ));
        add_action('admin_init', array($this, 'wems_settings_fields_smtp_config'));

        $this->wems_data = new WEMSData();
        $this->wems_data->wemsSetOptionsPlugin('wems_data_smtp');
        
    }

    public function wems_register_submenu_page_enviar(){
        add_submenu_page('woocommerce-email-marketing-simples', 'Enviar e-mails', 'Enviar e-mails', 'manage_options','wems-enviar-email', array($this, 'wems_register_submenu_page_callback_enviar'));
    }

    public function wems_register_submenu_page_callback_enviar()
    {
        global $sd_active_tab;
        $sd_active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'enviar-emails'; ?>
       
        <h2 class="nav-tab-wrapper">
            <?php
                do_action( 'sd_settings_tab' );
            ?>
         </h2>
         <?php
            do_action('sd_settings_content');
            
           
    }

    public function wems_enviar_email_tab()
    {
        global $sd_active_tab; ?>
	        <a class="nav-tab <?php echo $sd_active_tab == 'enviar-emails' || '' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=wems-enviar-email&tab=enviar-emails' ); ?>"><?php _e( 'Enviar Emails', 'wems' ); ?> </a>
	    <?php
    
    }

    public function wems_config_tab()
    {
        global $sd_active_tab; ?>
         <a class="nav-tab <?php echo $sd_active_tab == 'config-smtp' || '' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=wems-enviar-email&tab=config-smtp' ); ?>"><?php _e( 'Configurar SMTP', 'wems' ); ?> </a>
         <?php
    }

    public function wems_config_content()
    {
        global $sd_active_tab;
        if ( '' || 'config-smtp' != $sd_active_tab )
            return;
        ?>
        <form action='options.php' method='post'>

            <?php
            settings_fields( 'wemsSMTPage' );
            do_settings_sections( 'wemsSMTPage' );
            submit_button();
            ?>

        </form>
        <?php    
    }

    public function wems_settings_fields_smtp_config()
    {
        register_setting('wemsSMTPage', 'wems_data_smtp');

        add_settings_section('wems_settings_section_smtp',__('Configurar o Enviador de e-mail','woocomerce_email_marketing_simples'), '','wemsSMTPage');

        add_settings_field('wems_smtp_nome', __('Nome','woocommerce_email_marketing_simples'), array($this,'wems_field_render_smtp_nome'),'wemsSMTPage', 'wems_settings_section_smtp');

        add_settings_field('wems_smtp_email', __('E-mail','woocommerce_email_marketing_simples'), array($this,'wems_field_render_smtp_email'),'wemsSMTPage', 'wems_settings_section_smtp');

        add_settings_field('wems_smtp_pass', __('Senha','woocommerce_email_marketing_simples'), array($this,'wems_field_render_smtp_pass'),'wemsSMTPage', 'wems_settings_section_smtp');

        add_settings_field('wems_smtp_host', __('Host','woocommerce_email_marketing_simples'), array($this,'wems_field_render_smtp_host'),'wemsSMTPage', 'wems_settings_section_smtp');

    }

    public function wems_field_render_smtp_nome()
    {
        $options = $this->wems_data->wemsGetOptionsPlugin();
        ?>
        <input type='text' required name=wems_data_smtp[wems_smtp_nome]' placeholder="Nome que irá aparecer no enviado por" value='<?php echo (isset($options['wems_smtp_nome']) ? $options['wems_smtp_nome'] : ''); ?>' style="width: 400px;">
        <?php
    }

    public function wems_field_render_smtp_email()
    {
        $options = $this->wems_data->wemsGetOptionsPlugin();
        ?>
        <input type='email' required name=wems_data_smtp[wems_smtp_email]' placeholder="E-mail" value='<?php echo (isset($options['wems_smtp_email']) ? $options['wems_smtp_email'] : ''); ?>' style="width: 400px;">
        <?php
    }

    public function wems_field_render_smtp_pass()
    {
        $options = $this->wems_data->wemsGetOptionsPlugin();
        ?>
        <input type='password' required name=wems_data_smtp[wems_smtp_pass]' placeholder="Senha do e-mail" value='<?php echo (isset($options['wems_smtp_pass']) ? $options['wems_smtp_pass'] : ''); ?>' style="width: 400px;">
        <?php
    }

    public function wems_field_render_smtp_host()
    {
        $options = $this->wems_data->wemsGetOptionsPlugin();
        ?>
        <input type='text' required name=wems_data_smtp[wems_smtp_host]' placeholder="Examplo: smtp@seudominio.com.br" value='<?php echo (isset($options['wems_smtp_host']) ? $options['wems_smtp_host'] : ''); ?>' style="width: 400px;">
        <?php
    }

    public function wems_enviar_email_content()
    {
        global $sd_active_tab;
        if ( '' || 'enviar-emails' != $sd_active_tab )
            return;
        ?>
      
         <h2>Primeiro Passo Clique no botão abaixo para verificar a quantidade de e-mails</h2>
        <table>
            <tbody>
                <tr scope="row">
                    <td>  <button id="buscarEmails" class="button button-primary">Buscar e-mails</button></td>
                    <td><p class="strong" id="quantidade_emails"></p></td>
                </tr>
            </tbody>
        </table>
        <div id="segundopasso">
            <h2>Agora é só clicar em enviar e esperar a mensagem de finalização!</h2>
            <table>
                <tbody>
                    <tr scope="row">
                    <td>  <button id="EnviarEmails" class="button button-primary">Enviar Emails</button></td>
                    <td><p class="strong" id="quantidade_enviada"></p></td>
                    <td><div class="update-nag notice" id="div_contador"><p id="contador"></p></div></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }

}