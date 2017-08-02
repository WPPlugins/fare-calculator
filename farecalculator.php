<?php
/*
Plugin Name: farecalculator
Plugin URI: http://staunchire.com
Description: Easy way to calculate the fare price on taxi or any service, with the help of google map with auto suggestion place , this is the plugin you need. 
Version: 1.1
Author: Gopi krishnan, MoB: +91 8122335200, Email: krishna25auro@gmail.com
Author URI: https://www.facebook.com/badchetah
License: GPL2
*/
/*
Copyright 2014  Gopi krishnan  (email : krishna25auro@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program;
*/
require_once(ABSPATH.'wp-admin/includes/upgrade.php');

function fc_createtable(){
global $wpdb;
$charset_collate = '';

if ( ! empty( $wpdb->charset ) ) {
  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
}

if ( ! empty( $wpdb->collate ) ) {
  $charset_collate .= " COLLATE {$wpdb->collate}";
}



 $table_name = $wpdb->prefix . "fare";
    $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    service tinytext NOT NULL,
    fare tinytext NOT NULL,
    UNIQUE KEY id (id)
    ) $charset_collate;";
 $table_nameb = $wpdb->prefix . "taxibooked";
$sql2= "CREATE TABLE IF NOT EXISTS $table_nameb (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `sd` tinytext NOT NULL,
  `vehicle` varchar(20) NOT NULL,
  `distance` varchar(20) NOT NULL,
  `price` varchar(20) NOT NULL,
  `address` tinytext NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(40) NOT NULL,
  `date_booked` varchar(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;";

dbDelta( $sql );
dbDelta( $sql2 );





}
/*****deactivate table ****/

function fc_fareremovetb() {
     global $wpdb;
     $table_name2 = $wpdb->prefix . "fare";
          $table_nameb = $wpdb->prefix . "taxibooked";
     $sql2 = "DROP TABLE IF EXISTS $table_name;";
          $sql3 = "DROP TABLE IF EXISTS $table_nameb;";
     $wpdb->query($sql2);   $wpdb->query($sql3);
     delete_option("my_plugin_db_version");

 $page=get_page_by_title('booktaxi'); 
      wp_delete_post($page_ID); 
}



register_activation_hook( __FILE__, 'fc_createtable' );
register_deactivation_hook( __FILE__, 'fc_fareremovetb');



/*********administration page***********/
function fc_plugin_menu() {
  add_menu_page('Fare Plugin Settings', 'Fare Settings', 'administrator', __FILE__, 'fc_faresettingspage',plugins_url('/images/fare.png', __FILE__));
  
   //  add_menu_page('My Page Title', 'My Menu Title', 'manage_options', __FILE__, 'my_menu_output' );
    add_submenu_page(__FILE__, 'Submenu Page Title', 'Bookings', 'administrator','fare-calculator/send.php','fc_bookings' );
        add_submenu_page(__FILE__, 'Submenu Page Title', 'ConfirmedBookings', 'administrator','fare-calculator/sendd.php','fc_confirmbookings' );
 add_submenu_page(__FILE__, 'Submenu Page Title', 'Settings', 'administrator','fare-calculator/senddd.php','fc_settings' );



}
//add_submenu_page( 'tools.php', 'My Custom Submenu Page', 'My Custom Submenu Page', 'manage_options', 'my-custom-submenu-page', 'fc_service' ); }
add_action( 'admin_menu', 'fc_plugin_menu' );
// mt_settings_page() displays the page content for the Test settings submenu



function fc_faresettingspage() {  
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }  
    $lat = 'latitude';
    $long='longitude';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'mt_favorite_color';
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
             $lat_val = $_POST[ $data_field_name ];
        $long_val=$_POST['longitude'];
        update_option( $lat, $lat_val );
            update_option( $long, $long_val );?>
<div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
<?php }
      echo '<div class="wrap">';
    echo "<h2>" . __( 'Fare Calculator Settings', 'menu-test' ) . "</h2>";
    ?>
<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
  <table class="form-table">
        <tr valign="top">
    <label>Enter latitude and longitude of country/place to display on map</label> 
   <th scope="row">Latitude</th>  
 <td>
<input type="text" name="<?php echo $data_field_name; ?>" value="11.9310" size="20" readonly>
 </td> </tr>
 <tr valign="top">
       <th scope="row">Longitude</th> 
 <td>
<input type="text" name="longitude" value="79.7852" size="20" readonly>
 </td> </tr>
<tr>
  <td></td>
<td>
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" /></td>
 </tr>
<hr />
</table>
</form>
<hr/>
</div>
<?php 
//fc_paypal();
fc_addservice();

}










function fc_addservice()
{

global $wpdb;
if(isset($_POST['service'])){

$table_name = $wpdb->prefix . "fare";
$wpdb->insert( $table_name, array( 'service' => $_POST['service'], 'fare' => $_POST['fare'] ) );
?>
<div class="updated"><p><strong><?php _e('Added Successfully.', 'menu-test' ); ?></strong></p></div>
<?php } ?>
<form method="post" action="">
       <table class="form-table">
        <tr valign="top">
    <h2>Enter Service and its price/fare</h2> 
        <th scope="row">Service</th>
        <td><input type="text" name="service" value="" required="required" /> eg : INNOVA</td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Fare</th>
        <td><input type="text" name="fare" value="" required="required" />eg : $ 1/km(mile)(for amount)</td>
        </tr>
    <tr><td></td> <td>   <input type="submit"  class="button-primary" value="ADD"/></td> </tr>
        </table>    

</form><hr/>

<?php 
fc_displayservice();
}



function fc_displayservice(){ ?>
  <div class="faretable">
<h2>Data to be displayed on front end </h2>
    <table  >
    <th>Service</th><th>Fare</th><th>Action</th>
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . "fare";
    $postids = $wpdb->get_results("SELECT id,service,fare FROM $table_name");
    if(isset($_POST['delid'])){
  global $wpdb;
  echo $did=$_POST['delid'];
  $table_name = $wpdb->prefix . "fare";
        $wpdb->query(" DELETE FROM $table_name where id=".$did);
        ?>
   <div class="updated"><p><strong><?php _e('Row Deleted.', 'menu-test' ); ?></strong></p></div>
<?php 
}


            foreach ($postids as $value) {
                echo '<tr valign="top">';
                echo '<td>' . $value->service . '</td>';
                echo '<td>' . $value->fare . '</td>';
               echo "<td><form method='post' action=''>
         <input name='delid' type='hidden' value='$value->id'/><input type='submit' value='Delete'/></form></td>";

                echo '</tr>';
            } 
             
       ?>
       </table>
</div>
<?php //fc_mapui(); 
}








/************** map interface *****************/
function fc_fare(){?>
<style type="text/css">html{height:100%}body{height:100%;margin:0px;padding:0px;font-family:tahoma;font-size:8pt}#total{font-size:large;text-align:center;font-family:arial;color:green;margin:5px 0 10px 0;font-size:14px;width:374px}input{margin:5px 0px;font-family:tahoma;font-size:8pt}</style>
<head><script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script>
  $(function() {
    $( "#datepicker" ).datepicker();
  });
  </script> 


<script type="text/javascript">//<![CDATA[
var map=null;var directionDisplay;
var directionsService=new google.maps.DirectionsService();
function fc_initialize(){directionsDisplay=new google.maps.DirectionsRenderer();
  var India=new google.maps.LatLng(11.9310,79.7852);
  var mapOptions={center:India,zoom:10,minZoom:3,
    streetViewControl:false,
    mapTypeId:google.maps.MapTypeId.ROADMAP,
    zoomControlOptions:{style:google.maps.ZoomControlStyle.MEDIUM}};
    map=new google.maps.Map(document.getElementById('map_canvas'),mapOptions);

    var fromText=document.getElementById('start');var fromAuto=new google.maps.places.Autocomplete(fromText);fromAuto.bindTo('bounds',map);
    var toText=document.getElementById('end');
    var toAuto=new google.maps.places.Autocomplete(toText);toAuto.bindTo('bounds',map);
    directionsDisplay.setMap(map);directionsDisplay.setPanel(document.getElementById('directions-panel'));}



function fc_calcRoute(){
  var start=document.getElementById('start').value;
  var end=document.getElementById('end').value;
  document.getElementById("fc_source").value=start;
document.getElementById("fc_destination").value=end;

//document.getElementById("sd").innerHTML= start+""+"-"+""+end;


  var request={origin:start,destination:end,travelMode:google.maps.DirectionsTravelMode.DRIVING};
  directionsService.route(request,
    function(response,status){if(status==google.maps.DirectionsStatus.OK){directionsDisplay.setDirections(response);fc_computeTotalDistance(response);}});}
function fc_computeTotalDistance(result){var total=0;
  var myroute=result.routes[0];
  for(i=0;i<myroute.legs.length;i++){total+=myroute.legs[i].distance.value;}
total=total/1000;var e=document.getElementById("car");
var strUser=e.options[e.selectedIndex].value;

var selectedcar =e.options[e.selectedIndex].text;




var dist=strUser*total;

document.getElementById("fc_price").value=dist;
document.getElementById("fc_dist").value=total;
document.getElementById("fc_car").value=selectedcar;

document.getElementById("total").innerHTML="Total Distance = "+total+" km </br> Fare Price = Rs "+dist;}
function auto(){var input=document.getElementById[('start'),('end')];var types
var options={types:[],componentRestrictions:{country:["IND"]}};var autocomplete=new google.maps.places.Autocomplete(input,options);}
google.maps.event.addDomListener(window,'load',fc_initialize);
//]]></script>
<style>#car{  border: 1px solid #c6c6c6;
   
    padding: 5px;
    width: 50%;}

    #map_canvas{
      width: 100% !important;
    }
    </style>
 </head>
<body onLoad="fc_initialize()">
  <div id="map_canvas" style="width: 874px; height: 300px; border: solid 1px #336699"></div> 
 <div class="post style-2 bottom-2">
<div class='sdc'>
<table><tr><td> 
  <span style="color: black;float: left;"> From:</span></td>
               <td> <input type="text" id="start" size="50px" name="start" required placeholder="Enter Location From" style="float:left;margin-left:20px;"></td>
             <tr><td>   <span style="color: black;float: left;">To:</span></td>
                <td>  <input size="50px" type="text" id="end" name="end" required placeholder="Enter Destination " style="float:left;margin-left:20px;"> </td>
                          
 <tr><td>Select Service :</td><td><select  value="select car" id="car" style="margin-top:5px;margin-left:20px;">
         
             <option value='10'>Bus</option>
      <option value='20'>Van</option>
        </select></td></tr>
          
         <tr> <td><input type="button" value="Calculate" onClick="fc_calcRoute();"></td>
          <td> <div style="float:left" id="total"></td></tr>




       </div>

  
          </div>
             
             </div>
       </body>
<?php   fc_distprice();  
}





function fc_distprice(){
  $page=get_page_by_title('booktaxi'); 
   $taxiurl=site_url().'/?page_id='.$page->ID;


if (!empty($_POST['fc_price'])) {

echo "Kindly contact me  for other options";
}


  ?>
  <form action='' method='POST'>
  






<tr><input type="hidden" id="fc_source" name="fc_source" >
 <input type="hidden" id="fc_destination" name="fc_destination">
 <input type="hidden" id="fc_car" name="fc_car">
<input type="hidden" id="fc_dist" name="fc_dist">
<input type="hidden" id="fc_price" name="fc_price"></tr>


<tr><td>Name</td><td><input type='text' name='fc_name' id='fc_name' placeholder='Enter your name' required></td></tr>
<tr><td>Address</td><td><textarea  name='fc_address' placeholder='Enter address'></textarea></td></tr>
<tr><td>Mobile/Phone</td><td><input type='text' name='fc_mob' placeholder='Enter Mobile or phone' required></td></tr>
<tr><td>Email</td><td><input type='text' name='fc_email' placeholder='Enter email-id' required ></td></tr>
<tr><td>On Board Date</td><td><input type='text' id="datepicker" name='fc_date' placeholder='Pick date' required></td></tr>
<tr><td></td><td><input type='submit' value='Book Taxi'></td> </tr>
</form>
</table>
<?php
}




function fc_bookings()
{
   global $wpdb;
      $table_name = $wpdb->prefix . "taxibooked";
            $bookings = $wpdb->get_results("SELECT * FROM $table_name where status=1 ORDER BY id DESC LIMIT 30");

if (!empty($_POST['bdelid'])) {
$bdid=$_POST['bdelid'];

  $table_name = $wpdb->prefix . "taxibooked";
      // /  $wpdb->query(" DELETE FROM $table_name where id=".$bdid);

        $wpdb->query(" UPDATE $table_name SET status = 0 where id=".$bdid);



}

if (!empty($_POST['bdelidc'])) {
$bdidc=$_POST['bdelidc'];

  $table_name = $wpdb->prefix . "taxibooked";
        $wpdb->query(" DELETE FROM $table_name where id=".$bdidc);

       }

 echo "<style> table, tr, td, th {border:1px solid black;}</style>";
echo "<h2>Bookings</h2>";
echo "<table><th>Id</th>
<th>Name</th>
<th>Source-Destination</th><th>Vehicle</th>
<th>Distance</th>
<th>Price</th>
<th>Address</th>
<th>Phone</th>
<th>Email</th>
<th>OnBoard Date</th>

<th>Confirm</th>
<th>Delete</th>
";
            foreach ($bookings as $bvalue) {
              ?>
           


<tr><td><?php echo $bvalue->id; ?></td> <td><?php echo $bvalue->name; ?></td>
<td><?php echo $bvalue->sd; ?></td>
<td><?php echo $bvalue->vehicle; ?></td>
<td><?php echo $bvalue->distance; ?></td>
<td><?php echo $bvalue->price; ?></td>
<td><?php echo $bvalue->address ?></td>
<td><?php echo $bvalue->phone ;?></td>
<td><?php echo $bvalue->email ;?></td>
<td><?php echo $bvalue->date_booked; ?></td>
<td><form method='POST' action=''><input name='bdelid' type='hidden' value='<?php echo $bvalue->id; ?>'/><input type='submit' value='OK'/></form></td>
<td><form method='POST' action=''><input name='bdelidc' type='hidden' value='<?php echo $bvalue->id; ?>'/><input type='submit' value='Delete'/></form></td>

</tr>   

       <?php     }
       echo "</table>";
}




function fc_confirmbookings()
{
   global $wpdb;
      $table_name = $wpdb->prefix . "taxibooked";
            $bookings = $wpdb->get_results("SELECT * FROM $table_name where status=0 ORDER BY id DESC LIMIT 30");

if (!empty($_POST['bdelidc'])) {
$bdidc=$_POST['bdelidc'];

  $table_name = $wpdb->prefix . "taxibooked";
        $wpdb->query(" DELETE FROM $table_name where id=".$bdidc);

       }

 echo "<style> table, tr, td, th {border:1px solid black;}</style>";
echo "<h2>Confirmed Bookings</h2>";
echo "<table><th>Id</th>
<th>Name</th>
<th>Source-Destination</th><th>Vehicle</th>
<th>Distance</th>
<th>Price</th>
<th>Address</th>
<th>Phone</th>
<th>Email</th>
<th>OnBoard Date</th>

<th>Delete</th>
";
            foreach ($bookings as $bvalue) {
              ?>
           


<tr><td><?php echo $bvalue->id; ?></td> <td><?php echo $bvalue->name; ?></td>
<td><?php echo $bvalue->sd; ?></td>
<td><?php echo $bvalue->vehicle; ?></td>
<td><?php echo $bvalue->distance; ?></td>
<td><?php echo $bvalue->price; ?></td>
<td><?php echo $bvalue->address; ?></td>
<td><?php echo $bvalue->phone ;?></td>
<td><?php echo $bvalue->email ;?></td>
<td><?php echo $bvalue->date_booked; ?></td>
<td><form method='POST' action=''><input name='bdelidc' type='hidden' value='<?php echo $bvalue->id; ?>'/><input type='submit' value='Delete'/></form></td>

</tr>   

       <?php     }
       echo "</table>";
}

function fc_settings(){ ?>
<div><h2>Welcome Admin</h2>
<h3>Shortcode To Display Taxi fare calculator &nbsp;&nbsp;&nbsp; [fc_fare]</h3></br>
<h3>For all Options  to enable  Contact Me:</h3>
<p>Krish </br>
Email: krishna25auro@gmail.com </p>

</div>

<?php }


add_shortcode('fc_fare', 'fc_fare');
//add_shortcode('fc_booktaxi', 'fc_booktaxi');


