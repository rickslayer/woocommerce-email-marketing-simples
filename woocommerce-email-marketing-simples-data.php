<?php
require_once('woocommerce-email-marketing-simples-sender.php');
class WEMSData
{
    private $db;
    
    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;

    }

    public function retornoJSON(WEMSobjJSON $obj){
        ob_clean();
        
        wp_die(json_encode($obj));
    }

    public function getEmailsClientes()
    {
        $sql = "SELECT DISTINCT user_email FROM {$this->db->prefix}users";
        $email = $this->db->get_results($sql);

        
        $result = new  WEMSobjJSON();
        $result->success = true;
        $result->count = count($email);
        $result->insert_id = $this->setDataonTable($email);
    

        $this->retornoJSON($result);
    }
    
    public function getEmailToSend()
    {
        $sql = "SELECT DISTINCT email FROM {$this->db->prefix}wems_emails WHERE status = 'N'";
        $email = $this->db->get_results($sql);

        
        $result = new  WEMSobjJSON();
        $result->success = true;
        $result->count = count($email);
        $result->content = $email;
    

        $this->retornoJSON($result);
    }

    private function setDataonTable($data)
    {
        $table = $this->db->prefix . 'wems_emails';

        foreach($data as $item) {
            
            $dados = array('email' => $item->user_email, 'status' => 'N');
            $this->db->insert($table, $dados);
            $aIDS[] =  $this->db->insert_id;
        
        }
        return $aIDS;
    }
    
    public function wemsSendEmails()
    {   
      $table   = $this->db->prefix . 'wems_emails';
      $sql     = "SELECT DISTINCT email FROM {$table} WHERE status='N' LIMIT 1";
      $email   = $this->db->get_results($sql);
      
      $opcoes  = get_option('wems_data');
      $opcoes_smtp = get_option('wems_data_smtp');
        
      $objSender = new WEMSEmailSender($email[0]->email,$opcoes['wems_assunto'],$opcoes['wems_corpo'],$opcoes_smtp['wems_smtp_email'],$opcoes_smtp['wems_smtp_pass'],$opcoes_smtp['wems_smtp_host'],$opcoes_smtp['wems_smtp_nome'],$opcoes_smtp['wems_smtp_porta']); 
     
    }

    public function wemsSentTest($to)
    {
        $opcoes  = get_option('wems_data');
        $opcoes_smtp = get_option('wems_data_smtp');
        
        $objSender = new WEMSEmailSender($to,$opcoes['wems_assunto'],$opcoes['wems_corpo'],$opcoes_smtp['wems_smtp_email'],$opcoes_smtp['wems_smtp_pass'],$opcoes_smtp['wems_smtp_host'],$opcoes_smtp['wems_smtp_nome'],$opcoes_smtp['wems_smtp_porta']); 
    }
    
    private $option_name;

    public function wemsGetOptionsPlugin()
    {
        return get_option($this->option_name, array());
    }

    public function wemsSetOptionsPlugin($option_name)
    {
        $this->option_name = $option_name;
    }

}

class WEMSobjJSON extends \stdClass
{
    public function __construct()
    {
        $this->success = false;
        $this->message = "";
        $this->content = "";
        $this->error   = "";
    }

}

