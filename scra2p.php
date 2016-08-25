<?php
error_reporting(0);
require('./wp-load.php'); 
ini_set('max_execution_time', 4000000000000);
    echo "<table style='width: 100%;text-align: center;'>";
   /* echo "<tr>
              <th class='views-field views-field-field-image-cache-fid'>Image</th>
              <th class='views-field views-field-title'>Name</th>
              <th class='views-field views-field-field-chargecount-value'>Charge Descr.</th>
              <th class='views-field views-field-field-dateabp-value'>Date Arrested/Booked</th>
              <th class='views-field views-field-field-idbooking-value'>Booking ID</th>
              <th class='views-field views-field-tid'>State and Locale</th>
          </tr>";*/


       for ($page = 1; $page<=1; $page++) {
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
               
              //print_r($woData);
              $heree=$woData->getElementsByTagName('td');
              $imageSrc = $woData->getElementsByTagName('img');
              $imageurl=$woData->getElementsByTagName('a');
              $imageUrlMain=$imageurl->item(0)->getAttribute('href');


              $imaged=$imageSrc->item(0)->getAttribute('src');
              //$useUrl=$heree->item(1)->nodeValue;
              $username = $heree->item(1)->nodeValue;
              $useUrl=  str_replace(' ', '-', trim($username));
              $userUrlMain=strtolower($useUrl);

              $charge = $heree->item(2)->nodeValue;
              $bookingId = $heree->item(3)->nodeValue;
              $date = $heree->item(4)->nodeValue;
              $state = $heree->item(5)->nodeValue;



             $user_details['userurl']=$imageUrlMain;
             $user_details['username']=$username;
             $user_details['charge']=$charge;
             $user_details['bookingId']=$bookingId;
             $user_details['date']=$date;
             $user_details['state']=$state;
               // echo "</tr>";

              //for particular user

              $urlData  = 'http://arrestfiles.org/publicinfo/'.$userUrlMain.'';
              //$main_url = 'http://www.viewit.ca/';
              $domData  = new DOMDocument;
              $htmlData = file_get_contents($urlData);
              @$domData->loadHTML($htmlData);
              $finder    = new DomXPath($domData);


              $classnameData = 'af22-content-22';
      

              $nodes = $finder->query("//*[contains(@class, '$classnameData')]");

             
             foreach($nodes as $nodeUserData){
                  
                  echo "<tr>";
                  
                   $nodeUserImg=$nodeUserData->getElementsByTagName('img');
                  

                   $nodeUserSpan=$nodeUserData->getElementsByTagName('span');
                   
                   $ulData=$nodeUserData->getElementsByTagName('ul');

                   $nodeUserdiv=$nodeUserData->getElementsByTagName('div');

                   $user_details['dateOfbrith']=$nodeUserSpan->item(21);

                   $finder    = new DomXPath($domData);


                  $classnameData = 'af22-textcontainer-22';
          

                  $nodescheck = $finder->query("//*[contains(@class, '$classnameData')]");


                  $nodedivCharges=$nodeUserdiv->item(11)->nodeValue;
                // echo "<pre>";
                // print_r($nodedivCharges);
                // echo "</pre>";
                  $nodedivRace=$nodescheck->item(5)->nodeValue;
                  $nodedivGender=$nodescheck->item(6)->nodeValue;
                  $nodedivHeightFt =$nodescheck->item(7)->nodeValue;
                  $nodedivGenderCm=$nodescheck->item(8)->nodeValue;
                  $nodedivWeight=$nodescheck->item(9)->nodeValue;
                  $nodedivHair_Color=$nodescheck->item(10)->nodeValue;
                  $nodediveye_Color=$nodescheck->item(11)->nodeValue;
                  $nodedivBookId=$nodescheck->item(12)->nodeValue;
                  $nodedivDateOfArrest=$nodescheck->item(13)->nodeValue;
                  $nodedivAgeWhenArrest=$nodescheck->item(14)->nodeValue;
                  $nodedivArresting_Agency=$nodescheck->item(15)->nodeValue;


                  $nodedivCharge_Description=$nodescheck->item(16)->nodeValue;
                  $nodedivCharge_Bond=$nodescheck->item(17)->nodeValue;

                  $Local_Public_Resources1=$ulData->item(1)->nodeValue;
                  $Local_Public_Resources2=$ulData->item(2)->nodeValue;
                  $Local_Public_Resources3=$ulData->item(3)->nodeValue;
                  $Local_Public_Resources4=$ulData->item(4)->nodeValue;


                  $jailData1=$ulData->item(5)->nodeValue;

                  $imageusere=$nodeUserImg->item(0)->getAttribute('src');
                  $user_details['imageUserr']=$imageusere;




                  $string = $nodedivCharge_Description;
                  $wordlist = array("Charge Desc: ", "Charge","Desc", "1","Bond","500.00",":","Description");

                  foreach ($wordlist as &$word) {
                      $word = '/\b' . preg_quote($word, '/') . '\b/';
                  }

                   $string = preg_replace($wordlist, '', $string);

                   $FinalString=str_replace(':', '.', trim($string));


                 $mainData="<div class='gtr'><div class='upperMain'>
                 <div class='imaged'><img src='$imageusere'></div>
                 <div class='userinfo'><span class='uname'>".$user_details['username']."</span></div>
                 <div class='userinfo'><span class='uname'>Charge Desc".$FinalString." </span></div>
                 <div class='userinfo'><span class='uname'>Arrest/Booking Date :&nbsp; ".$user_details['date']."</span></div>
                 <div class='userinfo'><span class='uname'>Booking ID :&nbsp;".$user_details['bookingId']."</span></div>
                 <div class='af22-moreinfo-22'> 
                 <h2 class='af22-h2title-22'>More Information</h2>
                <!--af22-morinfocontent-22-->
                 <div class='af22-morinfocontent-22'>
                  <p class='af22-seperator-22'>
                          </p><div class='af22-textcontainer-22'>
                          <span class='af22-span-22'>Name:</span> ".$user_details['username']."</div>

                          <p class='af22-seperator-22'>
                          </p><div class='af22-textcontainer-22'>
                          <span class='af22-span-22'>".$nodeUserSpan->item(21)->nodeValue."</span>
                         </div>

                          <p class='af22-seperator-22'>
                          </p><div class='af22-textcontainer-22'>
                          <span class='af22-span-22'>".$nodedivRace."</div>
                          <div class='af22-textcontainer-22'>
                          <span class='af22-span-22'>".$nodedivGender."</div>
                          <div class='af22-textcontainer-22'>
                          <span class='af22-span-22'>".$nodedivHeightFt."</div>
                          <div class='af22-textcontainer-22'>
                          <span class='af22-span-22'>".$nodedivGenderCm."</div>
                          <div class='af22-textcontainer-22'>
                          <span class='af22-span-22'>".$nodedivWeight."</div>
                          <div class='af22-textcontainer-22'>
                          <span class='af22-span-22'>".$nodedivHair_Color."</div>
                          <div class='af22-textcontainer-22'>
                          <span class='af22-span-22'>".$nodediveye_Color."</div>
                           
                          <p></p>

                          <p class='af22-seperator-22'>
                          </p><div class='af22-textcontainer-22'>
                          <span class='af22-span-22'>".$nodedivBookId."</div>


                          <div class='af22-textcontainer-22'>
                          <span class='af22-span-22'>".$nodedivDateOfArrest."</span>
                          </div>
                                               


                          <div class='af22-textcontainer-22'>
                          <span class='af22-span-22'>".$nodedivAgeWhenArrest." </span></div>
                          <div class='af22-textcontainer-22'>
                          <span class='af22-span-22'>".$nodedivArresting_Agency."</span></div>
                          <p></p>

                          <p id='chargedescr' class='af22-seperator-22'>
                          </p><div class='af22-textcontainer-22' style='margin:0 0 10px 0;'>
                          
                          ".$nodedivCharge_Description."
                          </div>

                          <p></p>
                            



                          <p class='af22-seperator-22'>

                          </p><p>
                          IMPORTANT NOTICE:<br>
                          <b>All persons listed on this site are innocent till proven guilty.</b> 
                          This information does NOT constitute a criminal record/history, and may 
                          not be interpreted as such. Information provided herein may not be relied
                           upon for any type of legal action. This information is provided as a 
                           convenience to the general public and therefore no warranty is expressed 
                           or implied as to the accuracy, reliability, completeness, or timeliness of
                            any information obtained through the use of this service. </p>
                          <p>
                          This information was or is current at or around the time of publishing.
                           Any new information, i.e. new charges, dropped charges, etc.., please 
                           contact the appropriate State or County records dept.
                          </p>

                          <p></p>

                          </div>
                          <!--//af22-morinfocontent-22-->
                          </div>

                          <div id='node-232106'>

                          <div class='padding'>
                          <table class='contentpaneopen'>
                            <tbody><tr>
                              <td class='contentheading'>
                                
                                        
                              </td>
                            </tr>
                            <tr>
                              <td valign='top' colspan='2'>

                                <div class='meta'>
                                        <span class='submitted'></span>
                                    
                                      </div>
                              </td>
                            </tr>
                            <tr>
                              <td valign='top' colspan='2'>
                                <div class='content'>
                                  <h2 class='ai-titl'>Local Public Resources</h2>

                          <div class='ai-cities-towns'>
                          <h3 class='ai-titl ai-head'>Cities/Communities/Towns - Fort Lauderdale FL and Surrounding Areas</h3>
                          <div class='ai-box'><ul><li>Arlington Park</li>
                          <li>Bonnie Loch</li>
                          <li>Boulevard Gardens</li>
                          <li>Broadview</li>
                          <li>Broadview Park</li>
                          <li>Broward Estates</li>
                          <li>Browardale</li>
                          <li>Carver Ranches</li>
                          <li>Coconut Creek</li>
                          <li>Cooper City</li>
                          <li>Coral Estates</li>
                          <li>Coral Springs</li>
                          <li>Coral Woods</li>
                          <li>Crystal Lake</li>
                          <li>Dania Beach</li>
                          <li>Davie</li>
                          <li>Deerfield Beach</li>
                          <li>Fair Gate</li>
                          <li>Fort Lauderdale</li>
                          <li>Franklin Park</li>
                          <li>Golden Isles</li>
                          <li>Hacienda Village</li>
                          <li>Hallandale Beach</li>
                          <li>Hillsboro Beach</li>
                          <li>Hillsboro Pines</li></ul></div>
                          <div class='ai-box'><ul><li>Hillsboro Ranches</li>
                          <li>Hollywood</li>
                          <li>Hollywood Beach Gardens</li>
                          <li>Hollywood Ridge Farms</li>
                          <li>Jenada Isles</li>
                          <li>Kendall Green</li>
                          <li>Lake Forest</li>
                          <li>Lauderdale Lakes</li>
                          <li>Lauderdale-by-the-Sea</li>
                          <li>Lauderhill</li>
                          <li>Lazy Lake</li>
                          <li>Leisureville</li>
                          <li>Lighthouse Point</li>
                          <li>Loch Lomond</li>
                          <li>Margate</li>
                          <li>Margate Estates</li>
                          <li>Melrose Park</li>
                          <li>Miami Gardens</li>
                          <li>Miramar</li>
                          <li>North Andrews Gardens</li>
                          <li>North Lauderdale</li>
                          <li>Oak Point</li>
                          <li>Oakland Park</li>
                          <li>Palm Aire</li>
                          <li>Parkland</li></ul></div>
                          <div class='ai-box'><ul><li>Pembroke Park</li>
                          <li>Pembroke Pines</li>
                          <li>Pine Island Ridge</li>
                          <li>Plantation</li>
                          <li>Playland Estates</li>
                          <li>Playland Village</li>
                          <li>Pompano Beach</li>
                          <li>Pompano Beach Highlands</li>
                          <li>Pompano Estates</li>
                          <li>Pompano Park</li>
                          <li>Port Everglades</li>
                          <li>Port Everglades Junction</li>
                          <li>Port Laudania</li>
                          <li>Ravenswood Estates</li>
                          <li>Riverland</li>
                          <li>Rock Island</li>
                          <li>Ro-Len Lake Gardens</li>
                          <li>Rolling Oaks</li>
                          <li>Roosevelt Gardens</li>
                          <li>Royal Palm Ranches</li>
                          <li>Saint George</li>
                          <li>Sea Ranch Lakes</li>
                          <li>Silver Shores</li>
                          <li>Southwest Ranches</li>
                          <li>Sunrise</li></ul></div>
                          <div class='ai-box'><ul><li>Sunrise Key</li>
                          <li>Sunshine Acres</li>
                          <li>Sunshine Park</li>
                          <li>Sunshine Ranches</li>
                          <li>Tamarac</li>
                          <li>Tedder</li>
                          <li>Terra Mar</li>
                          <li>Twin Lakes</li>
                          <li>Utopia</li>
                          <li>Village Park</li>
                          <li>Washington Park</li>
                          <li>West Hollywood</li>
                          <li>West Park</li>
                          <li>Weston</li>
                          <li>Wilton Manors</li>
                          <li>Woodsetter North</li></ul></div>
                          </div>  
                            
                            
                          <div class='ai-jail-loc'>
                          <h3 class='ai-titl ai-head'>Jail Location(s) in Fort Lauderdale FL and Surrounding Areas</h3>
                          <ul>
                          <li>Central Intake (Inmate Booking) Main Jail Bureau - 555 SE 1st Ave. Ft. Lauderdale, FL 33310</li>
                          <li>Joseph V. Conte Facility - 1351 NW 27th Ave. Pompano Beach, FL 33069</li>
                          <li>North Broward Bureau - 1550 NW 30th Avenue Pompano Beach, FL 33069</li>
                          <li>Paul Rein Detention Facility - 2421 NW 16 Street Pompano Beach, FL 33069</li>
                          </ul>
                          </div>

                          <h3 class='ai-titl ai-head'>County and Local Law Enforcement Public Information</h3>

                          <h3 class='ai-titl'><a href='http://sheriff.org/apps/arrest' target='_blank' class='ext'>Broward County Sheriff's Arrest Search</a></h3>
                          <span class='ai-quot'>'To view the arrest information and photograph of a currently incarcerated Broward County inmate, enter his or her name (Last and/or First) in the search ...'</span>
                          <span class='ai-src'>http://sheriff.org/apps/arrest</span>

                          <h3 class='ai-titl'><a href='http://sheriff.org/sexualpredators' target='_blank' class='ext'>Broward County Sheriff's Sexual Offenders and Sexual Predators Search</a></h3>
                          <span class='ai-quot'>'The Broward Sheriff's Office want to arm you with the tools to keep your children safe. BSO has developed an online interactive mapping program that allows you to plot the location of registered sexual offenders and predators in Broward County....'</span>
                          <span class='ai-src'>http://sheriff.org/sexualpredators</span>

                          <h3 class='ai-titl'><a href='http://sheriff.org/about_bso/other/most_wanted' target='_blank' class='ext'>Broward County Sheriff's Most Wanted Search</a></h3>
                          <span class='ai-quot'>'BSO values and recognizes the need for public assistance in tracking fugitives. If you have information on the whereabouts or activities of any person on the list, call Broward County Crime Stoppers...'</span>
                          <span class='ai-src'>http://sheriff.org/about_bso/other/most_wanted</span>

                          <h3 class='ai-titl'><a href='http://www.flpd.org/index.aspx?page=207' target='_blank' class='ext'>City of Fort Lauderdale Police Department Most Wanted</a></h3>
                          <span class='ai-quot'>'Anyone with information concerning a suspect's location is asked to contact the Fort Lauderdale Police Department - Fugitive Unit at (954) 828-5512 or FLPDMostWanted@fortlauderdale.gov'</span>
                          <span class='ai-src'>http://www.flpd.org/index.aspx?page=207</span>

                          <h3 class='ai-titl'><a href='http://sheriff.org' target='_blank' class='ext'>Broward County Sheriff's Office</a></h3>
                          <span class='ai-quot'>'Broward County Sheriff's Office A Full Service Public Safety Agency'</span>
                          <span class='ai-adrs'>Address: 2601 W. Broward Blvd. Ft. Lauderdale, FL 33312 </span>
                          <span class='ai-src'>http://sheriff.org</span>

                          <h3 class='ai-titl'><a href='http://www.flpd.org' target='_blank' class='ext'>City of Fort Lauderdale Police Department</a></h3>
                          <span class='ai-quot'>'City of Fort Lauderdale Police Department.'</span>
                          <span class='ai-adrs'>Address: 1300 W. Broward Boulevard Fort Lauderdale, FL 33312 </span>
                          <span class='ai-src'>http://www.flpd.org</span>

                          <h3 class='ai-titl'><a href='http://www.coralsprings.org/police' target='_blank' class='ext'>Coral Springs Police Department</a></h3>
                          <span class='ai-quot'>'Mission Statement To provide professional, high quality and effective police service in partnership with the community..'</span>
                          <span class='ai-adrs'>Address: 2801 Coral Springs Drive - Coral Springs, FL 33065</span>
                          <span class='ai-src'>http://www.coralsprings.org/police</span>

                          <h3 class='ai-titl'><a href='http://www.hollywoodpolice.org' target='_blank' class='ext'>Hollywood Police Department</a></h3>
                          <span class='ai-quot'>'The Hollywood Police Department has adopted City Manager Cameron Benson's Mission, Diversity, and Values Statement for the City of Hollywood....'</span>
                          <span class='ai-adrs'>Address: 3250 Hollywood Boulevard, Hollywood, Florida 33021</span>
                          <span class='ai-src'>http://www.hollywoodpolice.org</span>

                          <h3 class='ai-titl'><a href='http://www.ppines.com/police' target='_blank' class='ext'>Pembroke Pines Police Department</a></h3>
                          <span class='ai-quot'>'The Pembroke Pines Police Department Is dedicated to providing the highest level of professional, caring police service to our community...'</span>
                          <span class='ai-adrs'>Address: 9500 Pines Blvd Pembroke Pines, FL 33025</span>
                          <span class='ai-src'>http://www.ppines.com/police</span>

                          <h3 class='ai-titl ai-head'>County and Local Judicial Public Information</h3>

                          <h3 class='ai-titl'><a href='http://www.clerk-17th-flcourts.org' target='_blank' class='ext'>Broward County Clerk Court</a></h3>
                          <span class='ai-quot'>'Broward County Clerk Court....'</span>
                          <span class='ai-src'>http://www.clerk-17th-flcourts.org</span>a

                          <h3 class='ai-titl ai-head'>County and Local Government Public Information</h3>

                          <h3 class='ai-titl'><a href='http://broward.org' target='_blank' class='ext'>Broward County, Florida - County Website</a></h3>
                          <span class='ai-quot'>County Website</span>
                          <span class='ai-src'>http://broward.org</span>

                          <h3 class='ai-titl'><a href='http://ci.ftlaud.fl.us' target='_blank' class='ext'>City of Fort Lauderdale Website</a></h3>
                          <span class='ai-quot'>'City of Fort Lauderdale, Florida - Official Website'</span>
                          <span class='ai-src'>http://ci.ftlaud.fl.us</span>

                          <h3 class='ai-titl'><a href='http://www.hollywoodfl.org' target='_blank' class='ext'>City of Hollywood Florida Website</a></h3>
                          <span class='ai-quot'>'City of Hollywood, Florida - Official Web Site'</span>
                          <span class='ai-src'>http://www.hollywoodfl.org</span>      </div>
                              </td>
                            </tr>
                          </tbody></table>
                          </div>

                          </div>

                 </div>

                 ";
                  
echo  $mainData;
                //echo "<div class='userinfo'><span class='uname'>".$user_details['username']."</span></div>";



            echo "</tr>";
            
            //for particular use

              $timestamp = date("Y-m-d H:i:s", strtotime($user_details['date']));
              $postname=trim($user_details['username']);
              $useUrlm=  str_replace(' ', '-', trim($postname));
              $userUrlMainm=strtolower($useUrlm);

               $postId=trim($user_details['date']);
               $username=trim($user_details['username']);
               global $wpdb;

                     $sqlSelectPost=$wpdb->get_results("SELECT * FROM `wp_posts` WHERE `bookingID` LIKE '%$postId%'");
                 
                     $sid=$sqlSelectPost[0]->bookingID;
                

                     if(!$sid) {
                      $wpdb->insert("wp_posts", array(
                        "bookingID"=>$date,
                       "post_author" =>'1',
                        "post_date"   =>$timestamp,
                        "post_date_gmt"   =>$timestamp,
                        "post_content" =>$mainData,
                        "post_excerpt" =>$mainData,
                        "post_status" =>'publish',
                        "comment_status" =>'open',
                        "post_password" =>'',
                        "post_name" =>trim($userUrlMainm),
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
         
    }

       
      
 echo "</table>";
// Register Post Data
// Add Featured Image to Post

?>