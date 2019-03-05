<?php
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    function wems_init()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "wems_emails";
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "   CREATE TABLE $table_name (
            id INT NOT NULL,
            content JSON NULL,
            PRIMARY KEY (id)
            )$charset_collate;";
     
           dbDelta($sql); 
      }
      
      register_activation_hook(__FILE__, array($this, 'wems_init'));


