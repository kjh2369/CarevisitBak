<?
	include("../inc/_header.php");

	$mPopup  = $_POST['mPopup'];
	$mCode   = $_POST['mCode'];
	$mKind   = $_POST['mKind'];
	$mKey    = $_POST['mKey'];
	$mDate   = $_POST['mDate'];
	$mFmTime = $_POST['mFmTime'];
	$mSeq    = $_POST['mSeq'];

	$sql = "select m03_jumin"
		 . "  from m03sugupja"
		 . " where m03_ccode = '".$mCode
		 . "'  and m03_mkind = '".$mKind
		 . "'  and m03_key   = '".$mKey
		 . "'";
	$mJumin = $conn->get_data($sql);

	$sql = "select m04_svc_code1"
		 . ",      m04_svc_code2"
		 . ",      m04_svc_name"
		 . "  from m04svccode";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();
	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$svcCodeList[$i]['code1'] = $row['m04_svc_code1'];
		$svcCodeList[$i]['code2'] = $row['m04_svc_code2'];
		$svcCodeList[$i]['name']  = $row['m04_svc_name'];
	}
	$conn->row_free();

	$sql = "select t00_sugup_date"
		 . ",      t00_sugup_fmtime"
		 . ",      t00_sugup_totime"
		 . ",      t00_yoyangsa_id"
		 . ",      t00_svc_subcdcnt"
		 . ",      t00_svc_subcode1,  t00_svc_subtime1"
		 . ",      t00_svc_subcode2,  t00_svc_subtime2"
		 . ",      t00_svc_subcode3,  t00_svc_subtime3"
		 . ",      t00_svc_subcode4,  t00_svc_subtime4"
		 . ",      t00_svc_subcode5,  t00_svc_subtime5"
		 . ",      t00_svc_subcode6,  t00_svc_subtime6"
		 . ",      t00_svc_subcode7,  t00_svc_subtime7"
		 . ",      t00_svc_subcode8,  t00_svc_subtime8"
		 . ",      t00_svc_subcode9,  t00_svc_subtime9"
		 . ",      t00_svc_subcode10, t00_svc_subtime10"
		 . "  from t00iljung"
		 . " inner join m02yoyangsa"
		 . "    on m02_ccode  = t00_ccode"
		 . "   and m02_mkind  = t00_mkind"
		 . "   and m02_yjumin = t00_yoyangsa_id"
		 . " where t00_ccode = '".$mCode
		 . "'  and t00_mkind = '".$mKind
		 . "'  and t00_jumin = '".$mJumin
		 . "'  and t00_sugup_date   = '".$mDate
		 . "'  and t00_sugup_fmtime = '".$mFmTime
		 . "'  and t00_sugup_seq    = '".$mSeq
		 . "'";

	$conn->query($sql);
	$row = $conn->fetch();
?>
<style>
body{
	margin-top:0px;
	margin-left:0px;
}
</style>
<table class="view_type1" style="width:100%; margin-top:0px;">
<tr>
<th style="width:15%; padding:0px; text-align:center;">No</th>
<th style="width:55%; padding:0px; text-align:center;">서비스명</th>
<th style="width:20%; padding:0px; text-align:center;">시간</th>
</tr>
<?
	for($i=1; $i<=10; $i++){
	?>
		<tr>
		<th style="padding:0px; text-align:center;"><?=$i;?></th>
		<td><?=GetSvcName($svcCodeList, $row['t00_svc_subcode'.$i]);?></td>
		<td style="padding:0px; text-align:center;"><?=($row['t00_svc_subtime'.$i] != '0' ? ($row['t00_svc_subtime'.$i].'분') : '분');?></td>
		</tr>
	<?
	}
?>
<tr>
<td colspan="3" style="text-align:right;">
<input name="btnClose" type="button" value="" onClick="window.close();" style="width:59px; height:21px; border:0px; background:url('../image/btn_close.png') no-repeat; cursor:pointer;">
</td>
</tr>
</table>
<?
	$conn->row_free();

	include("../inc/_footer.php");

	function GetSvcName($svcList, $svcCode){
		$svcCount = sizeOf($svcList);
		$svcName = '';
		for($i=0; $i<$svcCount; $i++){
			if ($svcList[$i]['code2'] == $svcCode){
				$svcName = $svcList[$i]['name'];
				break;
			}
		}

		return $svcName;
	}
?>