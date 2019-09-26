<?
	include_once('../inc/_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $_POST['jumin'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);
	
	$sql = 'SELECT code
			FROM   mst_jumin
			WHERE  jumin = \''.$jumin.'\'
			LIMIT  1';
	$ssn = $conn -> get_data($sql);

	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_jumin = \''.$ssn.'\'
			LIMIT	1';

	$name = $conn->get_data($sql);
?>
<script type="text/javascript">
	$(document).ready(function(){
		var top = $('#divBody').offset().top;
		var height = $(this).height();
		var h = height - top - 3;

		$('#divBody').height(h);
	});
</script>
<div class="title title_border">대상자 서비스이력</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>대상자명</th>
			<td class="left last"><?=$name;?></td>
		</tr>
	</tbody>
</table>
<div id="divBody" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;">
	<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="150px">
		<col>
	</colgroup>
	<tbody><?
		$sql = 'SELECT	*
				FROM	(
						SELECT	org_no
						,		svc_cd
						,		from_dt
						,		to_dt
						FROM	client_his_svc
						WHERE	jumin = \''.$ssn.'\'
						UNION	ALL
						SELECT	org_no
						,		svc_cd
						,		from_dt
						,		to_dt
						FROM	care_svc_his
						WHERE	jumin = \''.$ssn.'\'
						) AS t
				INNER  join m03sugupja
				ON	   m03_ccode = org_no
				AND	   m03_jumin = \''.$ssn.'\'
				AND	   m03_name = \''.$name.'\'
				ORDER	BY to_dt DESC, from_dt DESC';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($row['svc_cd'] == '0'){
				$svcNm = '재가요양';
			}else if ($row['svc_cd'] == '1'){
				$svcNm = '가사간병';
			}else if ($row['svc_cd'] == '2'){
				$svcNm = '노인돌봄';
			}else if ($row['svc_cd'] == '3'){
				$svcNm = '산모신생아';
			}else if ($row['svc_cd'] == '4'){
				$svcNm = '장애인활동보조';
			}else if ($row['svc_cd'] == 'A'){
				$svcNm = '산모유료(비급여)';
			}else if ($row['svc_cd'] == 'B'){
				$svcNm = '병원간병(비급여)';
			}else if ($row['svc_cd'] == 'C'){
				$svcNm = '기타비급여(비급여)';
			}else if ($row['svc_cd'] == 'S'){
				$svcNm = '재가지원';
			}else if ($row['svc_cd'] == 'R'){
				$svcNm = '자원연계';
			}else{
				$svcNm = '기타';
			}

			if ($from && $to){
				if ($from >= $row['from_dt'] && $from <= $row['to_dt']){
					$duplicate = true;
				}else{
					$duplicate = false;
				}
			}else{
				$duplicate = false;
			}

			if ($orgNo == $row['org_no']){
				$orgStr = '<span style="color:BLUE;">본기관</span>';
			}else{
				$orgStr = '<span style="color:RED;">타기관</span>';
			}?>
			<tr>
				<th class="left"><?=$svcNm;?></th>
				<td class="left last">
					<span class="<?=($duplicate ? 'bold' : '');?>"><?=$myF->dateStyle($row['from_dt'],'.');?></span>
					<span class="<?=($duplicate ? 'bold' : '');?>">~</span>
					<span class="<?=($duplicate ? 'bold' : '');?>"><?=$myF->dateStyle($row['to_dt'],'.');?></span>
					<span class="<?=($duplicate ? 'bold' : '');?>" style="margin-left:5px;">(<?=$orgStr;?>)</span><?
					if ($duplicate){?>
						<span class="bold" style="color:red;">중복</span><?
					}?>
				</td>
			</tr><?
		}

		$conn->row_free();?>
	</tbody>
</table>
</div>
<?
	include_once('../inc/_footer.php');
?>