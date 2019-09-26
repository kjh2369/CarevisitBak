<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];

	//등급정보
	$sql = 'SELECT	jumin
			,		seq
			,		from_dt
			,		to_dt
			,		app_no
			,		level
			FROM	client_his_lvl
			WHERE	org_no = \''.$orgNo.'\'
			AND		svc_cd = \'0\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$lvl[$row['jumin']][$row['seq']] = Array('from'=>str_replace('-','',$row['from_dt']),'to'=>str_replace('-','',$row['to_dt']),'lvl'=>$row['level']);
	}

	$conn->row_free();

	$sql = 'SELECT	m03_jumin AS jumin
			,		m03_name AS name
			,		LEFT(m03_name, 1) AS cho
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'0\'
			AND		m03_del_yn	= \'N\'
			ORDER	BY CASE WHEN cho >= \'가\' THEN 1 ELSE 2 END, name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$idx = 0;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($cho != $myF->GetCho($row['cho'])){
			$cho  = $myF->GetCho($row['cho']);
			$choCd= $myF->GetChoCode($row['cho']);

			$idx = 0;

			if ($choCd < 0) $choCd = 99999;?>
			<div class="bold" style="width:auto; height:25px; text-align:left; padding-left:5px; line-height:25px; background-color:#B2CCFF; <?=($i > 0 ? 'margin-top:5px;' : '');?>"><?=($cho ? $cho : '그외');?></div><?
		}

		$birthDay	= $myF->issToBirthDay($row['jumin'],'.');
		$gender		= $myF->issToGender($row['jumin']);
		$jumin		= $row['jumin'];?>
		<div id="divTarget<?=$i;?>" style="cursor:default; text-align:left; line-height:1.5em; margin-left:10px; padding-top:5px; <?=($idx > 0 ? 'margin-top:5px; border-top:1px solid #4374D9;' : '');?>" onmouseover="this.style.backgroundColor='#D9E5FF';" onmouseout="if ($(this).attr('sel') == 'N'){this.style.backgroundColor='#FFFFFF';}" jumin="<?=$ed->en($jumin);?>" onclick="lfClientSel(this);" sel="N">
			<span id="name" class="bold nowrap" style="width:55px;"><?=$row['name'];?></span>
			<span class="nowrap" style="width:auto;"><?=$birthDay;?></span>
			<span class="nowrap" style="width:auto; color:#<?=($gender == '남' ? '0000FF' : 'FF0000');?>;"><?=$gender;?></span><?

			if (is_array($lvl[$jumin])){
				foreach($lvl[$jumin] as $seq => $arr){?>
					<div id="date<?=$seq;?>" style="margin-left:5px;" lvl="<?=$arr['lvl'];?>" from="<?=$arr['from'];?>" to="<?=$arr['to'];?>">
						<span class="bold"><?=$arr['lvl'];?>등급</span>
						<span><?=$myF->dateStyle($arr['from'],'.');?></span>
						<span>~</span>
						<span><?=$myF->dateStyle($arr['to'],'.');?></span>
					</div><?
				}
			}?>
		</div><?

		$idx ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>