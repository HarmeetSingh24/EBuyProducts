<!--Name :Harmeet Singh
   Id	:10052567 -->
<html>
<head><title>Buy Products</title></head>
<body>
<p>Shopping Cart</p>
<?php
session_start();//session has been started from here .
//to maintain an array of elements in session 
if(!isset($_SESSION["buy"])){
$_SESSION["buy"]=array();
}
$tc=0.0;	
?>

<p></p>
<?php	
//$var1 = null;
//$var2 = null;
error_reporting(E_ALL);//to get the errors on runtime

ini_set('display_errors','On');//to display the errors

$xmlstr = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/CategoryTree?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId=72&showAllDescendants=true');//this method will fetch the various elements of the category 72,i.e ,'computer'
$xml = new SimpleXMLElement($xmlstr);//SimpleXML is an extension that allows us to easily manipulate and get XML data
//header('Content-Type: text/xml');
//$result = count($xml->category->categories->category);
$result = $xml->category;
//To empty the shopping cart when hit Empty Basket
if(isset($_GET["refresh"])==1)
	{
	$_GET["refresh"]=0;
	session_unset();//this function will unset the session,i.e,clearing off the basket
	}
//to delete the selected item from the shopping cart
if(isset($_GET['delete']))
{

//$_SESSION["buy"][$_GET["delete"]]=null;
unset($_SESSION["buy"][$_GET["delete"]]);
}
//to get the details of products selected and storing their data in session array
if(isset($_GET['buy1']))
{
	error_reporting(E_ALL);
	//$tr=$_GET["category"];
	ini_set('display_errors','On');
	//$xmlstr2 = file_get_contents('http://sandbox.api.shopping.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId='.$_GET['category'].'&keyword='.$_GET["search"].'&numItems=20');
	$bought=$_GET['buy1'];//stores various values of buy1 selected
	$xmlstr2= file_get_contents('http://sandbox.api.shopping.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&productId='.$_GET['buy1']);//To get details of the specific product using this method
	
	//$_SESSION["buy"]=$xmlstr1;
	$xml2 = new SimpleXMLElement($xmlstr2);

	$r1=$xml2->categories->category->items->product;
	if($r1!= null)
	{
	$_SESSION["buy"][$bought]=array(
				"min"=>(string)$r1->minPrice,"id"=>$bought,
				"name"=>(string)$r1->name,
				"image"=>(string)$r1->images->image[0]->sourceURL,
				"url"=>(string)$r1->productOffersURL);
}
else
{
	echo "unrecognized id=".$bought;
}
}
//To display the shopping cart 
if(!empty($_SESSION["buy"]))
{
/*$count1=count($_SESSION['buy'][$_GET['buy1']]);
for($i=0;$i<count1;$i++)
{echo $_SESSION['buy'][$i]['url'];}*/
echo'<table border="1"><tbody>';
foreach( $_SESSION as $k=>$k1)//to read the data of array session
{
	foreach($k1 as $k2)//to go further in array to fetch the required items
	{
	//print_r($_SESSION);
	//print_r($k1);
	echo'<tr><td><a href="'.$k2['url'].'"><img src="'.$k2['image'].'"></a></td>
	<td>'.$k2['name'].'</td>
	<td>$'.$k2['min'].'</td>
	<td><a href="buy.php?delete='.$k2['id'].'">delete</td></tr>
	';
	$tc=$tc+$k2['min'];
	

}
echo'</tbody></table>';
}
echo '<p>totalcost=$'.$tc.'</p>';//to display the total cost of all items in the shopping cart
}
else{
	echo'<p>totalcost= $ 0.0</p>';
}
	//Empty Basket Label
	echo'<form><input type="hidden" name="refresh" value="1">
	<input type="submit" value="Empty Basket"></form>';
// to display all the categories 
foreach($result as $t)
	{
	echo '<p><form action="buy.php" method="GET">
	<fieldset><legend>Find products:</legend>
	<label>Category:<select name="category">';
	echo'<option value="'.$result['id'].'">'.$result->name.'</option>';
	echo'<optgroup label="'.$result->name.'">';
	$har=count($t->categories->category);
	for($i=0;$i<$har;$i++)
	{
		$r=$t->categories->category[$i];
		echo'<option value="'.$r['id'].'">'.$r->name.'</option>';
			echo'<optgroup label="'.$r->name.'">';
			foreach($r->categories->category as $p)
		{
			echo'<option value="'.$p['id'].'">'.$p->name.'</option></optgroup>';
		}
	echo'</optgroup>';
	}
	
	
	echo'</select></label><label>Search by Keyword:<input type="text" name="search" /><label>
	<input type="submit" value="Search"/></label></label>
	</fieldset></form></p>';
	
	}
	//to diplay the results whenever the search button is hit according to the keyword and search item
	if (isset($_REQUEST["search"])) {
	error_reporting(E_ALL);
	$tr=$_GET["category"];
	ini_set('display_errors','On');
	$xmlstr1 = file_get_contents('http://sandbox.api.shopping.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId='.$tr.'&keyword='.$_GET["search"].'&numItems=20');
	//$_SESSION["buy"]=$xmlstr1;
	$xml1 = new SimpleXMLElement($xmlstr1);
	//print_r($xml1);
		echo ' <table border="1"><tbody>';
		$ty=count($xml1->categories->category->items->product);
		
	for($i=0;$i<$ty;$i++)
		{ 
	$r=$xml1->categories->category->items->product[$i];
				echo'<tr>
				<td><a href="buy.php?buy1='.$r['id'].'"><img src="'.$r->images->image[0]->sourceURL.'"></a></td>
				<td>'.$r->name.'</td>
				<td>$'.$r->minPrice.'</td>
				<td>'.$r->fullDescription.'</td>
				</tr>';
						
		}
		echo '</tbody></table>';	
		
		/*$bought = isset($_GET['buy1']);
		$r1=$xml1->categories->category->items->product;
		$_SESSION["buy"][$bought]=array(
				"min"=>(string)$r1->minPrice,"id"=>$bought,
				"name"=>(string)$r1->name,
				"image"=>(string)$r1->images->image[0]->sourceURL,
				"url"=>(string)$r1->productOffersURL);*/
	}
	
?>
</body>
</html>