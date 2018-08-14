<?

/**

*  - Paging Class

*

* This file impliments the paging class.

*/

/**

* This class deals with paging

*

* This class controls the html table paging variable.

* @package Paging

*/

class clspaging

{

	/**

	* Store the name of list which we display in the page.

	* This property is used particularly when we implement two paging in one page.

	* Ex.one page has two paging like firm and message listing then value of $pagelistname is just like a firmlist,messagelist

	* @var string

	*/

	var $pagelistname;



	/**#@+

	* Variables for Paging the search result.

	*

	* @var string

	*/

	var $numofrecs;

	var $numofrecsperpage;

	var $numofpages;

	var $currentpage;

	var $recsfrom;

	var $recsto;

	var $limit;

	var $pageLinksRange;

	var $sortby;

	var $sorttype;

	var $implementclassname;

	/**#@-*/

	/**

	* Constructor

	* @param object Connection Object

	*/

	function clspaging($pagelistname='', $connect='')

	{

		//$this->clsadminbase($connect);

		$this->numofrecs=0;

		$this->numofrecsperpage=20;

		$this->numofpages =0;

		$this->currentpage =0;

		$this->recsfrom=0;

		$this->recsto ="";

		$this->limit="";

		$this->pageLinksRange=5;

		$this->sorttype="";

		$this->sortby="";

		$this->pagelistname=$pagelistname;

		$this->implementclassname = get_class($this);

	}

	/**

	* To set class property by GET variable.

	*

	* This function will be called by every page function in order to set object property from GET variables.

	* It also calls setgetvars() of parent class.

	*/

	function setgetvars()

	{

		//parent::setgetvars();

		if(isset($_GET[$this->implementclassname."_".$this->pagelistname.'_cp']))

			$this->currentpage=$_GET[$this->implementclassname."_".$this->pagelistname.'_cp'];

		if(isset($_GET['currentpage']))

			$this->currentpage=$_GET['currentpage'];

		if($this->currentpage=="" or !is_numeric($this->currentpage))	$this->currentpage=0;





	}

	/**

	* To set class property by POST variable.

	*

	* This function will be called by every page function in order to set object property from POST variables.

	* It also calls setpostvars() of parent class.

	*/

	function setpostvars()

	{

		//parent::setpostvars();

		if(isset($_POST[$this->implementclassname.'_'.$this->pagelistname.'_currentpage']))

			$this->currentpage=$_POST[$this->implementclassname.'_'.$this->pagelistname.'_currentpage'];

		if($this->currentpage=="" or !is_numeric($this->currentpage)) $this->currentpage=0;

		if(isset($_POST[$this->implementclassname.'_'.$this->pagelistname.'_sortby']))

			$this->sortby=$_POST[$this->implementclassname.'_'.$this->pagelistname.'_sortby'];

		if(isset($_POST[$this->implementclassname.'_'.$this->pagelistname.'_sorttype']))

			$this->sorttype=trim($_POST[$this->implementclassname.'_'.$this->pagelistname.'_sorttype']);

		if(isset($_POST[$this->implementclassname.'_'.$this->pagelistname.'_numofrecsperpage']))

			$this->numofrecsperpage=trim($_POST[$this->implementclassname.'_'.$this->pagelistname.'_numofrecsperpage']);

		if($this->numofrecsperpage<=0 || !is_numeric($this->numofrecsperpage))

			$this->numofrecsperpage=20;

	}



	/**

	* Generate the limt for SQL query.

	*

	* This function is called from watopic.cls.php file's GetTopicArrayByTopicTypes() function.

	* Parameter value should pass when a page contains multiple paging.

	* @param int Value of current page

	*/

	function getcurrpagevardb($currentpage="")

	{

		if($this->numofrecsperpage==0)

		{

			$this->limit = '';

			return;

		}

		if($currentpage!="" && $currentpage >0 && is_numeric($this->currentpage))

			$this->currentpage=$currentpage;

		if($this->numofrecs == 0)

		{

			$this->recsfrom = 0;

			$this->recsto = 0;

			return;

		}

		$this->numofpages=ceil($this->numofrecs/$this->numofrecsperpage);

		if($this->currentpage==0)

			$this->currentpage=1;

		if($this->currentpage > $this->numofpages)

			$this->currentpage=$this->numofpages;

		$this->limit=" LIMIT ".(( $this->currentpage - 1 )* $this->numofrecsperpage ) . "," . $this->numofrecsperpage ;

		$this->recsfrom=( $this->currentpage - 1 )* $this->numofrecsperpage+1 ;

		$this->recsto=$this->recsfrom+$this->numofrecsperpage-1;

		if ($this->recsto > $this->numofrecs)

			$this->recsto=$this->numofrecs;

	}

	/**

	* Generate HTML for entering no. of records display in the page .

	*

	* This function is called from

	* If user not enter the value then it display $this->numofrecsperpage variable value record.

	* By default value of $this->numofrecsperpage variable set into constuctor of this class.

	*/

	function GetnumberOfRecordsPerPage()

	{

		echo "<table width='100%'  border='0' cellspacing='0' cellpadding='0' class='size1normal'>

					<tr>

						<td><div align='right'><strong> No of Records :&nbsp; </strong></div></td>

						<td width='5%'><input type='text' name='clspaging_numofrecsperpage' size='5' value='$this->numofrecsperpage'></td>

					</tr>

				</table>";

	}

	/**

	* Generate HTML for page links

	*

	* This function is called from wadiscussion.php file

	*

	* @param string	URL of Page

	* @param boolean

	*/

	function writeHTMLpagesrange($url,$javascript=false,$URL_IMG="")

	{

		//echo "Function is called";

		$rangecurrent=0;

		$rangetotal=0;

		$rangeprevpg=1;

		$rangenextpg=1;

		$rangecurrent=ceil($this->currentpage/$this->pageLinksRange);

		$rangetotal=ceil($this->numofpages/$this->pageLinksRange);

		if ($rangecurrent>1)

			$rangeprevpg=($rangecurrent-2)*$this->pageLinksRange+1;

		if ($rangetotal>$rangecurrent)

			$rangenextpg=($rangecurrent)*$this->pageLinksRange+1;

		$rangecurrentstart=($rangecurrent-1)*$this->pageLinksRange+1;

		$rangecurrentend=($rangecurrent-1)*$this->pageLinksRange+$this->pageLinksRange;

		if ($rangecurrentend>$this->numofpages)

		{

			$rangecurrentstart=$this->numofpages-$this->pageLinksRange+1;

			if($rangecurrentstart<1)

				$rangecurrentstart=1;

			$rangecurrentend=$this->numofpages;

		}

		$varprefix=$this->implementclassname."_".$this->pagelistname;

		echo "<table cellpadding='0' cellspacing='0' width='100%' border='0' align='center'><tr>".

				"<tr><td width='100%' class='leftcoltext' valign='bottom' align='center'>";

		             //"<input type='text' name='pageno' id='pageno' value='$this->currentpage' size='4' maxlength='4'>&nbsp;<a href=\"javascript:gotoPage('$varprefix');\"><font color='blue>'<u>Go to Page</u></font></a>&nbsp;&nbsp;&nbsp;";

		if ($rangecurrent==1)

			echo "<img border='0' src='".$URL_IMG."images/prev.gif'>";

		else

		{

			$turl="$url&$varprefix"."_cp=1";

			if ($javascript) $turl="javascript:navigatepage('$varprefix',1);";

				echo "<b><span class='goldentext'><font size=1>";

				echo "<A href=\"$turl\">1</b></span></A></font>&nbsp;";

			$turl="$url&$varprefix"."_cp=$rangeprevpg";

			if ($javascript) $turl="javascript:navigatepage('$varprefix',$rangeprevpg);";

				echo "<A href=\"$turl\">";

				echo "<img src='".$URL_IMG."images/prev.gif' border=0></A>";

		}

		echo "<span class='goldentext'><font size='1'>";

		for($i=$rangecurrentstart;$i<=$rangecurrentend;$i++)

		{

			if($this->currentpage==$i)

				echo "&nbsp;".$i;

			else

			{

				$turl="$url&$varprefix"."_cp=$i";

				if ($javascript) $turl="javascript:navigatepage('$varprefix',$i);";

					echo "&nbsp;<B><A href=\"$turl\">$i</B></A>";

			}

		}

		echo "</font></SPAN>";

		if ($rangecurrent==$rangetotal)

			echo "&nbsp;<img border='0' src='".$URL_IMG."images/next.gif'>";

		else

		{

			$turl="$url&$varprefix"."_cp=$rangenextpg";

			if ($javascript) $turl="javascript:navigatepage('$varprefix',$rangenextpg);";

				echo "&nbsp;<A href=\"$turl\">";

				echo "<img src='".$URL_IMG."images/next.gif' border=0></A>";

			$turl="$url&$varprefix"."_cp=$this->numofpages";

			if ($javascript) $turl="javascript:navigatepage('$varprefix',$this->numofpages);";

				echo "&nbsp;<b><span class='goldentext'><font size=1><A href=\"$turl\">$this->numofpages</b></font></span></A>";

		}

		echo "&nbsp;&nbsp<b>Showing records ".$this->recsfrom."-".$this->recsto." (".$this->numofrecs." Total)</b></td>";

		echo "</tr></table>";

	}



	function writeHTMLpagesrange2($url,$javascript=false,$URL_IMG="")

	{

		$rangecurrent=0;

		$rangetotal=0;

		$rangeprevpg=1;

		$rangenextpg=1;

		$rangecurrent=ceil($this->currentpage/$this->pageLinksRange);

		$rangetotal=ceil($this->numofpages/$this->pageLinksRange);

		if ($rangecurrent>1)

			$rangeprevpg=($rangecurrent-2)*$this->pageLinksRange+1;

		if ($rangetotal>$rangecurrent)

			$rangenextpg=($rangecurrent)*$this->pageLinksRange+1;

		$rangecurrentstart=($rangecurrent-1)*$this->pageLinksRange+1;

		$rangecurrentend=($rangecurrent-1)*$this->pageLinksRange+$this->pageLinksRange;

		if ($rangecurrentend>$this->numofpages)

		{

			$rangecurrentstart=$this->numofpages-$this->pageLinksRange+1;

			if($rangecurrentstart<1)

				$rangecurrentstart=1;

			$rangecurrentend=$this->numofpages;

		}

		$varprefix=$this->implementclassname."_".$this->pagelistname;







		echo "Pages: <span>";

		if ($rangecurrent==1)

			echo "";

		else

		{

			$turl="$url&currentpage=$rangeprevpg";

			if ($javascript)

				$turl="javascript:navigatepage('$varprefix',$rangeprevpg);";



			echo "<A href=\"$turl\">Previous</A>";

		}

		for($i=$rangecurrentstart; $i<=$rangecurrentend; $i++)

		{

			if($this->currentpage==$i)

			{

				echo "&nbsp;[".$i."]&nbsp;";



				if($i<$rangecurrentend)

					echo "|";

			}

			else

			{

				//echo "i = ".$i."###start = ".$rangecurrentstart."###end = ".$rangecurrentend."###";

				$turl="$url&currentpage=$i";

				if ($javascript) $turl="javascript:navigatepage('$varprefix',$i);"; {

					echo "&nbsp;<A href=\"$turl\">$i</A>&nbsp;";

				if($i<$rangecurrentend)

					echo "|";

				}

			}

		}



		if ($rangecurrent==$rangetotal)

			echo "&nbsp;";

		else

		{

			$turl="$url&currentpage=$rangenextpg";



			if ($javascript)

				$turl="javascript:navigatepage('$varprefix',$rangenextpg);";



			echo "&nbsp;|<a href=\"$turl\">Next</a>";



			$turl="$url&$varprefix"."_cp=$this->numofpages";

			if ($javascript)

				$turl="javascript:navigatepage('$varprefix',$this->numofpages);";





		}



		echo "</span>";

	}

	/**

	* Used for displaying total no of records .

	*

	* This function is called from

	*/

	function ShowNumberOfRecordsDisplayed()

	{

		echo "$this->numofrecs matches | $this->recsfrom - $this->recsto displayed";

	}

	/**

	* Used for writeing paging hidden variable.

	*

	* This function is called from wadiscussion.php file

	*/

	function writePagingVariable($cpvalue="",$sbvalue="",$stvalue="")

	{

		echo "<input type='hidden' id='$this->implementclassname"."_".$this->pagelistname."_currentpage' name='$this->implementclassname"."_".$this->pagelistname."_currentpage' value='$cpvalue'>";

		echo "<input type='hidden' id='$this->implementclassname"."_".$this->pagelistname."_sortby' name='$this->implementclassname"."_".$this->pagelistname."_sortby' value='$sbvalue'>";

		echo "<input type='hidden' id='$this->implementclassname"."_".$this->pagelistname."_sorttype'  name='$this->implementclassname"."_".$this->pagelistname."_sorttype' value='$stvalue'>";

	}

	/**

	* Used for setting number of records per peg

	*

	* This function is called from

	*/

	function setpagevariable($numrecords)

	{

		$this->numofrecsperpage=$numrecords;

	}

	/**

	* Generate HTML for page links

	*

	* This function is called from wadiscussion.php file

	*

	* @param string	URL of Page

	* @param boolean

	*/

	function writeHTMLpagesrange_SEO($url,$javascript=false,$URL_IMG="")

	{

		$rangecurrent=0;

		$rangetotal=0;

		$rangeprevpg=1;

		$rangenextpg=1;

		$linkjoinchar="";//added by binal for removing duplication of url

		$findme   = '?';

		$pos = strpos($url, $findme);

		if($pos === false)

			$linkjoinchar="?";

		else

			$linkjoinchar="&";

		$rangecurrent=ceil($this->currentpage/$this->pageLinksRange);

		$rangetotal=ceil($this->numofpages/$this->pageLinksRange);





		$rangecurrentstart=$this->currentpage;

		$rangecurrentend=$rangecurrentstart+$this->pageLinksRange-1;



		if ($rangecurrentend>$this->numofpages)

		{

			$rangecurrentstart=$this->numofpages-$this->pageLinksRange+1;

			if($rangecurrentstart<1)

				$rangecurrentstart=1;

			$rangecurrentend=$this->numofpages;

		}



		if ($rangecurrentstart>1)

			$rangeprevpg=$rangecurrentstart - 1;

		if ($rangetotal>$rangecurrent)

			$rangenextpg=$rangecurrentend+1;



		$varprefix=$this->implementclassname."_".$this->pagelistname;



		echo "<table cellpadding='0' cellspacing='0' width='100%' border='0'><tr>".

				"<tr><td width='100%' class='leftcoltext' valign='bottom' align=right>";

		             //"<input type='text' name='pageno' id='pageno' value='$this->currentpage' size='4' maxlength='4'>&nbsp;<a href=\"javascript:gotoPage('$varprefix');\"><font color='blue>'<u>Go to Page</u></font></a>&nbsp;&nbsp;&nbsp;";

		if ($rangecurrentstart==1)

			echo "&nbsp;";	//echo "First&nbsp;Previous";  // Commented By Mishant

		else

		{

			$turl="$url".$linkjoinchar."$varprefix"."_cp=1";



			if ($javascript) $turl="javascript:navigatepage(1);";

				echo "<b><span class='goldentext'><font size=1>";

				echo "<A href=\"$turl\">First</b></span></A></font>&nbsp;";

			$turl="$url".$linkjoinchar."$varprefix"."_cp=$rangeprevpg";



			if ($javascript) $turl="javascript:navigatepage($rangeprevpg);";

				echo "<A href=\"$turl\">";

				echo "Previous</A>";

		}

		echo "<span class='goldentext'><font size='1'>";

		for($i=$rangecurrentstart;$i<=$rangecurrentend;$i++)

		{

			if($this->currentpage==$i)

				echo "&nbsp;".$i;

			else

			{

				$turl="$url".$linkjoinchar."$varprefix"."_cp=$i";



				if ($javascript) $turl="javascript:navigatepage($i);";

					echo "&nbsp;<B><A href=\"$turl\">$i</B></A>";

			}

		}

		echo "</font></SPAN>";

		if ($rangecurrent==$rangetotal || ($i-1)==$this->numofpages)

			echo "&nbsp;"; 		//echo "&nbsp;Next&nbsp;Last"; // Commented By Mishant

		else

		{

			$turl="$url".$linkjoinchar."$varprefix"."_cp=$rangenextpg";



			if ($javascript) $turl="javascript:navigatepage($rangenextpg);";

				echo "&nbsp;<A href=\"$turl\">";

				echo "Next</A>";

			$turl="$url".$linkjoinchar."$varprefix"."_cp=$this->numofpages";



			if ($javascript) $turl="javascript:navigatepage($this->numofpages);";

				echo "&nbsp;<b><span class='goldentext'><font size=1><A href=\"$turl\">Last</b></font></span></A>";

		}

		echo "</td></tr></table>";

	}





	/**

	 * Added for sitemap paging

	 *

	 * @param string $url

	 * @param boolean $javascript

	 * @param string $URL_IMG

	 */



	function writeHTMLpagesrange_SiteMap($url,$javascript=false,$URL_IMG="")

	{

		$rangecurrent=0;

		$rangetotal=0;

		$rangeprevpg=1;

		$rangenextpg=1;

		$last_page = $this->numofpages;

		$url=constant("HTTP");

		$rangecurrent=ceil($this->currentpage/$this->pageLinksRange);

		$rangetotal=ceil($this->numofpages/$this->pageLinksRange);



		$rangecurrentstart=$this->currentpage;

		$rangecurrentend=$rangecurrentstart+$this->pageLinksRange-1;



		if ($rangecurrentend>$this->numofpages)

		{

			$rangecurrentstart=$this->numofpages-$this->pageLinksRange+1;

			if($rangecurrentstart<1)

				$rangecurrentstart=1;

			$rangecurrentend=$this->numofpages;

		}



		if ($rangecurrentstart>1)

			$rangeprevpg=$rangecurrentstart - 2;

		if ($rangetotal>$rangecurrent) {

			if($this->numofpages-$rangecurrentend>0) {

				$rangenextpg=$rangecurrentend;

			} else {

				$rangenextpg='';

			}

		}



		$varprefix=$this->implementclassname."_".$this->pagelistname;

		/*

		print "<br>Current Page: ".$this->currentpage."<br>";

		print "Num of pages: ".$this->numofpages."<br>";

		print "Range Current Start: ".$rangecurrentstart."<br>";

		print "Range Current End: ".$rangecurrentend."<br>";

		print "Var Prefix: ".$varprefix."<br>";

		print "Range Total: ".$rangetotal."<br>";

		print "Range Current: ".$rangecurrent."<br>";

		*/

		echo "<table cellpadding='0' cellspacing='0' width='98%' border='0' class='size1normal' align='center'>".

				"<tr><td width='100%'><img src='<?echo constant(\"HTTP_IMAGE\");?>blanker.gif' width='1' height='10' border='0'></td></tr><tr><td width='100%' align='right'>";



		if ($rangecurrentstart==1)

			echo "&nbsp;";	//echo "First&nbsp;Previous";  // Commented By Mishant

		else

		{

			$turl="$url"."sitemap.php?&$varprefix"."_cp=1";

			if ($javascript) $turl="javascript:navigatepage(1);";

				echo "<a href=\"$turl\"><span class='size1normal'><strong>First</strong></span></a>&nbsp;";



			if($rangecurrentstart==2)

				$turl="$url"."sitemap.php?&$varprefix"."_cp=$rangeprevpg";

			else

				$turl="$url"."sitemap".$rangeprevpg.".php?&$varprefix"."_cp=$rangeprevpg";

			if ($javascript) $turl="javascript:navigatepage($rangeprevpg);";

				echo "<a href=\"$turl\">";

				echo "<strong>Previous</strong></a>";

		}



		$rangecurrentstart = $rangecurrentstart-1;

		$this->currentpage = $this->currentpage-1;

		for($i=$rangecurrentstart;$i<=$rangecurrentend;$i++)

		{

			if($i==$rangecurrentend) {

				break;

			} else {

				$j=$i+1;

			}

			if($this->currentpage==$i)

				echo "&nbsp;".$j;

			else

			{

				$turl="$url"."sitemap$i.php?&$varprefix"."_cp=$i";

				if ($javascript) $turl="javascript:navigatepage($i);";

					echo "&nbsp;<a href=\"$turl\"><span class='size1normal'><strong>$j</strong></span></a>";

			}



		}

		if ($rangecurrent==$rangetotal || ($i-1)==$this->numofpages)

			echo "&nbsp;"; 		//echo "&nbsp;Next&nbsp;Last"; // Commented By Mishant

		else

		{

			$last_page = $this->numofpages - 1;

			$turl="$url"."sitemap".$rangenextpg.".php?&$varprefix"."_cp=$rangenextpg";



			if ($javascript) $turl="javascript:navigatepage($rangenextpg);";



			if($rangenextpg!='') {

				echo "&nbsp;<a href=\"$turl\">";

				echo "<strong>Next</strong></a>";

			}

			$turl="$url"."sitemap".$last_page.".php?&$varprefix"."_cp=$last_page";

			if ($javascript) $turl="javascript:navigatepage($this->numofpages);";

				echo "&nbsp;<a href=\"$turl\"><span class='size1normal'><strong>Last</strong></span></a>";

		}

		echo "</td></tr></table>";

	}



}

?>