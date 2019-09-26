<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];

	$client_stat = $_POST['client_stat'];
	$find_center_kind = $_POST['find_center_kind'];	//서비스구분
	$client_name = $_POST['client_name'];

	if (!isset($find_center_kind)) $find_center_kind = 'all';

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;

?>

<script language='javascript' src='../js/report.js'></script>
<script language='javascript' src='../js/work.js'></script>
<script language='javascript'>
	var f = null;

	function search(page){
		if (page != undefined) f.page.value = page;
		f.target = '';
		f.action = 'client_app.php';
		f.submit();
	}

	function report_show(paper_dir, params, r_index){
		var r_val  = r_index.split('_');
		var r_menu = r_val[0];
		var r_id   = r_val[r_val.length - 1];

		f.report_menu.value	= r_menu;
		f.report_index.value= r_index;
		f.report_id.value	= r_id;

		_report_show_pdf(paper_dir, params, r_id);
	}

	window.onload = function(){
		f = document.f;

		__init_form(f);
	}

	function lfShowContLayer(obj, para){
		$.ajax({
			type :'POST'
		,	url  :'./svc_contract_dt_get.php'
		,	data :para
		,	beforeSend:function(){
			}
		,	success:function(data){
				var body  = $('#info_draw_body');
				var layer = $('#info_layer_body');

				var x = $(obj).offset().left;
				var y = $(obj).offset().top + $(obj).height();

				$(body).html(data)
				$(layer).css('top',y).css('left',x).show();
			}
		,	error:function(request, status, error){
				alert('code : '+request.status+'\r\nmessage : '+request.reponseText);
			}
		}).responseXML;
	}
</script>

<div class="title_border">
	<div id="report_navi" class="title" style="width:auto; float:left;">고객평가자료관리</div>
	<?
		include_once('../reportMenu/report_view_download.php');
	?>
</div>

<form name="f" method="post">

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="50px">
		<col width="50px">
		<col width="50px">
		<col width="100px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>이용상태</th>
			<td>
			<?
				echo '<select name=\'client_stat\' style=\'width:auto;\'>
						<option value=\'\'>전체</option>
						<option value=\'1\' '.($client_stat == '1' ? 'selected' : '').'>이용</option>
						<option value=\'2\' '.($client_stat == '2' ? 'selected' : '').'>중지</option>
					</select>';
			?>
			</td>

			<th class="center">서비스</th>
			<td>
			<?
				$kind_list = $conn->kind_list($code, $gHostSvc['voucher']);

				echo '<select name=\'find_center_kind\' style=\'width:auto;\'>';
				echo '<option value=\'all\'>전체</option>';

				foreach($kind_list as $i => $k){
					echo '<option value=\''.$k['code'].'\' '.($find_center_kind == $k['code'] ? 'selected' : '').'>'.$k['name'].'</option>';
				}

				echo '</select>';
			?>
			</td>
			<th>고객명</th>
			<td>
			<?
				echo '<input name=\'client_name\' type=\'text\' value=\''.$client_name.'\' onKeyDown="if(event.keyCode==13){ search(); }" >';
			?>
			</td>
			<td class="left last"><span class="btn_pack m"><button type="button" onclick="search();" >조회</button></span></td>
		</tr>
	</tbody>
</table>


<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="55px">
		<col width="45px">
		<col width="58px">
		<col width="58px">
		<col width="58px">
		<col width="45px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">고객명</th>
			<th class="head">초기상담<br>기록지</th>
			<th class="head">서비스<br>이용<br>계약서(구)</th>
			<th class="head">욕구평가<br>기록지<br>(구)</th>
			<th class="head">욕창<br>위험도<br>평가도구</th>
			<th class="head">낙상<br>위험도<br>평가도구(구)</th>
			<th class="head">익월<br>서비스<br>일정표</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?

		$wsl = ' where m03_ccode  = \''.$code.'\'
				   and m03_del_yn = \'N\'';

		/* 기존
		if ($client_stat == '1'){
			$wsl .= ' and m03_sugup_status = \'1\'';
		}else if ($client_stat == '2'){
			$wsl .= ' and m03_sugup_status != \'1\'';
		}
		*/

		if (!empty($client_name)) $wsl .= ' and m03_name like \'%'.$client_name.'%\'';

		$wsl2 = '';

		if ($client_stat == '1'){
			$wsl2 .= ' and svc_stat = \'1\'';
		}else if ($client_stat == '2'){
			$wsl2 .= ' and svc_stat != \'1\'';
		}

	    if ($find_center_kind != 'all') $wsl2 .= ' and svc_cd = \''.$find_center_kind.'\'';

		$sql = "select count(*)
				  from ( select mst.code
						 ,		mst.jumin
						 ,		mst.name
						 ,		svc.svc_cd
						   from (
						 select m03_ccode as code
						 ,	   min(m03_mkind) as kind
						 ,	   m03_jumin as jumin
						 ,     m03_name as name
						   from m03sugupja
						   $wsl
						  group by m03_jumin
						 ) as mst
						 join (
							select min(svc_cd) as svc_cd
							,	   jumin
							  from client_his_svc
							 where org_no = '$code'
							 $wsl2
							 group by jumin
							 /*order by case when date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')
							   and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d') then 1 else 2 end, seq desc*/
							 ) as svc
				on svc.jumin = mst.jumin) as t";

			/* 기존
			$sql = 'select count(*)
					  from m03sugupja   '.$wsl.'
					   and m03_mkind  = '.$conn->_client_kind();
			*/

		$total_count = $conn->get_data($sql);

		// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
		if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;

		$params = array(
			'curMethod'		=> 'post',
			'curPage'		=> 'javascript:search',
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

		$yymm = date('Ym', strtotime('+1 month'));


		$sql = "select code as k_cd
				,	   jumin as c_cd
				,	   svc_cd as k_kind
				,	   name as c_nm
				,      (select count(t01_jumin)
					          from t01iljung
					         where t01_ccode               = code
					           and t01_jumin               = jumin
						       and t01_del_yn              = 'N'
					           and left(t01_sugup_date, 6) = '".$yymm."') as i_cnt
				from ( select mst.code
					   ,		 mst.jumin
					   ,		 mst.name
					   ,		 svc.svc_cd
						from (
						select m03_ccode as code
						,	   min(m03_mkind) as kind
						,	   m03_jumin as jumin
						, m03_name as name
						  from m03sugupja
						  $wsl
						 group by m03_jumin
						) as mst
						  join (
						select min(svc_cd) as svc_cd
						,	   jumin
						  from client_his_svc
						 where org_no = '$code'
						 $wsl2
						 group by jumin
						 /*order by case when date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')
						   and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d') then 1 else 2 end, seq desc*/
						 ) as svc
				on svc.jumin = mst.jumin) as t
				order by c_nm, c_cd";
		
		$sql .= ' limit '.$pageCount.','.$item_count;
		
		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();
		$index = 0;

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if ($tmp_c_cd != $row['c_cd']){
				$client[$index] = array('no'=>$index + 1, 'k_cd'=>$row['k_cd'], 'kind'=>$row['k_kind'], 'c_cd'=>$row['c_cd'], 'c_nm'=>$row['c_nm'], 'i_cnt'=>$row['i_cnt']);

				$index ++;
				$k_seq = 0;
				$tmp_c_cd = $row['c_cd'];
			}

			$client[$index - 1]['kind'][$k_seq] = $row['k_kind'];

			$k_seq ++;
		}

		$conn->row_free();

		$client_cnt = sizeof($client);

		for($i=0; $i<$client_cnt; $i++){
			$sql = 'select (select max(concat(client_dt, \'_\', client_seq))
							  from counsel_client
							 where counsel_client.org_no     = \''.$client[$i]['k_cd'].'\'
							   and counsel_client.client_ssn = \''.$client[$i]['c_cd'].'\'
							   and counsel_client.del_flag   = \'N\') as counsel_cnt

					,      (select max(concat(r_yymm, \'_\', r_seq))
					          from r_cltbsr
					         where r_cltbsr.org_no = \''.$client[$i]['k_cd'].'\'
					           and r_cltbsr.r_c_id = \''.$client[$i]['c_cd'].'\'
							   and r_cltbsr.del_flag = \'N\') as cltbsr_cnt

					,      (select max(concat(r_yymm, \'_\', r_seq))
					          from r_cltpst
					         where r_cltpst.org_no = \''.$client[$i]['k_cd'].'\'
					           and r_cltpst.r_c_id = \''.$client[$i]['c_cd'].'\'
							   and r_cltpst.del_flag = \'N\') as cltpst_cnt

					,      (select max(concat(r_yymm, \'_\', r_seq))
					          from r_cltddt
					         where r_cltddt.org_no = \''.$client[$i]['k_cd'].'\'
					           and r_cltddt.r_c_id = \''.$client[$i]['c_cd'].'\'
							   and r_cltddt.del_flag = \'N\') as cltddt_cnt';

			$row = $conn->get_array($sql);

			//상담구분
			$sql = "select client_counsel
					,      m03_mkind as kind
					  from counsel_client
					  left join m03sugupja
						on m03_ccode = org_no
					   and m03_mkind = '".$client[$i]['kind']."'
					   and m03_jumin = client_ssn
					 where org_no   = '$code'
					   and del_flag = 'N'
					   and client_ssn = '".$client[$i]['c_cd']."'";
			$gbn[$i] = $conn -> get_data($sql);

			echo '<tr>';
			echo '<td class=\'center\'>'.$client[$i]['no'].'</td>';
			echo '<td class=\'center\'><div class=\'left nowrap\' style=\'width:70px;\' title=\''.$client[$i]['c_nm'].'\'>'.$client[$i]['c_nm'].'</div></td>';



			/**************************************************************

				초기상담기록지

			**************************************************************/
				echo '<td class=\'center\'>';

				$arr = explode('_', $row['counsel_cnt']);

				echo '<img src=\'../image/icon_editer.png\' style=\'cursor:pointer;\' onclick=\'location.href="../counsel/client_counsel_reg.php?parent_id=110&counsel_dt='.$arr[0].'&counsel_seq='.$arr[1].'";\' alt=\'초기상담기록지 수정\'> ';
				echo '<img src=\'../image/icon_pdf.png\' style=\'cursor:pointer;\' onclick=\'window.open("../counsel/client_counsel_print.php?code='.$client[$i]['k_cd'].'&dt='.$arr[0].'&seq='.$arr[1].'&gbn='.$gbn[$i].'", "REPORT", "width=700,height=900,scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no");\' alt=\'초기상담기록지 출력\'>';

				unset($arr);

				echo '</td>';



			/**************************************************************

				서비스 이용계약서

			**************************************************************/
				echo '<td class=\'center\' onmouseover=\'$("#info_layer_body").hide();\'>';

				$k_cnt = sizeof($client[$i]['kind']);

				//현재 계약 기간 조회
				$sql = "select from_dt
						,      to_dt
						  from client_his_svc
						 where org_no = '".$code."'
						   and jumin  = '".$client[$i]['c_cd']."'
						   and date_format(now(),'%Y%m%d') >= date_format(from_dt, '%Y%m%d')
						   and date_format(now(),'%Y%m%d') <= date_format(to_dt,  '%Y%m%d')
						 order by from_dt, to_dt";

				$svc = $conn->get_array($sql);


				if($svc['from_dt'] == ''){
					//최근 계약기간 조회
					$sql = "   select from_dt
							   ,      to_dt
							   ,	  max(seq) as seq
								 from client_his_svc
								where org_no = '$code'
								  and jumin = '".$client[$i]['c_cd']."'
								order by from_dt, to_dt";
					$svc = $conn->get_array($sql);
				}

				for($j=0; $j<$k_cnt; $j++){
					$para  = '{ "report_id":"CLTSVCCTC", "c_cd":"'.$ed->en($client[$i]['c_cd']).'", "kind":"'.$client[$i]['kind'][$j].'","dt":"'.$myF->dateStyle($svc['from_dt'], '.').'~'.$myF->dateStyle($svc['to_dt'], '.').'", "svcCd":"'.$find_center_kind.'" }';

					//echo '<img src=\'../image/icon_pdf.png\' style=\'cursor:pointer;\' onmouseover=\'_svc_contract_dt_get_layer(this,'.$para.');\'>';
					echo '<img src=\'../image/icon_pdf.png\' style=\'cursor:pointer;\' onmouseover=\'lfShowContLayer($(this).parent(),'.$para.');\'>';
				}

				echo '</td>';



			/**************************************************************

				욕구평가 기록지

			**************************************************************/
				echo '<td class=\'center\'>';

				if (!empty($row['cltbsr_cnt'])){
					echo '<img src=\'../image/btn_rep_list.gif\' style=\'cursor:pointer;\' onclick=\'_report_app_list(this,"APP_LIST","30_10_10_CLTBSR","'.$client[$i]['k_cd'].'","'.$ed->en($client[$i]['c_cd']).'");\' alt=\'리스트\'>';
				}else{
					echo '<img src=\'../image/btn_rep_reg.gif\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$client[$i]['k_cd'].'","30","30_10_10_CLTBSR","'.date('Ym', mktime()).'","0","'.$ed->en($client[$i]['c_cd']).'");\' alt=\'작성\'>';
				}

				/*
					$arr   = explode('_', $row['cltbsr_cnt']);
					$para  = ' "yymm":"'.$arr[0].'"';
					$para .= ',"seq":"'.$arr[1].'"';
					$para  = '{'.$para.'}';

					echo '<img src=\'../image/icon_writer.png\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$client[$i]['k_cd'].'","30","30_10_10_CLTBSR","'.date('Ym', mktime()).'","0","'.$ed->en($client[$i]['c_cd']).'");\' alt=\'욕구평가 기록지 작성\'> ';

					if (!empty($row['cltbsr_cnt'])){
						echo '<img src=\'../image/icon_editer.png\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$client[$i]['k_cd'].'","30","30_10_10_CLTBSR","'.$arr[0].'","'.$arr[1].'","'.$ed->en($client[$i]['c_cd']).'");\' alt=\'욕구평가 기록지 수정\'> ';
						echo '<img src=\'../image/icon_pdf.png\' style=\'cursor:pointer;\' onclick=\'report_show(1,'.$para.',"30_10_10_CLTBSR");\' alt=\'욕구평가 기록지 출력\'>';
					}

					unset($arr);
				*/

				echo '</td>';



			/**************************************************************

				욕창위험도 평가도구

			**************************************************************/
				echo '<td class=\'center\'>';

				if (!empty($row['cltpst_cnt'])){
					echo '<img src=\'../image/btn_rep_list.gif\' style=\'cursor:pointer;\' onclick=\'_report_app_list(this,"APP_LIST","30_10_10_CLTPST","'.$client[$i]['k_cd'].'","'.$ed->en($client[$i]['c_cd']).'");\' alt=\'리스트\'>';
				}else{
					echo '<img src=\'../image/btn_rep_reg.gif\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$client[$i]['k_cd'].'","30","30_10_10_CLTPST","'.date('Ym', mktime()).'","0","'.$ed->en($client[$i]['c_cd']).'");\' alt=\'작성\'>';
				}
				/*
					$arr   = explode('_', $row['cltpst_cnt']);
					$para  = ' "yymm":"'.$arr[0].'"';
					$para .= ',"seq":"'.$arr[1].'"';
					$para  = '{'.$para.'}';

					echo '<img src=\'../image/icon_writer.png\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$client[$i]['k_cd'].'","30","30_10_10_CLTPST","'.date('Ym', mktime()).'","0","'.$ed->en($client[$i]['c_cd']).'");\' alt=\'욕창위험도 평가도구 작성\'> ';

					if (!empty($row['cltpst_cnt'])){
						echo '<img src=\'../image/icon_editer.png\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$client[$i]['k_cd'].'","30","30_10_10_CLTPST","'.$arr[0].'","'.$arr[1].'","'.$ed->en($client[$i]['c_cd']).'");\' alt=\'욕창위험도 평가도구 수정\'> ';
						echo '<img src=\'../image/icon_pdf.png\' style=\'cursor:pointer;\' onclick=\'report_show(1,'.$para.',"30_10_10_CLTPST");\' alt=\'욕창위험도 평가도구 출력\'>';
					}

					unset($arr);
				*/

				echo '</td>';



			/**************************************************************

				낙상위험도 평가도구

			**************************************************************/
				echo '<td class=\'center\'>';

				if (!empty($row['cltddt_cnt'])){
					echo '<img src=\'../image/btn_rep_list.gif\' style=\'cursor:pointer;\' onclick=\'_report_app_list(this,"APP_LIST","30_10_10_CLTDDT","'.$client[$i]['k_cd'].'","'.$ed->en($client[$i]['c_cd']).'");\' alt=\'리스트\'>';
				}else{
					echo '<img src=\'../image/btn_rep_reg.gif\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$client[$i]['k_cd'].'","30","30_10_10_CLTDDT","'.date('Ym', mktime()).'","0","'.$ed->en($client[$i]['c_cd']).'");\' alt=\'작성\'>';
				}
				/*
					$arr   = explode('_', $row['cltddt_cnt']);
					$para  = ' "yymm":"'.$arr[0].'"';
					$para .= ',"seq":"'.$arr[1].'"';
					$para  = '{'.$para.'}';

					echo '<img src=\'../image/icon_writer.png\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$client[$i]['k_cd'].'","30","30_10_10_CLTDDT","'.date('Ym', mktime()).'","0","'.$ed->en($client[$i]['c_cd']).'");\' alt=\'낙상위험도 평가도구 작성\'> ';

					if (!empty($row['cltddt_cnt'])){
						echo '<img src=\'../image/icon_editer.png\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$client[$i]['k_cd'].'","30","30_10_10_CLTDDT","'.$arr[0].'","'.$arr[1].'","'.$ed->en($client[$i]['c_cd']).'");\' alt=\'낙상위험도 평가도구 수정\'> ';
						echo '<img src=\'../image/icon_pdf.png\' style=\'cursor:pointer;\' onclick=\'report_show(1,'.$para.',"30_10_10_CLTDDT");\' alt=\'낙상위험도 평가도구 출력\'>';
					}

					unset($arr);
				*/

				echo '</td>';




			/**************************************************************

				익월 서비스 일정표

			**************************************************************/
				echo '<td class=\'center\'>';

				if ($client[$i]['i_cnt'] > 0){
					echo '<img src=\'../image/icon_html.png\' style=\'cursor:pointer;\' onclick=\'serviceCalendarShow("'.$client[$i]['k_cd'].'","'.$client[$i]['kind'][0].'","'.substr($yymm, 0, 4).'","'.substr($yymm, 4).'","'.$ed->en($client[$i]['c_cd']).'","s","y","html","y");\' alt=\'익월 서비스 일정표 보기\'> ';
					echo '<img src=\'../image/icon_pdf.png\' style=\'cursor:pointer;\' onclick=\'serviceCalendarShow("'.$client[$i]['k_cd'].'","'.$client[$i]['kind'][0].'","'.substr($yymm, 0, 4).'","'.substr($yymm, 4).'","'.$ed->en($client[$i]['c_cd']).'","s","y","pdf","y");\' alt=\'익월 서비스 일정표 출력\'>';
				}else{
					echo '-';
				}

				echo '</td>';



			echo '<td class=\'left last\'></td>';
			echo '</tr>';

			unset($row);
		}

		echo '<input name=\'para_yymm\' type=\'hidden\' value=\'\'>'; //
		echo '<input name=\'para_seq\'  type=\'hidden\' value=\'\'>'; //
		echo '<input name=\'para_kind\' type=\'hidden\' value=\'\'>'; //서비스
		echo '<input name=\'para_dt\'   type=\'hidden\' value=\'\'>'; //일자
		echo '<input name=\'para_l_dt\' type=\'hidden\' value=\'\'>'; //서비스제공계약서 인정유효일자
		echo '<input name=\'para_k_dt\' type=\'hidden\' value=\'\'>'; //서비스제공계약서 본인부담일자
		echo '<input name=\'para_m_cd\' type=\'hidden\' value=\'\'>'; //직원
		echo '<input name=\'para_c_cd\' type=\'hidden\' value=\'\'>'; //고객
		echo '<input name=\'para_type\' type=\'hidden\' value=\'\'>'; //타입
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

<input name="code"	type="hidden" value="<?=$code;?>">
<input name="page"	type="hidden" value="<?=$page;?>">
<input name="kind"	type="hidden" value="">
<input name="ssn"	type="hidden" value="">
<input name="report_menu"	type="hidden" value="">
<input name="report_index"	type="hidden" value="">
<input name="report_id"		type="hidden" value="">
<input name="yymm"	type="hidden" value="">
<input name="seq"	type="hidden" value="">
<input name="copy_yn"	type="hidden" value="">

<div id="APP_LIST" style="position:absolute; top:0; left:0; width:auto; background-color:#ffffff; border:2px solid #0e69b0; display:none;"></div>

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>