<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	$code     = $_POST['code'];
	$year     = $_POST['year'];
	$month    = $_POST['month'];
	$para = explode('/', $_POST['para']);
	
	
	$conn -> begin();

	if (is_array($para)){

		foreach($para as $var){

			parse_str($var, $val);
			
			$sql = "replace into iljung_plan ( org_no
											  ,yymm
											  ,jumin
											  ,plan ) values
											  ( '$code'
											  ,	'$year$month'
											  , '$val[jumin]'
											  , '$val[visitDt]'); ";
			
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}
			
		}
		
	}
	
	$conn -> commit();
	
	echo "정상 처리 되었습니다';

?>