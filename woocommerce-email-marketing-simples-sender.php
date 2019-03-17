<?php
require($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
require_once(ABSPATH . 'wp-includes/class-phpmailer.php');
require_once(ABSPATH . 'wp-includes/class-smtp.php');
require_once('woocommerce-email-marketing-simples-data.php');

class WEMSEmailSender extends PHPMailer
{
    
    private $result;

    public function __construct($to, $assunto, $corpo, $usuario_email, $senha, $host, $nome, $porta)
    {
        date_default_timezone_set('America/Sao Paulo');
        $this->result = new WEMSobjJSON();
        $this->isSMTP();
        $this->CharSet='UTF-8';
        $this->setFrom($usuario_email, $nome);
        $this->addReplyTo($usuario_email, $nome);
        $this->Subject = $assunto;
        //$this->AddBCC('webmaster@bemybag.com.br', 'Administrador');
        $this->SMTPAuth = true;  
        if($host == 'smtp.gmail.com') {
            $this->SMTPSecure = 'tls';
        }
        //$mail->SMTPSecure = 'ssl'; 
        $this->SMTPAutoTLS = false;
        $this->Host = $host;
        $this->Port = $porta;
        $this->Username = $usuario_email;
        $this->Password = $senha;        
        $this->SMTPDebug  = 0;
        $this->addAddress( $to );
        $this->IsHTML(true);
        $this->Body = $corpo;
        $updated = $this->atualizaStatusEmail($to);

        if(!$this->send()) {
            $this->result->message = "Erro ao enviar o e-mail";
            $this->result->success = false;
            $this->result->error = $this->ErrorInfo;
        } else {
            $this->result->message = "Enviado com sucesso";
            $this->result->success = true;
            $this->result->content = $updated;
            $this->result->email_sentto = $to;

        }
        $objData = new WEMSData();
        $objData->retornoJSON($this->result);

    }
    private function atualizaStatusEmail($email)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'wems_emails';
        return $wpdb->update($table, array('status' => 'S'), array('email' => $email));
    }
}