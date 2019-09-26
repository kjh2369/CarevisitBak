<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	/*
	 * 기능		: 기관등록 / 수정
	 * 작성자	: 김재용
	 * 일자		: 2011.03.21
	 */
	$comDomain = $myF->_domain();					//도메인
	//$companyCD = $conn->_company_code($comDomain);

	//기관기호
	if ($_SESSION["userLevel"] == "A" || $_SESSION["userLevel"] == "B"){
		$mCode = $_REQUEST["mCode"];
	}else{
		$mCode = $_SESSION["userCenterCode"];
	}

	$item_count = 20;			//행 카운트
	$page_count = 10;			//페이지 카운트
	$page = $_REQUEST["page"];	//링크페이지

	if (!is_numeric($page)) $page = 1;

	switch('branch'){
		case _COM_:
			if ($comDomain == _DWCARE_)
				$mark_val = 'ON';
			else
				$mark_val = 'GE';
			break;
		case _BRAN_:
			if ($_SESSION['userLevel'] == 'A'){
				if ($comDomain == _DWCARE_)
					$mark_val = 'ON';
				else
					$mark_val = 'G';
			}else{
				$mark_val = $_SESSION['userBranchCode']; //지사코드
			}
			break;
		case _STORE_:
			$mark_val = 'S';
			break;
	}


	#검색변수
	$find_center_code	= $_REQUEST['find_center_code'];
	$find_center_name	= $_REQUEST['find_center_name'];						//기관명
	$find_center_addr	= $_REQUEST['find_center_addr'];						//주소
	$find_member_cnt	= $_REQUEST['find_member_cnt'];							//직원수
	$find_client_cnt	= $_REQUEST['find_client_cnt'];							//수급자수
	$find_iljung_cnt	= $_REQUEST['find_iljung_cnt'];							//일정수
	$find_cont_date     = $_REQUEST['find_cont_date'];							//계약
	$find_cont_no_date  = $_REQUEST['find_cont_no_date'];						//미계약
	$find_from_yymm     = str_replace('-', '', $_REQUEST['find_from_yymm']);	//연결시작년월
	$find_to_yymm       = str_replace('-', '', $_REQUEST['find_to_yymm']);		//연결종료년월
	$person				= explode('_',$_REQUEST['find_person']);		        //person[0]:지사명 person[1]:담당자명
	$find_branch		= $_REQUEST['find_branch'];								//지사명
	$find_person        = $person[1];											//담당자명

	/*
	if (!$_POST['load']){
		if ($find_member_cnt == '') $find_member_cnt = 'Y';
		if ($find_client_cnt == '') $find_client_cnt = 'Y';
		if ($find_iljung_cnt == '') $find_iljung_cnt = 'Y';
	}
	*/

	$today = date('Ym', mktime());
?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>
<script language='javascript'>

$(document).ready(function(){
	__init_form(document.f);
});

function showCenterScreen(did, url, id, pw){
	var tgt = 'WINDOW_CENTER_'+did;
	var win = window.open('about:blank',tgt);
	var frm = document.createElement('form');

	frm.appendChild(__create_input('loc', 'admin'));
	frm.appendChild(__create_input('uCode', id));
	frm.appendChild(__create_input('uPass', pw));
	frm.setAttribute('method', 'post');

	document.body.appendChild(frm);

	frm.target = tgt;
	frm.action = url;
	frm.submit();
}

function excel(){

	document.f.action = "excel_list.php";
	document.f.submit();

}

// 선택한 지사의 담당자리스트
function _getPerson(p_branch){
	var target  = document.f.find_person;
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

					__setSelectBox(target, value[0] + '_' + value[2], value[1]);
				}
			}
		}
	);
}


// 선택한 담당자의 지사
function _getBranch(p_person){
	var person = p_person.split('_');

	document.getElementById('find_branch').value = person[0];

}

function copy_val(obj){
	document.getElementById('find_to_yymm').value = obj.value;
}

</script>
<form name="f" method="post">
<div class="title">기관조회</div>
<table class="my_table my_border">
	<colgroup>
		<col width="55px">
		<col width="100px">
		<col width="45px">
		<col width="110px">
		<col width="30px">
		<col width="300px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관기호</th>
			<td>
				<input name="find_center_code" type="text" value="<?=$find_center_code;?>" maxlength="15" style="width:100%; ime-mode:disabled;" onKeyDown="if(event.keyCode!=13){}else{_centerList('<?=$page;?>');}" onkeyup="if(event.keyCode==13){_centerList('<?=$page;?>');}" onFocus="this.select();">
			</td>
			<th>기관명</th>
			<td>
				<input name="find_center_name" type="text" value="<?=$find_center_name;?>" maxlength="20" onKeyDown="if(event.keyCode==13){_centerList('<?=$page;?>');}" style="width:100%; ime-mode:active;" onFocus="this.select();">
			</td>
			<th>선택</th>
			<td class="left">
				<input name="find_cont_date" type="checkbox" class="checkbox" value="Y" <? if($find_cont_date == 'Y'){?>checked<?} ?>>계약
				<input name="find_cont_no_date" type="checkbox" class="checkbox" value="Y" <? if($find_cont_no_date == 'Y'){?>checked<?} ?>>미계약
				<input name="find_member_cnt" type="checkbox" class="checkbox" value="Y" <? if($find_member_cnt == 'Y'){?>checked<?} ?>>직원
				<input name="find_client_cnt" type="checkbox" class="checkbox" value="Y" <? if($find_client_cnt == 'Y'){?>checked<?} ?>>수급
				<input name="find_iljung_cnt" type="checkbox" class="checkbox" value="Y" <? if($find_iljung_cnt == 'Y'){?>checked<?} ?>>일정
			</td>
			<td class="other" style="line-height:26px; padding-left:5px; vertical-align:top; padding-top:2px;">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_centerList('1');">조회</button></span>
				<?
					if ($_SESSION["userLevel"] == "A"){
						if ($gDomain != 'vaerp.com'){
							//<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_reg_center('');">등록</button></span>
						}
					}
				?>
				<span class="btn_pack m icon"><span class="excel"></span><button type="button" onFocus="this.blur();" onClick="excel();">엑셀</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table">
	<colgroup>
		<col width="60px">
		<col width="100px">
		<col width="55px">
		<col width="150px">
		<col width="40px">
		<col width="225px">
	</colgroup>
	<tbody>
		<tr>
			<th class="head">주소</th>
			<td>
				<input name="find_center_addr" type="text" value="<?=$find_center_addr;?>" maxlength="30" onKeyDown="if(event.keyCode==13){_centerList('<?=$page;?>');}" style="width:100%; ime-mode:active;" onFocus="this.select();">
			</td>
			<th>연결년월</th>
			<td>
				<input class="yymm" name="find_from_yymm" type="text" value="<?=substr($find_from_yymm, 0, 4).'-'.substr($find_from_yymm, 4, 2);?>" maxlength="6" onchange="copy_val(this);"> - <input class="yymm" name="find_to_yymm" type="text" value="<?=substr($find_to_yymm, 0, 4).'-'.substr($find_to_yymm, 4, 2);?>" maxlength="6" >
			</td>
			<th class="head">지사</th>
			<td class="last">
			<select name="find_branch" onChange="_getPerson(this.value);">
			<option value="">-지사선택-</option>
			<?
				ob_start();

				//지사테이블 조회
				$sql = "select b00_code, b00_name
						  from b00branch
						 where b00_domain = '".$gDomain."'
						 order by b00_name";
				$conn->query($sql);
				$conn->fetch();
				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					echo "<option value='".$row[0]."' ".($row[0] == $find_branch ? "selected" : "").">".$row[1].'['.$row[0].']'."</option>";
				}
				$conn->row_free();
				$opton = ob_get_contents();
				ob_end_clean();

				echo $opton;
			?>
			</select>
			<select name="find_person" style="margin:0;" onchange="_getBranch(this.value)">
			<?
				ob_start();

				//담당자테이블 조회
				if($find_branch != ''){
					$sql = "select b01_branch, b01_name, b01_code
							  from b01person
							 where b01_branch = '".$find_branch."'
							 order by b01_name";
				}else {
					$sql = "select b00_code
							,      b01_name
							,	   b01_code
							  from b01person
							 inner join b00branch
								on b00_code = b01_branch
							   and b00_domain = '".$comDomain."'
							 where b01_branch like '$mark_val%'
							 order by b01_branch, b01_code";
				}

				$conn->query($sql);
				$conn->fetch();
				$rowCount = $conn->row_count();


				echo "<option value=''>-담당자-</option>";

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					echo "<option value='".$row[0].'_'.$row[2]."' ".($row[0].$row[2] == $person[0].$person[1] ? "selected" : "")." >".$row[1]."</option>";
				}

				$conn->row_free();
				$opton = ob_get_contents();
				ob_end_clean();

				echo $opton;
			?>
			</select>
		</td>
		</tr>
	</tbody>
</table>
<table class="my_table my_border" style="margin-top:-1px;">
	<colgroup>
		<col width="40px">
		<col>
		<col width="75px">
		<col width="50px">
		<col width="85px">
		<?
			if ($_SESSION['userLevel'] == 'A' || $_SESSION['userLevel'] == 'B'){?>
			    <col width="50px">
				<col width="70px">
				<col width="70px">
				<col width="35px">
				<col width="35px">
				<col width="35px"><?
			}
		?>
		<col width="55px">
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관명</th>
			<th class="head">기관기호</th>
			<th class="head">대표자</th>
			<th class="head">연락처</th>
			<?
				if ($_SESSION['userLevel'] == 'A' || $_SESSION['userLevel'] == 'B'){?>
					<th class="head">담당자</th>
					<th class="head">사용일자</th>
					<th class="head">계약일자</th>
					<th class="head">직원</th>
					<th class="head">수급자</th>
					<th class="head">일정</th><?
				}
			?>
			<th class="last head">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$wsl = '';
		if ($_SESSION["userLevel"] == "A" || $_SESSION["userLevel"] == "B"){
			if ($find_center_code != '')  $wsl .= " and m00_mcode like '$find_center_code%'";		//기관코드 검색
			if ($find_center_name != '')  $wsl .= " and m00_store_nm like '%$find_center_name%'";	//기관명 검색
			if ($find_center_addr != '')  $wsl .= " and m00_caddr1 like '%$find_center_addr%'";		//주소 검색


			if ($find_cont_date   == 'Y' and $find_cont_no_date   == 'Y'){
				//계약,미계약 둘다 체크했을 시
			}else {

				//그 외일 경우
				if ($find_cont_date   == 'Y') $wsl .= " and ifnull(m00_cont_date, '') != ''";		//계약 기관 검색
				if ($find_cont_no_date   == 'Y') $wsl .= " and ifnull(m00_cont_date, '') = ''";		//미계약 기관 검색
			}

			if ($find_from_yymm != '') $wsl .= " and left(m00_start_date, 6) between '$find_from_yymm' and '".($find_to_yymm != '' ? $find_to_yymm : '999912')."'";		//연결년월 검색
			if ($find_branch != '') $wsl .= " and b02_branch = '$find_branch'";																							//지사명 검색
			if ($find_branch != '' and $find_person != '')  $wsl .= " and b02_person = '$find_person'";																	//지사명있을 시 담당자 검색

			if ($find_member_cnt == 'Y'){			//직원있는기관만 검색
				$wsl .= ' and  (select count(m02_yjumin)
								  from m02yoyangsa
							     where m02_ccode  = m00_mcode
								   and m02_del_yn = \'N\') > 0';
			}

			if ($find_client_cnt == 'Y'){		   //수급자있는기관만 검색
				$wsl .= ' and  (select count(m03_jumin)
								  from m03sugupja
							     where m03_ccode  = m00_mcode
								   and m03_del_yn = \'N\') > 0';
			}

			if ($find_iljung_cnt == 'Y'){		  //일정있는거만 검색
				$wsl .= ' and  (select count(distinct t01_jumin)
								  from t01iljung
							     where t01_ccode  = m00_mcode
								   and t01_del_yn = \'N\'
								   and t01_sugup_date like \''.$today.'%\') > 0';
			}

		/*************************/
		//기관조회 : 사용일자안보여서 추가였습니다.
		//	$wsl .= ' and m00_mkind = (select min(m00_mkind) from m00center as tmp where tmp.m00_mcode = m00center.m00_mcode and m00_del_yn = \'N\')';
		/************************/

			$wsl .= ' and m00_mkind = (select min(m00_mkind) from m00center as tmp where tmp.m00_mcode = m00center.m00_mcode and m00_del_yn = \'N\')';

		}else{
			$wsl .= ' and m00_mcode = \''.$_SESSION["userCenterCode"].'\'';
		}

		/*
		$sql = 'select count(*)
				  from m00center as mst
				 inner join b02center
				    on b02_center = m00_mcode
				 where m00_mcode is not null
				   and m00_mkind  = (select min(chd.m00_mkind) from m00center as chd where chd.m00_mcode = mst.m00_mcode and chd.m00_del_yn = 'N') $wsl
				   and m00_del_yn = 'N'';
		*/

		//글전체갯수
		$sql = 'select count(code)
				  from (
					   select m00_mcode as code
					   ,      min(m00_mkind) as kind
						 from m00center
						inner join b00branch
						   on b00_domain = \''.$comDomain.'\'';

		if ($_SESSION['userLevel'] == 'B'){
			$sql .= ' and b00_code = \''.$mark_val.'\'';
		}

		$sql .= '		inner join b02center
						   on b02_center = m00_mcode
                          and b02_branch = b00_code
						where m00_mcode is not null
						  and m00_del_yn = \'N\' '.$wsl.'
						group by m00_mcode
					   ) as t';

		$total_count = $conn->get_data($sql);

		// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
		if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;

		//페이지링크를 하기위한 배열
		$params = array(
			'curMethod'		=> 'post',
			'curPage'		=> 'javascript:_centerList',
			'curPageNum'	=> $page,
			'pageVar'		=> 'page',
			'extraVar'		=> '',
			'totalItem'		=> $total_count,
			'perPage'		=> $page_count,
			'perItem'		=> $item_count,
			'prevPage'		=> '[이전]',
			'nextPage'		=> '[다음]',
			'prevPerPage'	=> '[이전'.$page_count.'페이지]',
			'nextPerPage'	=> '[다음'.$page_count.'페이지]',
			'firstPage'		=> '[처음]',
			'lastPage'		=> '[끝]',
			'pageCss'		=> 'page_list_1',
			'curPageCss'	=> 'page_list_2'
		);

		$pageCount = $page;

		if ($pageCount == ""){
			$pageCount = "1";
		}

		$pageCount = (intVal($pageCount) - 1) * $item_count;

		/*
		$sql = "select m00_store_nm
				,      m00_mcode
				,      m00_code1
				,      m00_mname
				,      m00_ctel
				,      m00_start_date
				,      m00_cont_date
				,      m97_id
				,      m97_pass";

		if ($_SESSION['userLevel'] == 'A'){
			$sql .= "
				,    (select count(*)
						from m02yoyangsa
					   where m02_ccode = m00_mcode
					     and m02_mkind = ".$conn->_mem_kind()."
					     and m02_del_yn = 'N') as member_cnt
				,    (select count(*)
						from m03sugupja
					   where m03_ccode = m00_mcode
					     and m03_mkind = ".$conn->_client_kind()."
					     and m03_del_yn = 'N') as client_cnt
				,    (select count(distinct t01_jumin)
					    from t01iljung
					   where t01_ccode  = m00_mcode
					     and t01_del_yn = 'N'
					     and t01_sugup_date like '$today%') as iljung_cnt";
		}

		$sql .= " from m00center as mst
				 inner join b02center
				    on b02_center = m00_mcode
				 inner join m97user
					on m97_user = m00_mcode";

		$sql .= "
				 where m00_mcode is not null
				   and m00_mkind  = (select min(chd.m00_mkind) from m00center as chd where chd.m00_mcode = mst.m00_mcode and chd.m00_del_yn = 'N') $wsl
				   and m00_del_yn = 'N'
				 order by m00_store_nm
				 limit $pageCount, $item_count";
		*/

		$sql = 'select code, kind, nm, cd, m_nm, tel, use_dt, cont_dt, id, pw, manager';

		if ($_SESSION['userLevel'] == 'A'){
			/*
				$sql .= ', (select count(distinct concat(m02_ccode, \'_\', m02_yjumin))
							  from m02yoyangsa
							 where m02_ccode        = t.code
							   and m02_ygoyong_stat = \'1\'
							   and m02_del_yn       = \'N\') as member_cnt
						 , (select count(distinct concat(m03_ccode, \'_\', m03_jumin))
							  from m03sugupja
							 where m03_ccode        = t.code
							   and m03_sugup_status = \'1\'
							   and m03_del_yn       = \'N\') as client_cnt';
			 */
			$sql .= '
					,		(
							SELECT	COUNT(DISTINCT jumin)
							FROM	mem_his
							WHERE	org_no		= t.code
							AND		employ_stat = \'1\'
							AND		join_dt <= NOW()
							AND		IFNULL(quit_dt,\'9999-12-31\') >= NOW()
							) AS member_cnt
					,		(
							SELECT	COUNT(DISTINCT jumin)
							FROM	client_his_svc
							WHERE	org_no	 = t.code
							AND		from_dt <= NOW()
							AND		to_dt	>= NOW()
							)AS client_cnt';


			if ($find_iljung_cnt == 'Y'){
				$sql .= ', (select count(distinct t01_jumin)
							  from t01iljung
							 where t01_ccode  = t.code
							   and t01_del_yn = \'N\'
							   and t01_sugup_date like \''.$today.'%\') as iljung_cnt';
			}else{
				$sql .= ', 0 as iljung_cnt';
			}
		}

		$sql .= ' from (
					   select m00_mcode as code
					   ,      min(m00_mkind) as kind
					   ,      m00_store_nm as nm
					   ,      m00_mcode as cd
					   ,      m00_mname as m_nm
					   ,      m00_ctel as tel
					   ,      m00_start_date as use_dt
					   ,      m00_cont_date as cont_dt
					   ,      m97_id as id
					   ,      m97_pass as pw
					   ,	  b01_name as manager
						 from m00center
						inner join b00branch
						   on b00_domain = \''.$comDomain.'\'';

		if ($_SESSION['userLevel'] == 'B'){
			$sql .= ' and b00_code = \''.$mark_val.'\'';
		}

		$sql .= '		inner join b02center
						   on b02_center = m00_mcode
						  and b02_branch = b00_code
						inner join b01person
						   on b01_branch = b02_branch
						  and b01_code   = b02_person
						inner join m97user
						   on m97_user = m00_mcode
						where m00_mcode is not null
						  and m00_del_yn = \'N\' '.$wsl.'
						group by m00_mcode
					   ) as t
				 order by use_dt DESC, nm
				 limit '.$pageCount.','.$item_count;

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				if ($gDomain == _KLCF_){
					$url = 'care.'.$gDomain;
				}else{
					$url = 'www.'.$gDomain;
				}

				if ($gDomain == 'dolvoin.net' && $_SESSION['userCode'] == 'carevisit'){
					$org_name = str_replace('돌보인 ', '', $row['nm']);
				}else{
					$org_name = $row['nm'];
				}?>
				<tr>
					<td class="center"><?=$pageCount + ($i + 1);?></td>
					<td class="left"><a href="#" onclick="_reg_center('<?=$row['code'];?>');"><?=$org_name;?></a></td>
					<td class="left"><?=$row['code'];?></td>
					<td class="left"><?=$row['m_nm'];?></td>
					<td class="left"><?=$myF->phoneStyle($row['tel']);?></td>
					<?
						if ($_SESSION['userLevel'] == 'A' || $_SESSION['userLevel'] == 'B'){?>
							<td class="left"><?=$row['manager'];?></td>
							<td class="center"><?=$myF->dateStyle($row['use_dt'],'.');?></td>
							<td class="center"><?=$myF->dateStyle($row['cont_dt'],'.');?></td>
							<td class="right"><?=$row['member_cnt'];?></td>
							<td class="right"><?=$row['client_cnt'];?></td>
							<td class="right"><?=$row['iljung_cnt'];?></td>
							<td class="left last"><?
								if ($debug || $gDomain != 'vaerp.com'){?>
									<a href="#" onclick="showCenterScreen('<?=$gDomainID;?>','http://<?=$url;?>/main/login_ok.php','<?=$ed->en($row['id']);?>','<?=$ed->en($row['pw']);?>');">바로가기</a><?
								}?>
							</td><?
						}else{?>
							<td class="other">&nbsp;</td><?
						}
					?>
				</tr>
			<?
			}
		}else{
		?>	<tr>
				<td class="center last" colspan="12">::검색된 데이타가 없습니다.::</td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
</table>
<div style="text-align:left;">
	<div style="position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($total_count);?></div>
	<div style="width:100%; text-align:center;">
	<?
		$paging = new YsPaging($params);
		$paging->printPaging();
	?>
	</div>
</div>
<input name="mCode" type="hidden" value="">
<input name="page"	type="hidden" value="<?=$page;?>">
<input name="load"	type="hidden" value="1">
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>