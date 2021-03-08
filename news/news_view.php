<?php
/**
 * survey_view.php along with index.php allows us to view surveys 
 * 
 * @package SurveySez
 * @author Kyrrah Nork <kyrrahnork@example.com>
 * @version 1.0 2021/02/18
 * @link http://www.example.com/
 * @license https://www.apache.org/licenses/LICENSE-2.0
 * @see survey_view.php
 * @see Pager.php 
 * @todo none
 */

# '../' works for a sub-folder.  use './' for the root  
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials 
 
# SQL statement
// $sql = "select Title, SurveyID, Description from winter2021_surveys";
// $sql = "select CONCAT(a.FirstName, ' ', a.LastName) AdminName, s.SurveyID, s.Title, s.Description, 
// date_format(s.DateAdded, '%W %D %M %Y %H:%i') 'DateAdded' from "
// . PREFIX . "surveys s, " . PREFIX . "Admin a where s.AdminID=a.AdminID order by s.DateAdded desc";

$sql = "select FeedID, CategoryID, FeedName, FeedLink from Feeds";

#Fills <title> tag. If left empty will default to $PageTitle in config_inc.php  
$config->titleTag = 'Surveys made with love & PHP in Seattle';

#Fills <meta> tags.  Currently we're adding to the existing meta tags in config_inc.php
$config->metaDescription = 'Seattle Central\'s IT262 Class Surveys are made with pure PHP! ' . $config->metaDescription;
$config->metaKeywords = 'News,PHP,Fun,Bran,Regular,Regular Expressions,'. $config->metaKeywords;

//adds font awesome icons for arrows on pager
$config->loadhead .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';

/*
$config->metaDescription = 'Web Database ITC281 class website.'; #Fills <meta> tags.
$config->metaKeywords = 'SCCC,Seattle Central,ITC281,database,mysql,php';
$config->metaRobots = 'no index, no follow';
$config->loadhead = ''; #load page specific JS
$config->banner = ''; #goes inside header
$config->copyright = ''; #goes inside footer
$config->sidebar1 = ''; #goes inside left side of page
$config->sidebar2 = ''; #goes inside right side of page
$config->nav1["page.php"] = "New Page!"; #add a new page to end of nav1 (viewable this page only)!!
$config->nav1 = array("page.php"=>"New Page!") + $config->nav1; #add a new page to beginning of nav1 (viewable this page only)!!
*/

# END CONFIG AREA ---------------------------------------------------------- 

get_header(); #defaults to theme header or header_inc.php
?>
<h3 align="center">News Links</h3>
<?php
#reference images for pager
//$prev = '<img src="' . $config->virtual_path . '/images/arrow_prev.gif" border="0" />';
//$next = '<img src="' . $config->virtual_path . '/images/arrow_next.gif" border="0" />';

#images in this case are from font awesome
$prev = '<i class="fa fa-chevron-circle-left"></i>';
$next = '<i class="fa fa-chevron-circle-right"></i>';

# Create instance of new 'pager' class
$myPager = new Pager(10,'',$prev,$next,'');
$sql = $myPager->loadSQL($sql);  #load SQL, add offset

# connection comes first in mysqli (improved) function
$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));


if(mysqli_num_rows($result) > 0)
{#records exist - process
	if($myPager->showTotal()==1){$itemz = "survey";}else{$itemz = "surveys";}  //deal with plural
    
	echo '
		<table class="table table-hover">
		<thead>
		<tr>
			<th scope="col">Title</th>
			<th scope="col">Description</th>
		</tr>
		</thead>
		<tbody>  
	';	
	
	echo '<div align="center">We have ' . $myPager->showTotal() . ' ' . $itemz . '!</div>';

	while($row = mysqli_fetch_assoc($result)){
		
		$url = 'http://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI'].'';

		$i = substr($url, -1);

		if( $i == ((int)$row['CategoryID']))
		{
			echo '
				<tr>
				<th><a href="' . VIRTUAL_PATH . 'news/feeds.php?id=' . (int)$row['FeedID'] . '">' . dbOut($row['FeedName']) . '</a></th>
				<td>' . dbOut($row['FeedLink']) . '</td>
				</tr>
			';
			
		}

	}

	echo '
			</tbody>
		</table>	
	';
	echo $myPager->showNAV(); # show paging nav, only if enough records	 
}else{#no records
    echo "<div align=center>What! No muffins?  There must be a mistake!!</div>";	
}
@mysqli_free_result($result);

get_footer(); #defaults to theme footer or footer_inc.php
?>
