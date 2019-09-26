<?
	if ($debug){
		//미납금액
		if ($_SESSION['UNPAID_AMT'] > 0){
			$tmpY = SubStr($_SESSION['UNPAID_YM'],0,4);
			$tmpM = IntVal(SubStr($_SESSION['UNPAID_YM'],4));?>
			<div style="margin-top:1px; color:WHITE; background-color:#CC3D3D;">&nbsp;
				<span id="ID_CELL_UNPAID_MSG" style="font-weight:bold;">※ <?=$tmpY;?>년 <?=$tmpM;?>월 <?=number_format($_SESSION['UNPAID_AMT']);?>원이 미납되어있습니다.</span>
				<span>[<a href="../center/bill_non.php" style="color:#002266;">미납내역조회</a>]</span>
			</div>
			<script type="text/javascript">
				function lfUnpaidMsgSet(){
					if ($('#ID_CELL_UNPAID_MSG').css('display') == 'none'){
						$('#ID_CELL_UNPAID_MSG').show();
						setTimeout('lfUnpaidMsgSet()',2000);
					}else{
						$('#ID_CELL_UNPAID_MSG').hide();
						setTimeout('lfUnpaidMsgSet()',300);
					}
				}

				//setTimeout('lfUnpaidMsgSet()',2000);
			</script><?
		}
	}
?>