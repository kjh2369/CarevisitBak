<?
	include_once('../inc/_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	parse_str($_POST['para'],$val);

	$orgNo = $_SESSION['userCenterCode'];
	$rowId = $_POST['rowId'];
	$no = $_POST['no'];
	$SR = $_POST['SR'];
	$jumin = $ed->de($val['jumin']);
	$date = $val['date'];
	$time = $val['time'];
	$seq = $val['seq'];
	$now = Date('YmdHis');

	$sql = 'SELECT	picture
			FROM	care_result
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		date	= \''.$date.'\'
			AND		time	= \''.$time.'\'
			AND		seq		= \''.$seq.'\'
			AND		no		= \''.$no.'\'';

	$pic = '../care/pic/'.$conn->get_data($sql);

	if (is_file($pic)){
		$size = getimagesize($pic);
		$width = $size[0];
		$height = $size[1];
		$rate = 1;
		define(SIZE,250);

		if ($width > SIZE || $height > SIZE){
			if ($width > $height){
				$rate = $height / $width;
			}else{
				$rate = $width / $height;
			}

			if ($width > $height){
				$width = SIZE;
				$height = SIZE * $rate;
			}else{
				$height = SIZE;
				$width = SIZE * $rate;
			}
		}
	}


	//org_no,org_type,jumin,date,time,seq,no
	//content,picture,file,del_flag,insert_id,insert_dt,update_id,update_dt
?>
<script type="text/javascript">
	$(document).ready(function(){
		var top = $('#divBody').offset().top;
		var height = $(this).height() - top - 50;

		$('div[id="divBody"]').height(height);
	});

	function lfPicShow(obj){
		if (!__checkImageExp2(obj)) return;

		var path = __get_file_path(obj);

		$('#divImgView').css('filter','progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'file://'+path+'\', width=\'230px\', height=\'230px\', sizingMethod=\'image\'');
	}

	function lfPicReg(){
		var f = document.f;

		f.action = 'care_result_pic_reg.php';
		f.submit();
	}
</script>
<div class="title title_border">실적사진등록</div>
<form name="f" method="post" enctype="multipart/form-data">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50%" span="2">
	</colgroup>
	<tbody>
		<tr>
			<td colspan="2" style=""><input name="pic" type="file" style="width:100%; background-color:#FFFFFF;" onchange="lfPicShow(this);"></td>
		</tr>
		<tr>
			<th class="head">현재</th>
			<th class="head">변경</th>
		</tr>
		<tr>
			<td>
				<div id="divBody" style="width:100%; height:100px; overflow:auto;"><div style="height:250px; text-align:center; vertical-align:middle;"><img src="<?=$pic;?>?timestamp=<?=$now;?>" style="width:<?=$width;?>; height:<?=$height;?>;"></div></div>
			</td>
			<td>
				<div id="divBody" style="width:100%; height:100px; overflow:auto;"><div id="divImgView"></div></div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="center bottom last" colspan="2">
				<a href="#" onclick="lfPicReg();">등록</a> |
				<a href="#" onclick="self.close();">취소</a>
			</td>
		</tr>
	</tfoot>
</table>
<input name="rowId" type="hidden" value="<?=$rowId;?>">
<input name="jumin" type="hidden" value="<?=$ed->en($jumin);?>">
<input name="SR" type="hidden" value="<?=$SR;?>">
<input name="date" type="hidden" value="<?=$date;?>">
<input name="time" type="hidden" value="<?=$time;?>">
<input name="seq" type="hidden" value="<?=$seq;?>">
<input name="no" type="hidden" value="<?=$no;?>">
</form>
<?
	include_once('../inc/_footer.php');
?>