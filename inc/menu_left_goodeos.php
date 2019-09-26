<?
	include_once('../inc/_root_return.php');

	$sql = 'select count(*)
			  from msg_receipt
			 where org_no   = \''.$_SESSION['userCenterCode'].'\'
			   and msg_mem  = \''.$_SESSION['userCode'].'\'
			   and msg_open = \'N\'';

	$new_msg_cnt = $conn->get_data($sql);

	if (empty($new_msg_cnt)) $new_msg_cnt = 0;
?>
<div id="left_box">
	<h2>커뮤니티</h2>
	<ul id="s_gnb">
		<li class="<?=$debug ? 'top_line' : '';?>"><a style="cursor:default;"><?=$gDomain == 'vaerp.com' ? 'VA공지사항관리' : '공지사항관리';?></a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../goodeos/notice_list.php?menu=goodeos';"><?=$_SESSION["userLevel"] == 'A' ? '공지사항관리' : '공지사항';?></a></li>
				<li><a href="#" onclick="location.href='../goodeos/board_list.php?board_type=1&menu=goodeos';"><?=$gDomain == 'vaerp.com' ? 'VA문의' : '케이비지트';?></a></li>
			</ul>
		</li>

		<ul id="s_gnb">
			<li class="top_line"><a style="cursor:default;">쪽지함</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../note/note_send.php?menu=goodeos';">쪽지보내기</a></li>
					<li><a href="#" onclick="location.href='../note/note_list.php?mode=to&menu=goodeos';">받은쪽지함(<?=$new_msg_cnt;?>)</a></li>
					<li><a href="#" onclick="location.href='../note/note_list.php?mode=from&menu=goodeos';">보낸쪽지함</a></li>
				</ul>
			</li>
		</ul>

		<ul id="s_gnb">
			<li class="top_line"><a style="cursor:default;">스케줄</a>
				<ul id="sub_menu"><?
					if ($gDomain == 'carevisit.net'){?>
						<li><a href="#" onclick="location.href='../calendar/?menu=goodeos';">스케줄 공통</a></li>
						<li><a href="#" onclick="location.href='../calendar/?menu=goodeos&ownerId=GE_ADMIN_1';">스케줄 사장님</a></li>
						<li><a href="#" onclick="location.href='../calendar/?menu=goodeos&ownerId=GE_ADMIN_2';">스케줄 김종성</a></li>
						<li><a href="#" onclick="location.href='../calendar/?menu=goodeos&ownerId=GE_ADMIN_3';">스케줄 김재용</a></li>
						<li><a href="#" onclick="location.href='../calendar/?menu=goodeos&ownerId=GE_ADMIN_4';">스케줄 안상현</a></li>
						<li><a href="#" onclick="location.href='../calendar/?menu=goodeos&ownerId=GE_ADMIN_5';">스케줄 김주완</a></li><?
					}else{?>
						<li><a href="#" onclick="location.href='../calendar/?menu=goodeos';">스케줄</a></li><?
					}?>
				</ul>
			</li>
		</ul>

		<?
			if ($_SESSION['userLevel'] == 'A'){?>
				<ul id="s_gnb">
					<li class="top_line"><a style="cursor:default;">노무&재무소식</a>
						<ul id="sub_menu">
							<li><a href="#" onclick="location.href='../news/board_list.php?board_type=L&menu=goodeos';"><?=$gDomain == 'vaerp.com' ? '노무,세무소식' : '인사노무소식';?></a></li>
							<li><a href="#" onclick="location.href='../news/board_list.php?board_type=A&menu=goodeos';">재무회계소식</a></li>
						</ul>
					</li>
				</ul>
				<li class="top_line"><a style="cursor:default;"><?=$gDomain == 'vaerp.com' ? 'VA커뮤니티' : '커뮤니티';?></a>
					<ul id="sub_menu">
						<li><a href="#" onclick="location.href='../goodeos/board_list.php?board_type=2&menu=goodeos';">세무회계</a></li>
						<li><a href="#" onclick="location.href='../goodeos/board_list.php?board_type=3&menu=goodeos';">노무자문</a></li>
						<li><a href="#" onclick="location.href='../goodeos/board_list.php?board_type=4&menu=goodeos';"><?=$gDomain == 'vaerp.com' ? '세무자문' : '법률자문';?></a></li>
					</ul>
				</li>

				<li class="top_line"><a style="cursor:default;">FAQ</a>
					<ul id="sub_menu">
						<li><a href="#" onclick="location.href='../faq/faq.php?mode=101&menu=goodeos';">FAQ</a></li>
					</ul>
				</li>

				<li class="top_line"><a style="cursor:default;">비회원문의</a>
					<ul id="sub_menu">
						<li><a href="#" onclick="location.href='../goodeos/visit_quest_list.php?menu=goodeos';">비회원문의</a></li>
					</ul>
				</li>
				<li class="top_line"><a style="cursor:default;">자료실</a>
					<ul id="sub_menu">
						<li><a href="#" onclick="location.href='../board/category.php?mode=data&menu=goodeos';">항목관리</a></li>
						<li><a href="#" onclick="location.href='../board/board.php?mode=data&menu=goodeos';">자료올리기</a></li>
					</ul>
				</li><?
			}else{?>
				<li class="top_line"><a style="cursor:default;">커뮤니티</a>
					<ul id="sub_menu">
						<li><a href="#" onclick="location.href='../goodeos/board_list.php?board_type=noti&menu=goodeos';">공지사항</a></li>
						<li><a href="#" onclick="location.href='../goodeos/board_list.php?board_type=free&menu=goodeos';">자유게시판</a></li>
					</ul>
				</li><?
			}
		?>
	</ul>
</div>