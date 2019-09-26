<?

	include_once('../inc/_db_open.php');
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');
	

	$fileName = "branch_.".date('Ymd');
		
	header( "Content-type: application/vnd.ms-excel; charset=euc-kr" ); 
	header( "Content-Disposition: attachment; filename=$fileName.xls" ); 
	header( "Content-Transfer-Encoding: binary" ); 
	header( "Content-Description: PHP4 Generated Data" ); 


	$mCode = $_POST['mCode'];
	$cName = $_POST['cName'];
	$branch = $_POST['branch'];
	$person = $_POST['person'];

	$print_dt = date('Y.m.d', mktime()); 	
?>
<script type="text/javascript" src="../js/branch.js"></script>
<style>
	.head{
		background-color:#efefef;
		font-family:굴림;
	}
	.head_l{
		text-align:left;
		background-color:#efefef;
		font-family:굴림;
	}
	.head_r{
		text-align:right;
		background-color:#efefef;
		font-family:굴림;
	}
	.head_c{
		text-align:center;
		background-color:#efefef;
		font-family:굴림;
	}
	.td_l{
		padding-left:5px;
		text-align:left;
		font-family:굴림;
	}
	.td_r{
		text-align:right;
		font-family:굴림;
	}
	.td_c{
		text-align:center;
		font-family:굴림;
	}
</style>
<form name="f" method="post">
	<table border=1>
	<?
		$sql = "select b02_branch as branchCode
				,      b00_name as branchName
				,      b01_name as personName
				,      b02_center as centerCode
				,      m00_cname as centerName
				,      m00_mkind as centerKind
				  from b02center
				 inner join b00branch
					on b00_code = b02_branch
				 inner join b01person
					on b01_branch = b02_branch
				   and b01_code   = b02_person
				 inner join m00center
					on m00_mcode = b02_center
				   and m00_mkind = b02_kind
				 where b02_branch != ''";


			if ($mCode != ''){
			$sql .= " and b02_center like '%$mCode%'";
			}
			if ($cName != ''){
			$sql .= " and m00_cname like '%$cName%'";
			}
			if ($branch != ''){
			$sql .= " and b02_branch = '$branch'";
			}
			if ($person != ''){
				$sql .= " and b02_person = '$person'";
			}
			$sql .= " order by m00_cname, m00_mname";

	    $center = $conn->get_array($sql);
		
	?>
		<thead>
			<tr>
				<th class="head_l"style="border-right:0;"  colspan='10'>
				<span style="font-size:15pt; font-weight:bold;">지사/기관 연결</span>
				<?
					echo "<span style='font-size:9pt;'>".($branch != '' ? '(지사-'.$center['branchName'].')' : '')." ".($person != '' ? '(담당자-'.$center['personName'].')' : '')."</span>";
				?>
				</th>
				<th class="head_r" style="border-left:0;" colspan="4">
					<span>출력일자 : <?=$print_dt;?></span>
				</th>
			</tr>
			<tr>
				<th class="head">No</th>
				<th class="head">기관명</th>
				<th class="head">기관코드</th>
				<th class="head">대표자명</th>
				<th class="head">연락처</th>
				<th class="head">지역</th>
				<th class="head" style="width:450px;">주소</th>
				<th class="head">지사명</th>
				<th class="head">담당자</th>
				<th class="head">연결일자</th>
				<th class="head">계약일자</th>
				<th class="head">직원</th>
				<th class="head">수급자</th>
				<th class="head">일정</th>
			</tr>
		</thead>
		<tbody>


	<?
			
		$date = date('Ym', mktime());

		$sql = "select b02_branch as branchCode
				,      b00_name as branchName
				,      b01_name as personName
				,      b02_center as centerCode
				,      m00_cname as centerName
				,      m00_mkind as centerKind
				,      case m00_mkind when '0' then '재가요양기관'
									  when '1' then '가사간병'
									  when '2' then '노인돌봄'
									  when '3' then '산모신생아'
									  when '4' then '장애인 활동보조' else '-' end as centerType
				,	   m00_mname as manager
				,	   m00_ctel as ctel
				,	   m00_caddr1 as caddr
				,      m00_cont_date as cont_date
				,      b02_date as date
				,      b02_other as other
				,     (select count(*)
						 from m02yoyangsa
						where m02_ccode        = b02_center
						  and m02_mkind        = b02_kind
						  and m02_ygoyong_stat = '1') as y_count
				,     (select count(*)
						 from m03sugupja
						where m03_ccode        = b02_center
						  and m03_mkind        = b02_kind
						  and m03_sugup_status = '1') as s_count
				,     (select count(distinct t01_jumin)
						 from t01iljung
						where t01_ccode  = b02_center
						  and t01_mkind  = b02_kind
						  and t01_del_yn = 'N'
						  and t01_sugup_date like '$date%') as i_count
				  from b02center
				 inner join b00branch
					on b00_code = b02_branch
				 inner join b01person
					on b01_branch = b02_branch
				   and b01_code   = b02_person
				 inner join m00center
					on m00_mcode = b02_center
				   and m00_mkind = b02_kind
				 where b02_branch != ''";


			if ($mCode != ''){
			$sql .= " and b02_center like '%$mCode%'";
			}
			if ($cName != ''){
			$sql .= " and m00_cname like '%$cName%'";
			}
			if ($branch != ''){
			$sql .= " and b02_branch = '$branch'";
			}
			if ($person != ''){
				$sql .= " and b02_person = '$person'";
			}
			$sql .= " order by m00_cname, m00_mname";

		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();
		
		if ($rowCount > 0){
			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				?>
					<tr>
						<td class="td_c"><?=($i+1);?></td>
						<td class="td_l"><?=$row['centerName'];?></td>
						<td class="td_l"><?=$row['centerCode'];?></td>
						<td class="td_l"><?=$row['manager'];?></td>
						<td class="td_l"><?=$myF->phoneStyle($row['ctel']);?></td>
						<td class="td_l"><?=substr($row['caddr'],0,6);?> </td>
						<td class="td_l"><?=$row['caddr'];?></td>
						<td class="td_l"><?=$row['branchName'];?></td>
						<td class="td_l"><?=$row['personName'];?></td>
						<td class="td_c"><?=$myF->dateStyle($row['date'], '.');?></td>
						<td class="td_c"><?=$myF->dateStyle($row['cont_date'], '.');?></td>
						<td class="td_c"><?=$row['y_count'];?></td>
						<td class="td_c"><?=$row['s_count'];?></td>
						<td class="td_c"><?=$row['i_count'];?></td>
					</tr>
				<?
			}
			$conn->row_free();
		}else{
		?>
			<tr>
				<td class="td_c" colspan="9">::검색된 데이타가 없습니다.::</td>
			</tr>
		<?
		}

	?>
	</tbody>
	</table>
</form>
<?
	include_once("../inc/_db_close.php");
?>
<script language="javascript">

// 선택한 지사의 담당자리스트
function _getPerson(p_branch){
	var target  = document.f.person;
	var URL = '../inc/_find_person_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				branch:p_branch
			},
			onSuccess:function (responseHttpObj) {
				var request = responseHttpObj.responseText;

				target.innerHTML = '';

				var list = request.split(';;');

				__setSelectBox(target, '', '-담당자-');

				for(var i=0; i<list.length - 1; i++){
					var value = list[i].split('//');

					__setSelectBox(target, value[0], value[1]);
				}
			}
		}
	);
}

function __setSelectBox(object, value, text){
	var option = null;

	option = document.createElement("option");
	option.value = value;
	option.text  = text;
	object.add(option);
}

function _b2c_center_list(page){
	var f = document.f;

	f.page.value = page;
	f.action = 'branch2center.php';
	f.submit();
}

</script>