<?php

/** Test suite  */

class FafTestPage extends FeedWordPressAdminPage
{

protected $boxes_by_methods = array();



public function display()
{
    
    $this->boxes_by_methods = array('faf_filters_dbug' => 'Advanced filters Debugging');
  $this->faf_filters_dbug();
}

function faf_filters_dbug()
{
	$post = array (
	  'post_title' => ' Valve begint besloten bètatest voor Linux-versie Linuxversie Steam in Linux',
	  'post_content' => 'ZOETERMEER – ZOETERMEER – <br />Valve is begonnen met een besloten bètatest voor de Linux-versie van zijn gamingplatform Steam. 
		<span id="GUROGOROR"> <img src="http://localhost/rss_images/basszje_snowboard.jpg?w=10&h=100" /></span><b>MOTHERFUCKERS</b><i>Dacht ik ook</i> 
		<img src="http://unit-test.weblogmechanic.com/blergh.jpg">
		 <img alt="alt-text" src="http://www.server.tld/2013/04/picture.jpg?w=300&h=98" width="300" height="98" />
		<blockquote>Deelnemers</blockquote> kunnen kiezen uit 26 verschillende games, waaronder Team Fortress 2. Valve is van plan de komende tijd meer mensen toe  te laten tot de bètatest.	 <img src="http://localhost/rss_images/fotoxx_exampl.jpg" width="450" height="251" />
		<a href=\'http://tweakers.net/nieuws/85391/blodhoung\'>Meer onzin</a>- <img src="http://localhost/rss_images/balzaka.jpg">
		
		<p>Alumni of the old Kyiv-Mohyla Academy have played an important role in Ukrainian professional life. Many hetmans of Zaporozhian Cossacks, political leaders of Ukraine in the 17th and 18th centuries, were educated here. These include Ivan Mazepa, Pylyp Orlyk, Pavlo Polubotok, Ivan Skoropadsky and Ivan Samoylovych. The Grand Chancellor of Russia Alexander Bezborodko was of Ukrainian origin and an alumnus. The Kyiv-Mohyla Academy was a religious school of note in the Orthodox world and archbishops of the Russian Empire such as Stephen Yavorsky and Feofan Prokopovich as well as the metropolitan bishop of Rostov Dimitry of Rostov were all alumni.</p>
		',
	  'post_excerpt' => '<b>Valve</b> is begonnen <em>met</em> een besloten b&egrave;tatest voor de Linux-versie van zijn gamingplatform Steam. 
		<span> <img src="http://localhost/rss_images/basszje_snowboard.jpg"></span><b>MOTHERFUCKERS</b><i>Dacht ik ook</i>
		<blockquote>Deelnemers</blockquote> kunnen kiezen uit 26 verschillende games, waaronder Team Fortress 2. Valve is van plan de komende tijd meer mensen toe  te laten tot de b&egrave;tatest.<img src="http://localhost/rss_images/fotoxx_exampl.jpg" width="450" height="251"><a href="http://www.rssmix.com/blodhoung">Meer onzin</a> - <img src="http://localhost/rss_images/balzaka.jpg">',
	  'post_date_gmt' => '2012-11-07 07:42:00',
	  'post_date' => '2012-11-07 09:42:00',
	  'post_modified_gmt' => '2012-11-07 07:42:00',
	  'post_modified' => '2012-11-07 09:42:00',
	  'post_status' => 'publish',
	  'comment_status' => 'open',
	  'ping_status' => 'open',
	  'guid' => 'tag:localhost://51f739b51776315701d282f6193ab561',
	  'meta' => 
	  array (
	    'enclosure' => array('http://unit-test.weblogmechanic.com/PEEN4936_-_Version_2.jpg 
 		0 image/jpeg'),
	    'syndication_source' => 'RSSMix.com Mix ID 3587188',
	    'syndication_source_uri' => 'http://www.rssmix.com/',
	    'syndication_source_id' => 'http://localhost/algemeen.rss',
	    'syndication_feed' => 'http://localhost/algemeen.rss',
	    'syndication_feed_id' => 23,
	    'syndication_permalink' => 'http://tweakers.net/nieuws/85391/valve-begint-besloten-betatest-voor-linux-versie-steam.html',
	    'syndication_item_hash' => '5372b0a8280ff40a59b0739714f25015',
	  ),
	  'post_type' => 'post',
	  'post_author' => 14,
	  'tax_input' => 
	  array (
	    'post_tag' => 
	    array (
	    ),
	    'post_format' => 
	    array (
	    ),
	    'category' => 
	    array (
	      0 => 98,
	      1 => 198,
	    ),
	  ),
	);



	// init faf
	$doRun = true; 
	
if ($doRun)
{
	$faf = new feedwordpressAdvancedFilters();

	$link = "http://localhost/algemeen.rss";
	$book =  get_bookmark(20);


	$Slink = new SyndicatedLink($book);


	$fakeP = (object) "";
	$fakeP->link = $Slink;	

	// run decide 
	
	$post = $faf->faf_decide_filter($post, $fakeP);

	echo "<pre>"; 
 print_r($post);
	echo "</pre>";  
}
}
} // class
$f = new FafTestPage();
$f->display();
?>
