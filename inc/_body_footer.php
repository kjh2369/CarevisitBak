<?
		if($mainYn != 'Y'){ ?>
				</div>
				<!--//content-->
		<? } ?>
			</div>
			<!--//container_box-->
		</div>
		<!--//container-->
	</div>
	<!--footer-->
	<div id="footer">
		<div id="main_copy"><?
			if ($gDomain == 'forweak.net'){?>
				<div>COPYRIGHT(C) 2014 FORWEAK CO. LTD. ALL RIGHTS RESERVED<br>TEL : 02-6479-40012&nbsp;&nbsp;&nbsp;&nbsp;FAX : 02-6455-4701</div><?
			}else{?>
				<div><span>TEL : <?=($gDomain == 'kacold.net' ? '070-8224-6760' : '02-6952-9253');?>&nbsp;&nbsp;&nbsp;FAX : 070-4850-8177</span><?
				if ($gHostNm != 'adm'){?>
					<br>COPYRIGHT(C) 2019 CAREVISIT CO. LTD. ALL RIGHTS RESERVED<?
				}?>
				</div><?
			}?>
		</div>
	</div>
	<!--//footer-->
</div>
<!--//wrap-->
<div id='divLongcareBody' onclick='__longcareHide();' style='position:absolute; left:0; top:0; display:none; z-index:10; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:#ffffff;'></div>
<div id='divLongcareCont' style='position:absolute; left:0; top:0; display:none; z-index:11; width:200; padding:20px; border:2px solid #cccccc; background-color:#ffffff;'></div>
<div id='divPopupBody' onclick='__popupHide();' style='position:absolute; left:0; top:0; display:none; z-index:10; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:#ffffff;'></div>
<div id='divPopupLayer' style='position:absolute; left:0; top:0; display:none; z-index:11; width:200; padding:20px; border:2px solid #cccccc; background-color:#ffffff;' onmousedown='__move_on_off(this, 1, window.event);' onmouseup='on_off=0;coord_x=0;coord_y=0;' onmousemove='__move(tblMain, this, window.event)'></div>
<div id='divTemp' style='position:absolute; left:0; top:0; display:none; z-index:11;'></div>
<?
	//echo hash('md5', '7710102145414');
	if ($_SESSION['userLevel'] != 'A' && $gDomain == 'kacold.net'){
		//경제협 계약서 및 사업자등록자을 받기 위한 확인작업
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

		$contFile = lfCheckFile('../popup/kacold_popup/contract', $_SESSION['userCenterCode']);
		$bizFile = lfCheckFile('../popup/kacold_popup/registration', $_SESSION['userCenterCode']);

		if (!$contFile || !$bizFile){
			//유지보수 안내 팝업?>
			<script type="text/javascript">
				var left = 50;
				var top = 50;
				//var w = window.open("../popup/kacold_popup/index.php","KACOLD_POP","width=600,height=700,left="+left+",top="+top+",scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no");
				//	w.focus();
			</script><?
		}
	}
?>