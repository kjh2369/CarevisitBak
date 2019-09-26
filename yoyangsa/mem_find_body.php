<?
	//include_once('../inc/_header.php');
	include_once("../inc/_db_open.php");
	include_once("../inc/_function.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code = $_SESSION['userCenterCode'];
	$name = $_POST['name'];
	$tel  = $_POST['tel'];
?>

<table class="my_table" style='width:100%;'>
	<colgroup>
		<col width="70px">
		<col width="65px">
		<col width="65px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
<?
	$sql = 'select m02_yjumin as jumin
			,      m02_yname as name
			,      m02_ytel as tel
			,	   m02_ytel2 as hp
			,	   m02_yipsail as fromDt
			,	   m02_ytoisail	as toDt
			,	   m02_ypostno as postNo
			,	   m02_yjuso1 as juso1
			,	   m02_yjuso2 as juso2
			  from m02yoyangsa
			 where m02_ccode = \''.$code.'\'';
	
	if(!empty($name)) $sql .= '  and m02_yname >= \''.$name.'\'';
	
	if(!empty($tel)) $sql .= '  and right(m02_ytel,4) = \''.$tel.'\'
								  or right(m02_ytel2,4) = \''.$tel.'\'';
	$sql .= ' group by m02_yjumin';

	$conn -> query($sql);
	$conn -> fetch();
	$rowCount = $conn -> row_count();
						
	for($i=0; $i<$rowCount; $i++){ 
		$row = $conn -> select_row($i); 
		
		#입사일
		$Dt = $myF->dateStyle($row['fromDt'],'.');
		
		#주소
		$Addr = ($row['postNo'] != '' ? $row['juso1'].' '.$row['juso2'] : $row['juso1'].' '.$row['juso2']);


		#나이
		//$Age = ($mem['jumin'] != '' ? $myF->issToAge($mem['jumin']).'세' : ''); 


		$lsResult = '"code='.$code.
					'&jumin='.$ed->en($row['jumin']).'"';

		$tel = $row['tel'] != '' ? $myF->phoneStyle($row['tel']) : $row['hp'];

		?>
		<tr>
			<td class="center"><a href="#" onclick='setItem(<?=$lsResult;?>);'><?=$row['name']?></a></td>
			<td class="center"><?=$myF->issToBirthday($row['jumin'],'.');?></td>
			<td class="center"><?=$Dt;?></td>
			<td class="center"><?=$tel?></td>
			<td class="left last"><div class="left nowrap" style="width:170px;"><?=$Addr?></div></td>
		</tr>
		<?
	}

	$conn -> row_free();
	
	?>
	</tbody>
</table>

<?
	include_once("../inc/_db_close.php");
?>