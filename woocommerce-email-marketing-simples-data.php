<?php
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
        $sql = "SELECT DISTINCT * FROM {$this->db->prefix}users";
        $clientes = $this->db->get_results($sql);
        wp_die(print_r($clientes, true));
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

