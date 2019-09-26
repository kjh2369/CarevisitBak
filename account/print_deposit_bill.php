<?
	include("../inc/_header.php");
	include('../inc/_myFun.php');
	include('../inc/_ed.php');
	
	$mCode = $_GET['mCode'];
	$mKind = $_GET['mKind'];
	$mYM = $_GET['mYM'];
	$mDate = $_GET['mDate'];
	$mType = $_GET['mType'];
	$mSugupja = $ed->de($_GET['mSugupja']);
	if ($date == '') $date = date('Ymd', mkTime());
	$sql = "select sum(t14_amount) as amt
			  from t14deposit
			 where t14_ccode = '$mCode'
			   and t14_mkind = '$mKind'
			   and t14_pay_date = '$mYM'
			   and t14_jumin = '$mSugupja'
			   and t14_date = '$mDate'
			   and t14_type = '$mType'";
	$amt = $conn->get_data($sql);

	$sql = "select m03_name as name
			,	   m03_tel as tel
			,      m03_juso1 as addr1
			,      m03_juso2 as addr2
			,      m03_post_no as postNo
			  from m03sugupja
			 where m03_ccode = '$mCode'
			   and m03_mkind = '$mKind'
			   and m03_jumin = '$mSugupja'";
	$sugupja = $conn->get_array($sql);	

	$sql = "select m00_cname as name
			,      m00_ctel as tel
			,      m00_ccode as code
			  from m00center
			 where m00_mcode = '$mCode'
			   and m00_mkind = '$mKind'";
	$center = $conn->get_array($sql);
?>

<style>
	body{
		margin:10px;
	}
</style>
<table style="width:100%;">
<tr>
	<td style="height=40px; font-weight:bold; font-size:10pt; background-color:#eeeeee;" colspan="6">입금확인증</td>
</tr>
<tr>
	<td style="height=80px; width:8%; font-wietht:bold; text-align:center;" rowspan="2">입금자</td>
	<td style="height=30px; width:10%;  font-wietht:normal; text-align:center;">성명</td>
	<td style="width:38%; text-align:center;"><?=$sugupja['name'];?></td>
	<td style="width:8%; text-align:center" rowspan="2">요양</br>기관</td>
	<td style="width:10%; text-align:center;">사업자</br>등록번호</td>
	<td style="width:25%; text-align:center;"><?=substr($center['code'],0,3)?>-<?=substr($center['code'],3,2)?>-<?=substr($center['code'],5,5)?></td>
</tr>
<tr>
	<td style="height=50px; font-wietht:normal; text-align:center;">주소</td>
	<td style="text-align:center;"><?=$sugupja['addr1'];?>&nbsp;&nbsp;<?=getPostNoStyle($sugupja['postNo']);?>번지&nbsp;&nbsp;<?=$sugupja['addr2'];?></td>
	<td style="text-align:center;">전화번호</td>
	<td style="text-align:center;"><?=$myF->phoneStyle($center['tel']);?></td>
<!--	<td style="padding-left:10px; font-wietht:bold; text-align:left;">입금일</td>
	<td style="padding-left:10px; font-wietht:normal; text-align:left;"><?=$myF->dateStyle($mDate);?></td>-->
</tr>
<tr>
	<td style="font-wietht:bold; text-align:center;" rowspan="2">입금</br>내역</td>
	<td style="font-wietht:normal; text-align:center;">입금일</td>
	<td style="text-align:center;"><?=substr($mDate,0,4);?>년&nbsp;&nbsp;<?=substr($mDate,4,2);?>월&nbsp;<?=substr($mDate,6,2);?>일</td>
	<td style="text-align:center;" colspan="2">입금구분</td>
	<td style="text-align:center;"><?=$definition->DepositGbn($mType);?></td>
</tr>
<tr>
	<td style="font-wietht:normal; text-align:center;">입금내용</td>
	<td style="text-align:center;">본인부담금</td>
	<td style="text-align:center;" colspan="2">입금금액</td>
	<td style="text-align:center;"><?=number_format($amt);?>원</td>
</tr>
<tr style="border-style:none;" >
	<td style="text-align:center;" colspan="6"><pre>위와 같이 입금하였음을 확인합니다</br><?=substr($date,0,4);?>년 <?=substr($date,4,2);?>월&nbsp;<?=substr($date,6,2);?>일</br>                                                                              <?=$center['name'];?>  (인)  </pre></td>
</tr>

</table>
<?
	include('../inc/_footer.php');
?>

<script>self.focus();</script>