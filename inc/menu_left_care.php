<?
	include_once('../inc/_root_return.php');

	if ($typeSR == 'S'){
		$menuTitle = '재가지원';
	}else if ($typeSR == 'R'){
		$menuTitle = '자원연계';
	}
?>
<script type="text/javascript">
	function lfCaseShow(sr){
		//사례관리
		var w = 1024;
		var h = (screen.availHeight > 768 ? screen.availHeight : 768);
		var l = (screen.availWidth - w) / 2;
		var t = (screen.availHeight - h) / 2;

		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var url = '../hce/hce.php?sr='+sr;
		var win = window.open('', 'WINCASE', option);
			win.opener = self;
			win.focus();

		var form = document.createElement('form');
			form.setAttribute('target', 'WINCASE');
			form.setAttribute('method', 'post');
			form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div id="left_box">
	<h2><?=$menuTitle;?></h2>
	<ul id="s_gnb"><?
		if ($debug && $_SESSION['userLevel'] == 'HAN'){?>
			<!--li class="top_line"><a style="cursor:default;">기관관리</a>
				<ul id="sub_menu">
					<li><a href="#" onClick="location.href='../care/care.php?type=61'; return false;">기관리스트</a></li>
				</ul>
			</li--><?
		}?>
		<li class="top_line">
			<a style="cursor:default;"><?=$menuTitle;?></a>
			<ul id="sub_menu"><?
				if ($_SESSION['userLevel'] == 'HAN'){?>
					<li><a href="#" onClick="location.href='../acct/acct.php?sr=<?=$typeSR;?>&type=71&menu=care'; return false;">서비스관리</a></li>
					<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=2&menu=care'; return false;">서비스단위관리</a></li><?
				}else if ($_SESSION['userLevel'] == 'C'){?>
					<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=1&menu=care'; return false;">서비스관리</a></li>
					<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=71&menu=care'; return false;">거래처관리</a></li>
					<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=11&menu=care'; return false;">자원관리</a></li>
					<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=21&menu=care'; return false;">사업계획(년별)</a></li><?
				}?>
			</ul>
		</li><?
		if ($_SESSION['userLevel'] == 'C'){?>
			<li class="top_line">
				<a style="cursor:default;">고객관리</a>
				<ul id="sub_menu">
					<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=81&menu=care'; return false;">고객조회</a></li>
					<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=82&menu=care'; return false;">고객등록</a></li>
				</ul>
			</li>
			<li class="top_line">
				<a style="cursor:default;">일정관리</a>
				<ul id="sub_menu">
					<li><a href="#" onClick="location.href='../iljung/iljung_list.php?sr=<?=$typeSR;?>&mode=6&menu=care'; return false;">일정관리</a></li>
					<li><a href="#" onClick="location.href='../iljung/iljung_print_new.php?sr=<?=$typeSR;?>&mode=105&menu=care'; return false;">일정표출력</a></li>
					<!--
					<li><a href="#" onClick="location.href='../iljung/iljung_list.php?sr=<?=$typeSR;?>&mode=7'; return false;">상담관리</a></li>
					<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=31'; return false;">상담일정출력</a></li>
					-->
				</ul>
			</li>
			<li class="top_line">
				<a style="cursor:default;">실적관리</a>
				<ul id="sub_menu">
					<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=41&menu=care'; return false;">일정실적관리</a></li>
					<!--li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=42'; return false;">상담실적관리</a></li-->
					<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=43&menu=care'; return false;">실적마감</a></li>
				</ul>
			</li>
			<li class="top_line">
				<a style="cursor:default;">보고서</a>
				<ul id="sub_menu"><?
					if ($_SESSION['userLevel'] == 'HAN'){?>
						<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=53&menu=care'; return false;">중분류</a></li>
						<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=52&menu=care'; return false;">세부사업</a></li><?
					}else if ($_SESSION['userLevel'] == 'C'){?>
						<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=53&menu=care'; return false;">중분류</a></li>
						<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=51&menu=care'; return false;">보고서</a></li><?
					}?>
					<!--li><a href="#" onClick="location.href='../care/care.php?type=53&area=01'; return false;">중분류(서울)</a></li>
					<li><a href="#" onClick="location.href='../care/care.php?type=53&area=15'; return false;">중분류(부산)</a></li>
					<li><a href="#" onClick="location.href='../care/care.php?type=52&area=01'; return false;">세부사업(서울)</a></li>
					<li><a href="#" onClick="location.href='../care/care.php?type=52&area=15'; return false;">세부사업(부산)</a></li-->
				</ul>
			</li>
			<li class="top_line">
				<a style="cursor:default;">사례관리</a>
				<ul id="sub_menu">
					<li><a href="#" onClick="lfCaseShow('<?=$typeSR;?>'); return false;">사례관리</a></li>
					<!--li><a href="#" onClick="location.href='../hce/hce.php?type=1'; return false;">사례접수일지</a></li>
					<li><a href="#" onClick="location.href='../hce/hce.php?type=11'; return false;">사례접수등록</a></li>
					<li><a href="#" onClick="location.href='../hce/hce.php?type=21'; return false;">초기면접기록지</a></li-->
				</ul>
			</li><?
		}?>
	</ul>
</div>