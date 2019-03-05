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
        $sql = "SELECT DISTINCT user_email FROM {$this->db->prefix}users";
        $email = $this->db->get_results($sql);

        
        $result = new  WEMSobjJSON();
        $result->success = true;
        $result->count = count($email);
    

        $this->retornoJSON($result);
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

