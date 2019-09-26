<?
	//include_once('../inc/_header.php');
	include_once("../inc/_db_open.php");
	include_once("../inc/_function.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code = $_SESSION['userCenterCode'];
	$tel  = $_POST['tel'];
	
	/*
	if(!empty($tel)){
		$sql = 'select m03_sname
				,      m03_tel
				,	   m03_hp
				,	   m03_yboho_name
				,	   m03_yboho_phone
				  from m03sugupja
				 where rignt(m03_tel,4) = \''.$tel.'\'
				    or rignt(m03_hp,4) = \''.$tel.'\'
					or rignt(m03_yboho_phone,4) = \''.$tel.'\'';
		$conn -> query($sql);
		$conn -> fetch();
		$rowCount = $conn -> row_count();
	}
	*/


?> 
<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100%;">
	<table class="my_table my_border" style="width:100%;">
		<colgroup>
			<col width="30%">
			<col width="30%">
			<col width="30%">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center">직원명</th>
				<th class="center">유선</th>	
				<th class="center">무선</th>
				<th class="center last">비고</th>
			</tr>
		</tbody>
	</table>
</div><?
if(!empty($tel)){
	$sql = 'select m02_yname as name
			,      m02_ytel as tel
			,	   m02_ytel2 as hp
			  from m02yoyangsa
			 where m02_ccode         = \''.$code.'\'
			   and right(m02_ytel,4) = \''.$tel.'\'
				or right(m02_ytel2,4) = \''.$tel.'\'
			 group by m02_yjumin';
	
	$conn -> query($sql);
	$conn -> fetch();
	$rowCount = $conn -> row_count();
}

if($rowCount > 0){ ?>
	<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:80px;">
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="30%">
				<col width="30%">
				<col width="30%">
				<col>
			</colgroup>
			<tbody><?
				
				for($i=0; $i<$rowCount; $i++){ 
					$row = $conn -> select_row($i); ?>
					<tr>
						<td class="left"><?=$row['name']?></td>
						<td class="left"><?=$row['tel']?></td>
						<td class="left"><?=$row['hp']?></td>
						<td></td>
					</tr>
					<?
				}
			
				$conn -> row_free();

				?>
			</tbody>
		</table>
	</div><?
} else { ?>
	<div style="margin-top:30px; text-align:center; height:50px;">:: 검색된 데이터가 없습니다 ::</div><?
} ?>

<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100%;">
	<table class="my_table my_border" style="width:100%;">
		<colgroup>
			<col width="30%">
			<col width="30%">
			<col width="30%">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center">고객명</th>
				<th class="center">유선</th>	
				<th class="center">무선</th>
				<th class="center last">비고</th>
			</tr>
		</tbody>
	</table>
</div><?
if(!empty($tel)){
	$sql = 'select m03_name as name
			,      m03_tel as tel
			,	   m03_hp as hp
			,	   m03_yboho_name
			,	   m03_yboho_phone
			  from m03sugupja
			 where right(m03_tel,4) = \''.$tel.'\'
				or right(m03_hp,4) = \''.$tel.'\'
				or right(m03_yboho_phone,4) = \''.$tel.'\'
			 group by m03_jumin';
	$conn -> query($sql);
	$conn -> fetch();
	$rowCount = $conn -> row_count();
}
if($rowCount > 0){ ?>
	<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:80px;">
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="30%">
				<col width="30%">
				<col width="30%">
				<col>
			</colgroup>
			<tbody><?						
				for($i=0; $i<$rowCount; $i++){ 
					$row = $conn -> select_row($i); ?>
					<tr>
						<td class="left"><?=$row['name']?></td>
						<td class="left"><?=$row['tel']?></td>
						<td class="left"><?=$row['hp']?></td>
						<td></td>
					</tr>
					<?
				}
			
				$conn -> row_free();
			 ?>
			</tbody>
		</table>
	</div><?
}else { ?>
	<div style="margin-top:30px; text-align:center; height:50px;">:: 검색된 데이터가 없습니다 ::</div><?
} 

include_once("../inc/_db_close.php");

?>