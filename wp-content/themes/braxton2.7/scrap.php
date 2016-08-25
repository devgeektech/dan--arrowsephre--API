<?php
echo "dc";
require('./wp-load.php');    
ini_set('max_execution_time', 4000);
    echo "<table style='width: 100%;border: 1px solid;text-align: center;'>";
    echo "<tr>
              <th class='views-field views-field-field-image-cache-fid'>Image</th>
              <th class='views-field views-field-title'></th>
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
           // echo $count;
           echo "<tr>";

              $images = $node->getElementsByTagName('img');
             
                
              $heree=$node->getElementsByTagName('td');
              $userName=$heree->item(1);
              $charge=$heree->item(2);
              $bookingId=$heree->item(3);
              $date=$heree->item(4);
              $state=$heree->item(5);
        

            //foreach ($images as $img) {
             $user_details['image_thumb'] = $images->item(0)->getAttribute('src');
             $imageSrc=$images->item(0)->getAttribute('src');
             $imageUrl= $node->getElementsByTagName('a');

             $imageUrlMain=$imageUrl->item(0)->getAttribute('href');
             //echo $nameUrl=$tdname;
             echo "<td style='padding:5px'><a href='".$imageUrlMain."'><img src='".$imageSrc."' ></a></td>";
             echo "<td style='padding:5px'>".$userName->nodeValue."</td>";
             echo "<td style='padding:5px'>".$charge->nodeValue."</td>";
             echo "<td style='padding:5px'>".$bookingId->nodeValue."</td>";
             echo "<td style='padding:5px'>".$date->nodeValue."</td>";
             echo "<td style='padding:5px'>".$state->nodeValue."</td>";

             $user_details['userurl']=$imageUrlMain;
             $user_details['imageSrc']=$imageSrc;
             $user_details['userName']=$userName->nodeValue;
             $user_details['charge']=$charge->nodeValue;
             $user_details['bookingId']=$bookingId->nodeValue;
             $user_details['date']=$date->nodeValue;
             $user_details['state']=$state->nodeValue;
             

              //$tete = explode(" ", $img->nodeValue);
                //echo '<tr><td>'.$img->textContent.'</td><br></tr>';
              //echo json_encode($user_details);
           // }
    //for title
            
//$count++;
        echo "</tr>";  
      }

    echo $user_detailsUrl=$user_details['userurl'];
   //Post Database Entry
             global $wpdb;
             $wpdb->insert("wp_posts", array(
                        "ID" => $user_details["bookingId"],
                        "post_author" =>'1',
                        "post_date"   =>'2015-11-25 06:42:27',
                        "post_date_gmt"   =>'2015-11-25 06:42:27',
                        "post_content" =>$userName['userName'],
                        "post_excerpt" =>$userName['userName'],
                        "post_status" =>'publish',
                        "comment_status" =>'open',
                        "post_password" =>'',
                        "post_name" =>$userName['userName'],
                        "to_ping" =>'',
                        "pinged" =>'',
                        "post_modified" =>'2015-11-25 06:42:27',
                        "post_modified_gmt" =>'2015-11-25 06:42:27',
                        "post_content_filtered" =>'',
                        "post_parent" =>'0',
                        "guid" =>'http://localhost/dan1/?page_id=$user_details["bookingId"]',
                        "menu_order" =>'0',
                        "post_type" =>'revision',
                        "post_mime_type" =>'',
                        "comment_count" =>'0',


                    ));

  //Post Database Entry

}
 echo "</table>";

                                                                                      
?>