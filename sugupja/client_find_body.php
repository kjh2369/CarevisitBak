<?
	//include_once('../inc/_header.php');
	include_once("../inc/_db_open.php");
	include_once("../inc/_function.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code = $_SESSION['userCenterCode'];
	$name = $_POST['name'];
	
	if(!empty($name)){
		$sql = 'select m03_jumin as jumin
				,      m03_name as name
				,      m03_tel as tel
				,      m03_hp  as hp
				,	   m03_juso1 as juso1
				,	   m03_juso2 as juso2
				,	   m03_post_no as postNo
				,      svc.from_dt as fromDt
				,	   svc.to_dt  as toDt 
				,      datediff(date_format(lvl.to_dt,\'%Y-%m-%d\'), date_format(now(),\'%Y-%m-%d\')) as remainDt
				,      case lvl.svc_cd when \'0\' then case lvl.level when \'9\' then \'일반\' else concat(lvl.level,\'등급\') end
									   when \'4\' then concat(dis.svc_lvl,\'등급\') else \'\' end as lvl_nm
				,      case kind.kind when \'3\' then \'기초\'
									  when \'2\' then \'의료\'
									  when \'4\' then \'경감\' else \'일반\' end as kind_nm
				  from m03sugupja as mst
				  left join (
					   select org_no
					   ,	  min(svc_cd) as svc_cd
					   ,      jumin
					   ,      from_dt
					   ,      to_dt
					   ,      svc_stat
					   ,      svc_reason
						 from client_his_svc
						where org_no = \''.$code.'\'
						  and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
						  and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')
						GROUP BY jumin, svc_cd
					   ) as svc
					on mst.m03_jumin     = svc.jumin
				   and mst.m03_mkind     = svc.svc_cd
				  left join (
					   select jumin
					   ,      svc_cd
					   ,      level
					   ,      from_dt
					   ,      MAX(to_dt) AS to_dt
						 from client_his_lvl
						where org_no = \''.$code.'\'
						  and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
						  and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')
						GROUP BY jumin, svc_cd
					   ) as lvl
					on mst.m03_jumin    = lvl.jumin
				   and mst.m03_mkind	= lvl.svc_cd
				  left join (
					   select jumin
					   ,      kind
					   ,      from_dt
					   ,      to_dt
						 from client_his_kind
						where org_no = \''.$code.'\'
						  and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
						  and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')
					   ) as kind
					on mst.m03_jumin = kind.jumin
				  left join (
					   select jumin
					   ,      svc_lvl
					   ,      from_dt
					   ,      to_dt
						 from client_his_dis
						where org_no = \''.$code.'\'
						  and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
						  and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')
					   ) as dis
					on mst.m03_jumin = dis.jumin
				 where m03_ccode = \''.$code.'\'
				   and m03_name = \''.$name.'\'';
		
		$clt = $conn -> get_array($sql);
	}

	#계약기간
	$Dt = $myF->dateStyle($clt['fromDt'],'.').' ~ '.$myF->dateStyle($clt['toDt'],'.');
	
	
	#주소
	$Addr = ($mem['postNo'] != '' ? '('.getPostNoStyle($mem['postNo']).') '.$clt['juso1'] : $clt['juso1']);
	$Addr_dtl = $clt['juso2'];


	#나이
	$Age = ($clt['jumin'] != '' ? $myF->issToAge($clt['jumin']).'세' : ''); 


	$lsResult = '"code='.$code.
				'&jumin='.$ed->en($clt['jumin']).'"';
?> 

<table class="my_table" style="width:100%; height:100%">
	<colgroup>
		<col width="60px">
		<col width="60px">
		<col width="80px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center bold" colspan="5">고객정보</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th class="center">성명</th>
			<td class="left last"><?=$clt['name']?></td>
			<td class="left" colspan="3"><?
				if(!empty($clt['name'])){  ?>
					<span class="btn_pack m"><button type="button" onclick='setItem(<?=$lsResult;?>);'>수정</button></span><?
				} ?>
			</td>
		</tr>
		<tr>
			<th class="center" rowspan="2">연락처</th>
			<th class="center">유선</th>
			<td class="left last" colspan="3"><?=$myF->phoneStyle($clt['tel']);?></td>
		</tr>
		<tr>
			<th class="center">무선</th>
			<td class="left last" colspan="3"><?=$myF->phoneStyle($clt['hp']);?></td>
		</tr>
		<tr>
			<th class="center" rowspan="2">주소</th>
			<td class="left last" colspan="4"><?=$Addr?></td>
		</tr>
		<tr>
			<td class="left last" colspan="4" ><?=$Addr_dtl?></td>
		</tr>
		<tr>
			<th class="center">기간</th>
			<td class="center" colspan="2"><?=$Dt;?></td>
			<th class="center">남은기간</th>
			<td class="right last"><?=$clt['remainDt'];?>일</td>
		</tr>
		<tr>
			<th class="center">등급</th>
			<td class="left" ><?=$clt['lvl_nm'];?></td>
			<th class="center">구분</th>
			<td class="left last" colspan="2"><?=$clt['kind_nm'];?></td>
		</tr>
		<tr>
			<th class="center">생년월일</th>
			<td class="left" ><?=$myF->issToBirthday($clt['jumin'],'.');?></td>
			<th class="center">나이</th>
			<td class="left last" colspan="2"><?=$Age;?></td>
		</tr>
	</tbody>
</table>

<?
	include_once("../inc/_db_close.php");
?>