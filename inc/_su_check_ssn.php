<?
	include("../inc/_db_open.php");
	//include("../inc/_http_referer.php");
	//include("../inc/_function.php");

	$checkMcode  = $_GET["mCode"];
	$checkMkind  = $_GET["mKind"];
	$checkYjumin = $_GET["yJumin"];

	if ($checkMcode == "" or $checkMkind = "" or $checkYjumin = ""){
		$requestString = "N";
	}else{
		$sql = "select count(*)"
			 . "  from m03sugupja"
			 . " where m03_ccode = '".$_GET["mCode"]
			 . "'  and m03_mkind = '".$_GET["mKind"]
			 . "'  and m03_jumin = '".$_GET["yJumin"]
			 . "'";
		$conn->query($sql);
		$row = $conn->fetch();
		$row_count = $conn->row_count();
		$requestString = $row[0];

		if ($requestString == "0"){
			$requestString = "N";
		}
	}
	include("../inc/_db_close.php");

	echo $requestString;
?>