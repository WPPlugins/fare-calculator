<?php 

global $wpdb;

if(isset($_POST['fc_name'])){
	
$table_name = $wpdb->prefix . "taxibooked";
$wpdb->insert( $table_name, array( 'name' => $_POST['fc_name'], 'fare' => $_POST['fc_price'] ) );


}
?>