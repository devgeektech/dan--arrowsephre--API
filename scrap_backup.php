<?php
require('./wp-load.php'); 
ini_set('max_execution_time', 4000);
    echo "<table style='width: 100%;border: 1px solid;text-align: center;'>";
    echo "<tr>
              <th class='views-field views-field-field-image-cache-fid'>Image</th>
              <th class='views-field views-field-title'>Name</th>
              <th class='views-field views-field-field-chargecount-value'>Charge Descr.</th>
              <th class='views-field views-field-field-dateabp-value'>Date Arrested/Booked</th>
              <th class='views-field views-field-field-idbooking-value'>Booking ID</th>
              <th class='views-field views-field-tid'>State and Locale</th>
          </tr>";


        for ($page = 1; $page<=2; $page++) {
            // echo 'http://arrestfiles.org/records/florida/brevard-county?page='.$page;
            $url  = 'http://arrestfiles.org/records/florida/brevard-county?page=' . $page;
            //$main_url = 'http://www.viewit.ca/';
            $dom  = new DOMDocument;
            $html = file_get_contents($url);
            @$dom->loadHTML($html);
            $finder    = new DomXPath($dom);
            $classname = 'views-table';
    

          $nodes = $finder->query("//*[contains(@class, '$classname')]");

          $count=1;
          $user_details=array();
          foreach ($nodes as $node) {
      
          $rowtbody=$node->getElementsByTagName('tbody');
          foreach ($rowtbody as $tbody) {
            $rowtr=$tbody->getElementsByTagName('tr');
            foreach($rowtr as $woData){
               echo "<tr>";
              //print_r($woData);
              $heree=$woData->getElementsByTagName('td');
              $imageSrc = $woData->getElementsByTagName('img');
              $imageurl=$woData->getElementsByTagName('a');
              $imageUrlMain=$imageurl->item(0)->getAttribute('href');


              $imaged=$imageSrc->item(0)->getAttribute('src');
              $username = $heree->item(1)->nodeValue;
              $charge = $heree->item(2)->nodeValue;
              $bookingId = $heree->item(3)->nodeValue;
              $date = $heree->item(4)->nodeValue;
              $state = $heree->item(5)->nodeValue;


             $user_details['userurl']=$imaged;
             $user_details['username']=$username;
             $user_details['charge']=$charge;
             $user_details['bookingId']=$bookingId;
             $user_details['date']=$date;
             $user_details['state']=$state;

             
               echo "<td style='padding:5px'><a href='".$imageUrlMain."'><img src='".$imaged."' ></a></td>";
               echo "<td style='padding:5px'>".$username."</td>";
               echo "<td style='padding:5px'>".$charge."</td>";
               echo "<td style='padding:5px'>".$bookingId."</td>";
               echo "<td style='padding:5px'>".$date."</td>";
               echo "<td style='padding:5px'>".$state."</td>";

               echo "</tr>";

                $timestamp = date("Y-m-d H:i:s", strtotime($user_details['date']));
               $postname=trim($user_details['userurl']);
               $postId=trim($user_details['date']);
               $username=trim($user_details['username']);


$image_url  = $imaged; // Define the image URL here
$upload_dir = wp_upload_dir(); // Set upload folder
$image_data = file_get_contents($image_url); // Get image data

$filename   = basename($image_url); // Create image file name

// Check folder permission and define file location
if( wp_mkdir_p( $upload_dir['path'] ) ) {
    $file = $upload_dir['path'] . '/' . $filename;
} else {
    $file = $upload_dir['basedir'] . '/' . $filename;
}

// Create the image  file on the server
file_put_contents( $file, $image_data );

// Check image file type
$wp_filetype = wp_check_filetype( $filename, null );

// Set attachment data
$attachment = array(
    'post_mime_type' => $wp_filetype['type'],
    'post_title'     => sanitize_file_name( $filename ),
    'post_content'   => '',
    'post_status'    => 'inherit'
);
global $wpdb;
$sqlSelectPostt=$wpdb->get_results("SELECT * FROM `wp_posts`");

$idArray=array();
foreach ($sqlSelectPostt as $vfvalue) {
 $idArray['ID']=$vfvalue->ID;
}
   
 $post_id=$idArray['ID'];

// Create the attachment
$attach_id = wp_insert_attachment( $attachment, $file, $post_id );

// Include image.php
require_once(ABSPATH . 'wp-admin/includes/image.php');

// Define attachment metadata
$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

// Assign metadata to attachment
wp_update_attachment_metadata( $attach_id, $attach_data );

// And finally assign featured image to post
set_post_thumbnail( $post_id, $attach_id );

               global $wpdb;

                     $sqlSelectPost=$wpdb->get_results("SELECT * FROM `wp_posts` WHERE `bookingID` LIKE '%$postId%'");
                 
                     $sid=$sqlSelectPost[0]->bookingID;
                

                     if(!$sid) {
                      $wpdb->insert("wp_posts", array(
                        "bookingID"=>$date,
                       "post_author" =>'1',
                        "post_date"   =>$timestamp,
                        "post_date_gmt"   =>$timestamp,
                        "post_content" =>trim($user_details['username']),
                        "post_excerpt" =>trim($user_details['username']),
                        "post_status" =>'publish',
                        "comment_status" =>'open',
                        "post_password" =>'',
                        "post_name" =>trim($user_details['userurl']),
                        "to_ping" =>'',
                        "pinged" =>'',
                        "post_modified" =>$timestamp,
                        "post_modified_gmt" =>$timestamp,
                        "post_content_filtered" =>'',
                        "post_parent" =>'0',
                        "guid" =>'http://miamiarrest.com/scrap/?page_id=$user_details["date"]',
                        "menu_order" =>'0',
                        "post_type" =>'post',
                        "post_mime_type" =>'',
                        "comment_count" =>'0',
                        "post_title"=>trim($user_details['username'])


                    ));
                }

                else{


   
              $wpdb->query("UPDATE `wp_posts` SET `bookingID`='$date', `post_author` = `1` `post_date` = '$timestamp',`post_date_gmt` = '$timestamp',`post_content` = '$username',
                `post_excerpt` = '$username',`comment_status` = 'open',`post_password` = '',`post_name` = '$postname',
                `to_ping` = '',`pinged` = '',`post_modified` = '$timestamp',`post_content_filtered` = '',`post_parent` = '0',
                `guid` = 'http://miamiarrest.com/scrap/?page_id=$postId',`menu_order` = '0',`post_type` = 'post',`post_mime_type` = '',
                `comment_count` = '0',`post_title` = '$username' WHERE `bookingID`  = '$postId'");
              }

                }
            } 
                
          }
         
    }

        
        // $psotexists=array();

         //foreach($sqlSelectPost as $postmains){
          
        // echo $user_details['userName'];
          //$psotexists['ID']=$postmains['bookingID'];
         //}
            
           // if(!$psotexists) {

              
              //  }
              // else {
            
              // $wpdb->query("UPDATE `wp_posts` SET `bookingID`='$postId', `post_author` = `1` `post_date` = '$timestamp',`post_date_gmt` = '$timestamp',`post_content` = '$username',
              //   `post_excerpt` = '$username',`comment_status` = 'open',`post_password` = '',`post_name` = '$postname',
              //   `to_ping` = '',`pinged` = '',`post_modified` = '$timestamp',`post_content_filtered` = '',`post_parent` = '0',
              //   `guid` = 'http://localhost/dan1/?page_id=$postId',`menu_order` = '0',`post_type` = 'post',`post_mime_type` = '',
              //   `comment_count` = '0',`post_title` = '$username' WHERE `bookingID`  = '$postId'");
              // }
            
         // }

  //Post Database Entry
      
 echo "</table>";


?>