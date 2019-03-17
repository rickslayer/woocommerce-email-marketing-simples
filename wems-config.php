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
        add_filter( 'plugin_row_meta', array($this, 'wems_custom_plugin_row_meta'), 10, 2 );
        add_filter('plugin_action_links', array($this, 'wems_plugin_action_links'), 10, 2);

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
        <table class="form-table">
            <tbody>
                <h3>Teste se as configurações estão corretas:</h3>
                <tr><th scope="row"><input type="email" style="width: 400px;" id="emailTeste" placeholder="digite um e-mail para testar o envio"></th><td> 
                <tr>
                    <td><button class="button button-primary" id="btnEnviaTeste">Testar Envio</button></td>
                    <td><div class="update-nag notice" id="msg_sucesso_teste"></div></td>
                </tr>
            </tbody>
        </table>
        <?php    
    }

    public function wems_settings_fields_smtp_config()
    {
        register_setting('wemsSMTPage', 'wems_data_smtp');

        add_settings_section('wems_settings_section_smtp',__('Configurar o Enviador de e-mail','woocomerce_email_marketing_simples'), '','wemsSMTPage');

        add_settings_field('wems_smtp_token', __('Token','woocommerce_email_marketing_simples'), array($this,'wems_field_render_smtp_token'),'wemsSMTPage', 'wems_settings_section_smtp');

        add_settings_field('wems_smtp_nome', __('Nome','woocommerce_email_marketing_simples'), array($this,'wems_field_render_smtp_nome'),'wemsSMTPage', 'wems_settings_section_smtp');

        add_settings_field('wems_smtp_email', __('E-mail','woocommerce_email_marketing_simples'), array($this,'wems_field_render_smtp_email'),'wemsSMTPage', 'wems_settings_section_smtp');

        add_settings_field('wems_smtp_pass', __('Senha','woocommerce_email_marketing_simples'), array($this,'wems_field_render_smtp_pass'),'wemsSMTPage', 'wems_settings_section_smtp');

        add_settings_field('wems_smtp_host', __('Host','woocommerce_email_marketing_simples'), array($this,'wems_field_render_smtp_host'),'wemsSMTPage', 'wems_settings_section_smtp');
       
        add_settings_field('wems_smtp_porta', __('Porta','woocommerce_email_marketing_simples'), array($this,'wems_field_render_smtp_porta'),'wemsSMTPage', 'wems_settings_section_smtp');
        

    }
    public function wems_field_render_smtp_token()
    {
        $options = $this->wems_data->wemsGetOptionsPlugin();
        ?>
        <input type='text' required name=wems_data_smtp[wems_smtp_token]' maxlength="40" placeholder="token necessário para funcionar" value='<?php echo (isset($options['wems_smtp_token']) ? $options['wems_smtp_token'] : ''); ?>' style="width: 400px;">
        <?php
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

    public function wems_field_render_smtp_porta()
    {
        $options = $this->wems_data->wemsGetOptionsPlugin();
        ?>
        <input type='text' required name=wems_data_smtp[wems_smtp_porta]' maxlength="3" value='<?php echo (isset($options['wems_smtp_porta']) ? $options['wems_smtp_porta'] : ''); ?>' style="width: 100px;">
        <?php
    }

    public function wems_enviar_email_content()
    {
        global $sd_active_tab;
        if ( '' || 'enviar-emails' != $sd_active_tab )
            return;
        ?>
        <div class="error notice" id="msg_erro_smtp"></div>
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

    public function wems_custom_plugin_row_meta( $links, $file ) 
    {

        if ( strpos( $file, 'woocommerce-email-marketing-simples.php' ) !== false ) {
            $new_links = array(
                    'config-smtp'  => '<a href="/wp-admin/admin.php?page=wems-enviar-email&tab=config-smtp">Configurar SMTP</a>',
                    
                    'configuracao' => '<a href="/wp-admin/admin.php?page=wems-enviar-email">Enviar E-mail</a>',
                    
            );
            
            $links = array_merge( $links, $new_links );
        }
	
    	return $links;
    }

    public function wems_plugin_action_links($links, $file) 
    {
        if ( strpos( $file, 'woocommerce-email-marketing-simples.php' ) !== false ) {
        
            $new_links = array(
                'config-email'  => '<a href="/wp-admin/admin.php?page=woocommerce-email-marketing-simples">Montar Email</a>',
            );

            $links = array_merge($links, $new_links);
        }

        return $links;
    }

    public function wems_check_smtp_config()
    {
        $options = $this->wems_data->wemsGetOptionsPlugin();
       
        $result  = new WEMSobjJSON();        
        if($options['wems_smtp_email'] != '' && $options['wems_smtp_nome'] != '' && $options['wems_smtp_pass'] != ''  && $options['wems_smtp_porta'] != '') {
            $result->message = 'Configuração SMTP Ok';
            $result->success = true;
        } else {
            $result->message = 'Reveja as configurações SMTP';
            $result->success = false;
        }
        
        $this->wems_data->retornoJSON($result);
        
    }

}

class validateToken
{
    public static function check($token)
    {
        ob_start();
        $service_url = 'http://api.actiocomunicacao.com.br/api/token';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL , $service_url);
        curl_setopt($curl, CURLOPT_HEADER , 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("token:{$token}",'Content-Type: application/json;charset=utf8'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,"token=''");
        curl_exec($curl);
        $resposta = ob_get_contents();
        ob_end_clean();
        $httpCode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
        curl_close($curl);
        die(print_r($resposta, true))   ;
    }
}