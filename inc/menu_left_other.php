<?
	include_once('../inc/_root_return.php');

	$sql = 'select count(*)
			  from msg_receipt
			 where org_no        = \''.$_SESSION['userCenterCode'].'\'
			   and msg_mem       = \''.$_SESSION['userNo'].'\'
			   and msg_open_flag = \'N\'
			   and msg_del_flag  = \'N\'';

	$new_msg_cnt = $conn->get_data($sql);

	if (empty($new_msg_cnt)) $new_msg_cnt = 0;
?>
<div id="left_box">
	<h2>커뮤니티</h2><?
	if ($_SESSION['userLevel'] == 'C'){?>
		<ul id="s_gnb">
			<li class="top_line"><a style="cursor:default;">기관커뮤니티</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../goodeos/board_list.php?board_type=noti';">공지사항</a></li>
					<li><a href="#" onclick="location.href='../goodeos/board_list.php?board_type=free';">자유게시판</a></li>
				</ul>
			</li>
		</ul>

		<ul id="s_gnb">
			<li class="top_line"><a style="cursor:default;">쪽지함</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../note/note_send.php';">쪽지보내기</a></li>
					<li><a href="#" onclick="location.href='../note/note_list.php?mode=to';">받은쪽지함(<?=$new_msg_cnt;?>)</a></li>
					<li><a href="#" onclick="location.href='../note/note_list.php?mode=from';">보낸쪽지함</a></li>
				</ul>
			</li>
		</ul>

		<ul id="s_gnb">
			<li class="top_line"><a style="cursor:default;">스케줄</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../calendar/';">스케줄</a></li>
				</ul>
			</li>
		</ul>
		<ul id="s_gnb">
		<li class="top_line"><a style="cursor:default;">노무&재무소식</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../news/board_list.php?board_type=L';">인사노무소식</a></li>
				<!--li><a href="#" onclick="location.href='../news/board_list.php?board_type=A';">재무회계소식</a></li-->
			</ul>
		</li>
		</ul>
		<ul id="s_gnb">
			<li class="top_line"><a style="cursor:default;">질의응답</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../goodeos/board_list.php?board_type=1';">케이비지트</a></li>
					<!--li><a href="#" onclick="location.href='../goodeos/board_list.php?board_type=2';">세무회계</a></li-->
					<li><a href="#" onclick="location.href='../goodeos/board_list.php?board_type=3';">노무자문</a></li>
					<li><a href="#" onclick="location.href='../goodeos/board_list.php?board_type=4';">법률자문</a></li>
				</ul>
			</li>
		</ul>

		<ul id="s_gnb">
			<li class="top_line"><a style="cursor:default;">본사공지사항</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../goodeos/notice_list.php';"><?=$_SESSION["userLevel"] == 'A' ? '공지사항관리' : '공지사항';?></a></li>
					<li><a href="#" onclick="location.href='../other/notice.php?gubun=list';">스마트폰공지</a></li>
				</ul>
			</li>
		</ul><?
	}else{?>
		<ul id="s_gnb">
			<li class="top_line"><a style="cursor:default;">기관커뮤니티</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../goodeos/board_list.php?board_type=noti';">공지사항</a></li>
					<li><a href="#" onclick="location.href='../goodeos/board_list.php?board_type=free';">자유게시판</a></li>
				</ul>
			</li>
		</ul>

		<ul id="s_gnb">
			<li class="top_line"><a style="cursor:default;">쪽지함</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../note/note_send.php';">쪽지보내기</a></li>
					<li><a href="#" onclick="location.href='../note/note_list.php?mode=to';">받은쪽지함(<?=$new_msg_cnt;?>)</a></li>
					<li><a href="#" onclick="location.href='../note/note_list.php?mode=from';">보낸쪽지함</a></li>
				</ul>
			</li>
		</ul>

		<ul id="s_gnb">
			<li class="top_line"><a style="cursor:default;">스케줄</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../calendar/';">스케줄</a></li>
				</ul>
			</li>
		</ul>

		<ul id="s_gnb">
			<li><a style="cursor:default;">본사공지사항</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../goodeos/notice_list.php';"><?=$_SESSION["userLevel"] == 'A' ? '공지사항관리' : '공지사항';?></a></li>
				</ul>
			</li>
		</ul><?
	}

	if ($_SESSION['userLevel'] == 'C'){
		//기관만 SMS 사용한다.?>
		<ul id="s_gnb">
			<li class="top_line"><a style="cursor:default;">SMS(문자서비스)</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../sms/sms_send.php?mode=NORMAL';">SMS보내기</a></li>
					<li><a href="#" onclick="location.href='../sms/sms_send.php?mode=CLIENT';">본인부담금(고객)</a></li>
					<li><a href="#" onclick="location.href='../sms/sms_send.php?mode=MEMBER';">급여내역(직원)</a></li>
					<?
						//SMS 사용가능한 기관을 환익한다.
						$sql = 'SELECT COUNT(*)
								  FROM sms_acct
								 WHERE org_no = \''.$_SESSION['userCenterCode'].'\'';
						$liSMSCnt = $conn->get_data($sql);
						if ($liSMSCnt > 0){?>
							<li><a href="#" onclick="location.href='../sms/sms_send_list.php';">전송결과리스트</a></li>
							<li><a href="#" onclick="location.href='../sms/sms_send_acct.php';">SMS 요금내역</a></li><?
						}
					?>
				</ul>
			</li>
		</ul><?
	}
	if ($_SESSION['userLevel'] != 'A'){
		switch($gDomain){
			case 'carevisit.net':
				$lsNm = '케어비지트 ';
				break;

			case 'dwcare.com':
				$lsNm = '도우누리 ';
				break;

			case 'klcf.kr':
				$lsNm = '정보나눔협회 ';
				break;
		}
	}
	?>
	<ul id="s_gnb">
		<li class="top_line"><a style="cursor:default;"><?=$lsNm;?>공지사항</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../goodeos/notice_list.php';"><?=$_SESSION["userLevel"] == 'A' ? '공지사항관리' : '공지사항';?></a></li><?
				if ($_SESSION['userLevel'] == 'C'){?>
					<li><a href="#" onclick="location.href='../other/notice.php?gubun=list';">스마트폰공지</a></li><?
				}?>
			</ul>
		</li>
	</ul>
</div>