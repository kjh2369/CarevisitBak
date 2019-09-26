<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_login.php');
	include_once("../inc/_myFun.php");
	//include_once("../inc/_ed.php");
	//include_once("../inc/_function.php");
	//include_once("../inc/_definition.php");
	

	header( "Content-type: charset=utf-8" );
	header("Content-type: application/vnd.ms-excel;"); 
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Description: test" );
	header( "Content-Transfer-Encoding: binary" );
	header( "Content-Description: PHP4 Generated Data" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );
?>	
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?
	$findCd = $_POST['txtFindCode'];
	$findNm = $_POST['txtFindName'];
	$findManager = $_POST['txtManager'];
	$findCMS = $_POST['txtCMSCode'];
	$findBranch = $_POST['cboFindBranch'];
	$findContFrom = $_POST['txtContFrom'];
	$findContTo = $_POST['txtContTo'];
	$findContYn = $_POST['contYn'];
	$findCMSYn = $_POST['cmsYn'];
	
?>
<style>
	.title{
		font-size:20pt;
		text-align:left;
		font-weight:bold;
		text-align:center;
	}
	
	.head{
		background-color:#efefef;
		border:0.5pt solid #000000;
		font-family:굴림;
	}

	.center{
		text-align:center;
		font-family:굴림;
	}
	.left{
		text-align:left;
		font-family:굴림;
	}
	.right{
		text-align:right;
		background-color:#ffffff;
		font-family:굴림;
		font-size:12pt;
	}
</style>
<?
$r_dt = date('Y.m.d',mktime());
?>
<div class="title">관리기관리스트</div>
<div class="right">일자 : <?=$r_dt?></div>
<table border="1">
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관코드</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th>
			<th class="head">적용기간(시작)</th>
			<th class="head">적용기간(종료)</th>
			<th class="head">계약일자</th>
			<th class="head">CMS코드</th>
			<th class="head">과금</th>
			<th class="head">지사</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody><?
		//기관 아이디/비번
		$sql = 'SELECT m97_user AS cd
				,      m97_id AS id
				,      m97_pass AS pw
				  FROM m97user';
		$centerId = $conn->_fetch_array($sql, 'cd');

		if (!Empty($findCMS)){
			if (StrLen($findCMS) < 8){
				$liCnt = 8 - StrLen($findCMS);

				$findCMS = '';

				for($i=1; $i<=$liCnt; $i++){
					$findCMS .= '0';
				}
				$findCMS .= IntVal($_POST['cms']);
			}
		}

		
		$sql = 'SELECT DISTINCT
					   mst.m00_mcode AS code
				,      mst.m00_store_nm AS name
				,      IFNULL(mst.m00_cont_date,\'\') AS cont_dt
				,      IFNULL(mst.m00_mname,\'\') AS rep_nm
				,      IFNULL(center.from_dt,\'\') AS from_dt
				,      IFNULL(center.to_dt,\'\') AS to_dt
				,      center.b02_homecare AS homecare
				,      center.b02_voucher AS voucher
				,      center.b02_caresvc AS caresvc
				,      center.b02_date AS start_dt
				,		center.care_area
				,		IFNULL(center.care_group,\'99\') AS care_group
				,		center.care_support
				,		center.care_resource
				,      IFNULL(center.cms_cd,\'\') AS cms_cd
				,      center.hold_yn
				,      center.basic_cost
				,      center.client_cost
				,      center.client_cnt
				,      branch.b00_code AS branch_cd
				,      branch.b00_name AS branch_nm
				,      manager.b01_code AS manager_cd
				,      manager.b01_name AS manager_nm
				,      center.b02_other AS other
				  FROM m00center AS mst
				 INNER JOIN b02center AS center
					ON center.b02_center = mst.m00_mcode
				 INNER JOIN b00branch AS branch
					ON branch.b00_code = center.b02_branch
				 INNER JOIN b01person AS manager
					ON manager.b01_branch = center.b02_branch
				   AND manager.b01_code   = center.b02_person
				 WHERE m00_domain = \''.$gDomain.'\'
				   AND m00_del_yn = \'N\'';

		if (!Empty($findCd)){
			$sql .= ' AND m00_mcode LIKE \''.$findCd.'%\'';
		}

		if (!Empty($findNm)){
			$sql .= ' AND m00_store_nm LIKE \'%'.$findNm.'%\'';
		}

		if (!Empty($findManager)){
			$sql .= ' AND m00_mname LIKE \'%'.$findManager.'%\'';
		}

		if (!Empty($findBranch)){
			$sql .= ' AND b02_branch = \''.$findBranch.'\'';
		}

		if (!Empty($findCMS)){
			$sql .= ' AND cms_cd >= \''.$findCMS.'\'';
		}

		if (!Empty($findContFrom) && !Empty($findContTo)){
			$sql .= ' AND m00_cont_date >= \''.$findContFrom.'\'
					  AND m00_cont_date <= \''.$findContTo.'\'';
		}

		if ($findContYn == 'N'){
			$sql .= ' AND IFNULL(mst.m00_cont_date,\'\') = \'\'';
		}

		if ($findCMSYn == 'N'){
			$sql .= ' AND IFNULL(center.cms_cd,\'\') = \'\'';
		}

		if (!Empty($findCMS)){
			$sql .= ' ORDER BY cms_cd';
		}else{
			$sql .= ' ORDER BY name';
		}
	
		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			
			echo '<tr>
					<td class="center">'.($i + 1).'</td>
					<td class="left">'.$row['code'].'</td>
					<td class="left">'.$row['name'].'</td>
					<td class="left">'.$row['rep_nm'].'</td>
					<td class="center">'.$myF->datestyle($row['from_dt'],'.').'</td>
					<td class="center">'.$myF->datestyle($row['to_dt'],'.').'</td>
					<td class="center">'.$myF->datestyle($row['cont_dt'],'.').'</td>
					<td class="center" style="mso-number-format:\'\@\';">'.$row['cms_cd'].'</td>
					<td >'.number_format($row['basic_cost']).'</td>
					<td class="left">'.$row['branch_nm'].'/'.$row['manager_nm'].'</td>
					<td class="left">'.$row['other'].'</td>
				 </tr>';
			
		}

		$conn->row_free();

		?>
	</tbody>
</table>