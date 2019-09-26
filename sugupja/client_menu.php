<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$mode  = $_POST['mode'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	if (!empty($jumin)){
		$lbNew = false;
	}else{
		$lbNew = true;
	}

	$today     = date('Y-m-d');
	$laSvcList = $conn->kind_list($code, $gHostSvc['voucher']);
	
	$sql = 'select svc_cd as cd
			,      case when svc_cd = \'0\' then \'재가요양\'
			            when svc_cd >= \'1\' and svc_cd <= \'4\' then \'바우처\' else \'기타유료\' end as gbn
			,	   from_dt
			,      to_dt
			  from client_his_svc
			 where org_no   = \''.$code.'\'
			   and jumin    = \''.$jumin.'\'
			   and from_dt <= date_format(now(),\'%Y-%m-%d\')
			   and to_dt   >= date_format(now(),\'%Y-%m-%d\')
			 order by svc_cd';

	$laUseSvc = $conn->_fetch_array($sql, 'cd');

	foreach($laSvcList as $row){
		if ($row['code'] == '0'){
			$liSvc = 0;
		}else if ($row['code'] >= '1' && $row['code'] <= '4'){
			$liSvc = 1;
		}else if ($row['code'] == '6'){
			$liSvc = 3;
		}else{
			$liSvc = 2;
		}

		if ($row['code'] == '6'){
		}else{
			$laSvc[$liSvc][$row['code']] = $row['id'];

			if (isset($laUseSvc[$row['code']]))
				$laSvc[$liSvc][$row['code']] = 'Y';
		}
	}

	if ($view_type != 'read'){?>
		<div id="loSvcMenu" style="padding:10px 10px 0 10px;">
			<table class="my_table my_border_blue" style="width:100%;">
				<colgroup>
					<col width="10%">
					<col width="45%" span="2">
				</colgroup>
				<thead>
					<tr>
						<th class="head bold">구분</th>
						<th class="head bold">현재 이용서비스</th>
						<th class="head bold">서비스 등록/변경</th>
					</tr>
				</thead>
				<tbody><?
					if (is_array($laSvc)){
						foreach($laSvc as $i => $row){
							if ($i == 0)
								$lsGbn = '재가요양';
							else if ($i == 1)
								$lsGbn = '바우처';
							else if ($i == 3)
								$lsGbn = '재가관리';
							else
								$lsGbn = '기타유료';?>
							<tr>
								<th class="center"><div class="left"><?=$lsGbn;?></div></th>
								<td class="center"><?
								foreach($row as $cd => $yn){
									if ($yn == 'Y'){?>
										<div id="loSvcCd_<?=$cd;?>" value="Y" class="left bold" style="float:left; width:auto; margin-right:3px;"><span style="color:#ff0000; margin-right:2px;">√</span><?=$conn->_svcNm($cd);?></div><?
									}
								}?>
								</td>
								<td class="center last"><?
								foreach($row as $cd => $id){
									if ($id != 'Y'){
										//if ($lbNew){
										//	$lsLink = '<a id="loLink_'.$cd.'" href="#" onclick="return lfSvcShow(\''.$cd.'\');">'.$conn->_svcNm($cd).'</a>';
										//}else{
										//	$lsLink = '<a href="#" onclick="return _clientSvcReg(\''.$cd.'\',\''.$id.'\');">'.$conn->_svcNm($cd).'</a>';
										//}
										if ($lbNew){
											$lsLink = '<span class="btn_pack m"><button id="loLink_'.$cd.'" onclick="return lfSvcShow(\''.$cd.'\');">'.$conn->_svcNm($cd).'</button></span>';
										}else{
											$lsLink = '<span class="btn_pack m"><button onclick="return _clientSvcReg(\''.$cd.'\',\''.$id.'\');">'.$conn->_svcNm($cd).'</button></span>';
										}?>
										<div id="loSvcCd_<?=$cd;?>" value="N" class="left" style="float:left; width:auto;"><?=$lsLink;?></div><?
									}
								}?>
								</td>
							</tr><?
						}
						unset($laSvc);
					}else{?>
						<tr>
							<td class="center" colspan="3">::서비스내역이 없습니다.::</td>
						</tr><?
					}?>
				</tbody>
			</table>
		</div>
		<div id="loSvcHistory" style="padding:0 10px 10px 10px; display:none;"></div><?
	}
	include_once('./client_content.php');
	include_once('../inc/_db_close.php');?>

	<script type="text/javascript">
		$(document).ready(function(){
			if ('<?=$view_type;?>' != 'read'){
				__init_form(document.f);
			}

			_clientSetMgmtData(); //장기요양보험
			//_clientSetKindData(); //수급자구분
			_clientSetLimitData(); //청구한도
			_clientSetNurseData(); //가사간병
			_clientSetOldData(); //노인돌봄
			_clientSetBabyData(); //산모신생아
			_clientSetDisData(); //장애인활동지원
			_clientSetDisDataNew(); //장애인활동지원
			_clientSetLevelData('21','1');
			_clientSetLevelData('22','2');
			_clientSetLevelData('23','3');
			_clientSetLevelData('24','4');
			_clientSetLevelDataNew('24','4');
		});
	</script>