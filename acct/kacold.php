<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
?>
<div class="title title_border">경제협 계약서 및 사업자등록증 등록현황</div><?
/*
function lfFileList($dir){
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) != false) {
				if ($file != "." && $file != "..") {
					if (filetype($dir ."/". $file) == "file"){
						//$arr[] = $dir."/".$file;
						$tmp = Explode('.',$file);
						$arr[$tmp[0]] = Array('file'=>$file, 'path'=>$dir);
					}
				}
			}
		}
		closedir($dh);
		//if (count($arr) != 0) sort($arr,SORT_REGULAR);
	}

	return $arr;
}

$list['C'] = lfFileList('../popup/kacold_popup/contract');
$list['R'] = lfFileList('../popup/kacold_popup/registration');

if (is_array($list)){
	foreach($list as $tmpGbn => $R1){
		if (is_array($R1)){
			foreach($R1 as $orgNo => $R2){
				if (!$data[$orgNo]){
					$sql = 'SELECT	m00_store_nm AS org_nm
							,		m00_mname AS mg_nm
							,		m00_ctel AS phone
							,		a.mobile
							,		b.kacold_yn
							FROM	m00center
							LEFT	JOIN	mst_manager AS a
									ON		a.org_no = m00_mcode
							LEFT	JOIN	center_comm AS b
									ON		b.org_no = m00_mcode
							WHERE	m00_mcode = \''.$orgNo.'\'
							ORDER	BY m00_mkind
							LIMIT	1';

					$row = $conn->get_array($sql);

					$data[$orgNo]['orgNo']	= $orgNo;
					$data[$orgNo]['orgNm']	= $row['org_nm'];
					$data[$orgNo]['mgNm']	= $row['mg_nm'];
					$data[$orgNo]['phone']	= $row['phone'];
					$data[$orgNo]['mobile'] = $row['mobile'];
					$data[$orgNo]['kacold']	= $row['kacold_yn'];
				}

				$data[$orgNo]['REPORT'][$tmpGbn] = Array(
					'FILE'=>$R2['file']
				,	'PATH'=>$R2['path']
				);
			}
		}
	}


	foreach($data as $orgNo => $R){
		$tmpR[] = $R;
	}

	Unset($data);

	$cnt = SizeOf($tmpR);
	for($i=0; $i<$cnt-1; $i++){
		for($j=$i+1; $j<$cnt; $j++){
			if ($tmpR[$i]['orgNm'] > $tmpR[$j]['orgNm']){
				$tmpData = $tmpR[$i];
				$tmpR[$i] = $tmpR[$j];
				$tmpR[$j] = $tmpData;
			}
		}
	}

	$data = $tmpR;
	Unset($tmpR);
}
*/

	function lfCheckFile($dir, $search){
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) != false) {
					if ($file != "." && $file != "..") {
						if (filetype($dir ."/". $file) == "file") {
							$pattern = '/'.$search.'/';
							if (preg_match($pattern,$file)) {
								$orgFile = $dir."/".$file;
								break;
							}
						}
					}
				}
			}
			closedir($dh);
		}

		return $orgFile;
	}
?>
<script type="text/javascript">
	function lfSetYn(obj, orgNo, yn){
		$.ajax({
			type :'POST'
		,	url  :'./kacold_set.php'
		,	data :{
				'orgNo':orgNo
			,	'yn':(yn == '완료' ? 'Y' : 'N')
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 'Y'){
					$(obj).html('<span style="color:BLUE;">완료</span>');
				}else{
					$(obj).html('<span style="color:RED;">미완료</span>');
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="250px">
		<col width="80px">
		<col width="90px">
		<col width="90px">
		<col width="50px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관기호</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th>
			<th class="head">연락처</th>
			<th class="head">모바일</th>
			<th class="head">계약서</th>
			<th class="head">등록증</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody><?
		/*
		if (is_array($data)){
			$no = 1;

			foreach($data as $tmpI => $R){
				if ($R['REPORT']['C']['FILE']){
					$pic['C'] = '<a href="'.$R['REPORT']['C']['PATH'].'/'.$R['REPORT']['C']['FILE'].'" target="_blank">●</a>';
				}else{
					$pic['C'] = '';
				}

				if ($R['REPORT']['R']['FILE']){
					$pic['R'] = '<a href="'.$R['REPORT']['R']['PATH'].'/'.$R['REPORT']['R']['FILE'].'" target="_blank">●</a>';
				}else{
					$pic['R'] = '';
				}

				if ($R['kacold'] == 'Y'){
					$tmpKacold = '<span style="color:BLUE;">완료</span>';
				}else{
					$tmpKacold = '<span style="color:RED;">미완료</span>';
				}?>
				<tr>
					<td class="center"><?=$no;?></td>
					<td class="center"><?=$R['orgNo'];?></td>
					<td class="center"><div class="left"><?=$R['orgNm'];?></div></td>
					<td class="center"><?=$R['mgNm'];?></td>
					<td class="center"><?=$R['phone'];?></td>
					<td class="center"><?=$R['mobile'];?></td>
					<td class="center"><?=$pic['C'];?></td>
					<td class="center"><?=$pic['R'];?></td>
					<td class="center last"><a href="#" onclick="return false;"><div class="left" onclick="lfSetYn(this, '<?=$R['orgNo'];?>',$(this).text());"><?=$tmpKacold;?></div></a></td>
				</tr><?

				$no ++;
			}
		}
		*/

		$sql = 'SELECT	m00_mcode AS org_no, m00_store_nm AS org_nm, m00_mname AS mg_nm, m00_ctel AS phone, a.mobile, b.kacold_yn
				FROM	m00center
				INNER	JOIN	b02center
						ON		b02_center = m00_mcode
						AND		from_dt <= NOW()
						AND		to_dt >= NOW()
				LEFT	JOIN	mst_manager AS a
						ON		a.org_no = m00_mcode
				LEFT	JOIN	center_comm AS b
						ON		b.org_no = m00_mcode
				WHERE	m00_domain = \'kacold.net\'
				ORDER	BY org_nm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$no = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$contFile = lfCheckFile('../popup/kacold_popup/contract', $row['org_no']);
			$bizFile = lfCheckFile('../popup/kacold_popup/registration', $row['org_no']);

			if ($contFile){
				$pic['C'] = '<a href="'.$contFile.'" target="_blank">●</a>';
			}else{
				$pic['C'] = '';
			}

			if ($bizFile){
				$pic['R'] = '<a href="'.$bizFile.'" target="_blank">●</a>';
			}else{
				$pic['R'] = '';
			}

			if ($row['kacold_yn'] == 'Y'){
				$tmpKacold = '<span style="color:BLUE;">완료</span>';
			}else{
				$tmpKacold = '<span style="color:RED;">미완료</span>';
			}?>

			<tr>
				<td class="center"><?=$no;?></td>
				<td class="center"><?=$row['org_no'];?></td>
				<td class="center"><div class="left"><?=$row['org_nm'];?></div></td>
				<td class="center"><?=$row['mg_nm'];?></td>
				<td class="center"><?=$row['phone'];?></td>
				<td class="center"><?=$row['mobile'];?></td>
				<td class="center"><?=$pic['C'];?></td>
				<td class="center"><?=$pic['R'];?></td>
				<td class="center last"><a href="#" onclick="return false;"><div class="left" onclick="lfSetYn(this, '<?=$row['org_no'];?>',$(this).text());"><?=$tmpKacold;?></div></a></td>
			</tr><?

			$no ++;
		}

		$conn->row_free();?>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>