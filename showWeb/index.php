<?
	include_once("../inc/_header.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$orgNo	= $_SESSION["userCenterCode"];
	$orgNm	= $_SESSION["userCenterName"];
	$path	= $_REQUEST['path'];
	$dir	= $_REQUEST['dir'];

	$sql = 'SELECT	m00_ctel
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'';

	$orgTel = $conn->get_data($sql);

	/*
		<div id="ID_DIV_TMP" style="width:100%; height:1000px; text-align:center; font-weight:bold; padding-top:40px;">잠시 기다려 주십시오.</div>
		<iframe name="frmWebPrt" src="<?=$path;?>?para=<?=$_GET['data'];?>"></iframe>
	 */
?>
<style>
	.bt2b{border-top:1pt solid BLACK;}
	.br2b{border-right:1pt solid BLACK;}
	.bb2b{border-bottom:1pt solid BLACK;}
	.bl2b{border-left:1pt solid BLACK;}

	.bt1b{border-top:0.5pt solid BLACK;}
	.br1b{border-right:0.5pt solid BLACK;}
	.bb1b{border-bottom:0.5pt solid BLACK;}
	.bl1b{border-left:0.5pt solid BLACK;}

	.bold{font-weight:bold;}

	td{border:none;}
</style><?
if (is_file($path)) include_once($path);?>
<object id="webPrt" viewastext style="display:none"
	classid="clsid:1663ed61-23eb-11D2-b92f-008048fdd814"
	codebase="../activex/smsx.cab#Version=7,5,0,20">
</object>
<script type="text/javascript">
	$(document).ready(function(){
		webPrt.printing.header = ""; //머릿말 설정
		//webPrt.printing.footer = "<?=$orgNm;?>(<?=$orgTel;?>)&b &p of &P"; //꼬릿말 설정
		webPrt.printing.footer = "&b<?=$orgNm;?>(<?=$orgTel;?>)&b"; //꼬릿말 설정
		webPrt.printing.portrait = '<?=$dir;?>' == 'Y' ? false : true; //출력방향 설정: true-세로, false-가로
		webPrt.printing.leftMargin = 19.0; //왼쪽 여백 설정
		webPrt.printing.topMargin = 15.0; //위쪽 여백 설정
		webPrt.printing.rightMargin = 19.0; //오른쪽 여백 설정
		webPrt.printing.bottomMargin = 15.0; //아래쪽 여백 설정
		// webPrt.printing.printBackground = true; //배경이미지 출력 설정:라이센스 필요
		//webPrt.printing.Print(false); //출력하기
		//webPrt.printing.Print(true);

		//lfPrinting();
		lfPriview();

		// <p style="page-break-before:always"> -- > 다음페이지에 인쇄
	});

	function lfPrinting(){
		webPrt.printing.Print(true,document.all.frmWebPrt);
		self.close();
	}

	function lfPriview(){
		webPrt.printing.Preview();
		self.close();
	}
</script>
<?
	include_once("../inc/_footer.php");
?>