<? include "../inc/config.php" ?>
<?
	setSession("RETURN_URL", NOW_URL);

	$params['webtoon_idx']    = chkReqRpl("wt", null, "", "", "INT");
	$params['round_idx']      = chkReqRpl("ep", null, "", "", "INT");
	$params['translator_id']  = chkReqRpl("tid", "", 100, "", "STR");
	$params['translator_idx'] = chkReqRpl("tidx", null, "", "", "INT");

	//번역자 정보로 접근시
	if (chkBlank($params['translator_id']) || chkBlank($params['translator_idx'])) fnMsgGo(500, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "BACK", "");

	$cls_wt = new CLS_WEBTOON;

    //웹툰 상세정보 불러오기
	$wt_view = $cls_wt->wt_view($params['webtoon_idx'], SITE_SAVE_LANG, 'Y');
	if ($wt_view == false) fnMsgGo(501, getTransLangMsg("일치하는 웹툰 데이터가 없습니다."), "/", "");

    //웹툰 회차 목록 불러오기
    $round_list = $cls_wt->round_list($wt_view['idx'], SITE_SAVE_LANG);

    //웹툰 회차 상세정보 불러오기
	$round_view = $cls_wt->webtoon_translator_round_view(decryption($params['translator_id']), $params['webtoon_idx'], $params['round_idx'], $params['translator_idx']);
	if ($round_view == false) fnMsgGo(502, getTransLangMsg("번역이 안된 회차는 이용이 불가능합니다."), "/", "");

	//웹툰 번역자 번역회차 목록 불러오기
	$translator_round_list = $cls_wt->webtoon_translator_round_list($wt_view['idx'], SITE_SAVE_LANG, decryption($params['translator_id']));

	//기다리면 무료제외 회차 강제유료 전환 설정
	if ($wt_view['billing_type'] == '20') {
		rsort($round_list);
		for ($i=0; $i<count($round_list); $i++) {
			if ($i < $wt_view['wait_free_exception']) {
				$round_list[$i]['free_status']    = '10';
				$round_list[$i]['free_exception'] = 'Y';

				//회차 상세보기 강제유료 전환
				if ($round_list[$i]['idx'] == $round_view['idx']) {
					$round_view['free_status']    = '10';
					$round_view['free_exception'] = 'Y';
				}
			} else {
				break;
			}
		}
		sort($round_list);
	}

    //번역 블럭정보 불러오기
    $block_data = $round_view['block_data'];

	//웹툰 회차 이미지 불러오기
	$image_list = $cls_wt->round_images_list($round_view['idx']);

	//이전화, 다음화 체크
	$prev_idx = null;
	$next_idx = null;

	if (isUser()) {
		//이용권 사용여부 체크
		$used_free_ticket = $cls_wt->freeticket_used_check($MEM_USR['usr_idx'], $wt_view['idx'], $wt_view['wait_free_time'], decryption($params['translator_id']));

		//웹툰 구매내역 목록 불러오기
		$purchase_history_list = $cls_wt->purchase_history_list($MEM_USR['usr_idx'], $wt_view['idx'], decryption($params['translator_id']));

		$is_free_ticket = false; 	//이용권 체크
		$is_adview      = false; 	//광고뷰 체크
		$is_rental      = false; 	//대여중 체크
		$is_purchase    = false; 	//구매 체크

		for ($k=0; $k<count($purchase_history_list); $k++) {
			if ($purchase_history_list[$k]['round_idx'] == $round_view['idx']) {
				//이용권, 광고뷰, 대여중, 구매 확인
				if ($purchase_history_list[$k]['types']=='F') {
					$is_free_ticket = true;
				} else if ($purchase_history_list[$k]['types']=='A') {
					$is_adview = true;
				} else if ($purchase_history_list[$k]['types']=='P' || $purchase_history_list[$k]['types']=='R') {
					$is_rental = true;
				} else if ($purchase_history_list[$k]['types']=='G') {
					$is_purchase = true;
				}

				break;
			}
		}

		//웹툰 회차 접속가능 체크
		if ($is_free_ticket || $is_adview || $is_rental || $is_purchase) {
			//웹툰 회차 구매내역 확인 | 접속가능
		} else {
			if ($round_view['free_status'] == '10') {
				if ($wt_view['billing_type']=='20' && $round_view['free_exception']!='Y' && $used_free_ticket==false) {
					//이용권 사용 자동저장 | 접속가능
					$params_freeticket['usr_idx']        = $MEM_USR['usr_idx'];
					$params_freeticket['webtoon_idx']    = $wt_view['idx'];
					$params_freeticket['wait_free_time'] = $wt_view['wait_free_time'];
					$params_freeticket['round_idx']      = $round_view['idx'];
					$params_freeticket['title']          = $wt_view['title']. ' | '. $round_view['title'];
					$params_freeticket['trans_usr_idx']  = decryption($params['translator_id']);
					$params_freeticket['trans_idx']      = $params['translator_idx'];
					$params_freeticket['reg_ip']         = NOW_IP;
					$params_freeticket['reg_id']         = $MEM_USR['usr_idx'];
					if (!$cls_wt->freeticket_save($params_freeticket, $error_msg)) fnMsgGo(503, $error_msg, "/view/view.php?wt=". $wt_view['idx'], "");
				} else {
					//웹툰 회차 구매 후 이용가능 | 접속불가
					fnMsgGo(504, getTransLangMsg("이번 회차는 코인이 필요합니다."), "/view/view.php?wt=". $wt_view['idx'], "");
				}
			} else if ($round_view['free_status'] == '20') {
				//웹툰 회차 무료 확인 | 접속가능
			} else if ($round_view['free_status'] == '30') {
				//웹툰 회차 광고뷰 이용 후 이용가능 | 접속불가
				fnMsgGo(505, getTransLangMsg("이번 회차는 코인이 필요합니다."), "/view/view.php?wt=". $wt_view['idx'], "");
			}
		}
	} else {
		//무료 회차중 비공개 열람여부 체크
		if ($round_view['free_status']=='20') {
			if ($round_view['non_member_flag']!='Y') {
				fnMsgGo(506, getTransLangMsg("로그인 후 이용가능합니다."), "/member/login.php?return_flag=Y", "");
			}
		} else {
			fnMsgGo(507, getTransLangMsg("로그인 후 이용가능합니다."), "/member/login.php?return_flag=Y", "");
		}
	}

	//회차별 권한 체크 및 이전,다음 회차 정보 설정
	for ($i=0; $i<count($round_list); $i++) {
		//이전 회차 정보
		if ($round_view['sort'] > $round_list[$i]['sort']) {
			$prev_idx = $round_list[$i]['idx'];
		}

		//다음 회차 정보
		if (chkBlank($next_idx) && $round_view['sort'] < $round_list[$i]['sort']) {
			$next_idx = $round_list[$i]['idx'];
		}

		$is_free_ticket = false; 	//이용권 체크
		$is_adview      = false; 	//광고뷰 체크
		$is_rental      = false; 	//대여중 체크
		$is_purchase    = false; 	//구매 체크

		for ($k=0; $k<count($purchase_history_list); $k++) {
			if ($purchase_history_list[$k]['round_idx'] == $round_list[$i]['idx']) {
				//이용권, 광고뷰, 대여중, 구매 확인
				if ($purchase_history_list[$k]['types']=='F') {
					$is_free_ticket = true;
				} else if ($purchase_history_list[$k]['types']=='A') {
					$is_adview = true;
				} else if ($purchase_history_list[$k]['types']=='P' || $purchase_history_list[$k]['types']=='R') {
					$is_rental = true;
				} else if ($purchase_history_list[$k]['types']=='G') {
					$is_purchase = true;
				}

				//이용권, 광고뷰, 대여중 남은 시간 확인
				if ($purchase_history_list[$k]['types']!='G') {
					$round_remaining_time = abs($purchase_history_list[$k]['remaining_time']);

					if ($round_remaining_time <= 60) {
						$round_remaining_time = getTransLangMsg("1분 미만 남음");
					} else {
						$result = "";
						$day   = timeToKor($round_remaining_time, "D");
						$hour   = timeToKor($round_remaining_time, "G");
						$minute = timeToKor($round_remaining_time, "I");
						if ($day > 0) $result .= iif($day>1, "{{days}}일 ", "{{day}}일 ");
						if ($hour > 0) $result .= iif($hour>1, "{{hours}}시간 ", "{{hour}}시간 ");
						if ($day == 0 && $minute > 0) {
							$result .= iif($minute>1, "{{minutes}}분 ", "{{minute}}분 ");
						}

						$round_remaining_time = getTransLangMsg($result ."남음");
						$round_remaining_time = str_replace("{{days}}", $day, $round_remaining_time);
						$round_remaining_time = str_replace("{{day}}", $day, $round_remaining_time);
						$round_remaining_time = str_replace("{{hours}}", $hour, $round_remaining_time);
						$round_remaining_time = str_replace("{{hour}}", $hour, $round_remaining_time);
						$round_remaining_time = str_replace("{{minutes}}", $minute, $round_remaining_time);
						$round_remaining_time = str_replace("{{minute}}", $minute, $round_remaining_time);
					}

					$round_list[$i]['remaining_time'] = $round_remaining_time;
				}

				break;
			}
		}

		//번역자 웹툰 회차 설정
		if (count($translator_round_list) > 0) {
			$translator_round = array_search($round_list[$i]['idx'], array_column($translator_round_list, 'idx'));

			if ($translator_round !== false) {
				$round_list[$i]['trans_id']  = $params['translator_id'];
				$round_list[$i]['trans_idx'] = array_column($translator_round_list, 'trans_idx')[$translator_round];
				$round_list[$i]['title']     = array_column($translator_round_list, 'title')[$translator_round];
				$round_list[$i]['open_flag'] = 'Y';
			} else {
				$round_list[$i]['open_flag'] = 'N';
			}
 		} else {
			$round_list[$i]['open_flag'] = 'Y';
		}

		//웹툰 구매내역 체크 및 링크 설정
		$href    = "";
		$onclick = "";
		if ($is_free_ticket || $is_adview || $is_rental || $is_purchase) {
			$href = "/viewer/viewer_trans.php?wt=".$round_list[$i]['webtoon_idx'] ."&ep=". $round_list[$i]['idx'] ."&tid=". $round_list[$i]['trans_id'] ."&tidx=". $round_list[$i]['trans_idx'];
		} else {
			if (!isUser()) {
				if ($round_list[$i]['free_status']=='20' && $round_list[$i]['non_member_flag']=='Y') {
					$href = "/viewer/viewer_trans.php?wt=".$round_list[$i]['webtoon_idx'] ."&ep=". $round_list[$i]['idx'] ."&tid=". $round_list[$i]['trans_id'] ."&tidx=". $round_list[$i]['trans_idx'];
				} else {
					$href    = "javascript:;";
					$onclick = "onclick=\"popupNonLogin()\"";
				}
			} else {
				if ($round_list[$i]['free_status'] == '10') {
					if ($wt_view['billing_type']=='20' && $round_list[$i]['free_exception']!='Y' && $used_free_ticket==false) {
						$href = "/viewer/viewer_trans.php?wt=".$round_list[$i]['webtoon_idx'] ."&ep=". $round_list[$i]['idx'] ."&tid=". $round_list[$i]['trans_id'] ."&tidx=". $round_list[$i]['trans_idx'];
					} else {
						$href    = "javascript:;";
						$onclick = "onclick=\"purchaseGo(". $round_list[$i]['idx'] .", '". $round_list[$i]['trans_id'] ."', '". $round_list[$i]['trans_idx'] ."')\"";
					}
				} else if ($round_list[$i]['free_status'] == '20') {
					$href = "/viewer/viewer_trans.php?wt=".$round_list[$i]['webtoon_idx'] ."&ep=". $round_list[$i]['idx'] ."&tid=". $round_list[$i]['trans_id'] ."&tidx=". $round_list[$i]['trans_idx'];
				} else if ($round_list[$i]['free_status'] == '30') {
					$href    = "javascript:;";
					$onclick = "onclick=\"purchaseGo(". $round_list[$i]['idx'] .", '". $round_list[$i]['trans_id'] ."', '". $round_list[$i]['trans_idx'] ."')\"";
				}
			}
		}

		$round_list[$i]['is_free_ticket'] = $is_free_ticket;
		$round_list[$i]['is_adview']      = $is_adview;
		$round_list[$i]['is_rental']      = $is_rental;
		$round_list[$i]['is_purchase']    = $is_purchase;
		$round_list[$i]['href']           = $href;
		$round_list[$i]['onclick']        = $onclick;
	}


	//웹툰회차 조회 로그 저장
	$cls_wt->view_log_save($wt_view['idx'], $round_view['idx'], $MEM_USR['usr_idx']);

	$pageNum = "10";
	$pageSubNum = "";
?>
<? include "../inc/top.php" ?>


<link href="../css/owl.carousel.min.css" rel="stylesheet">
<script src="../js/owl.carousel.min.js"></script>


<header id="header_viewer" class="header_viewer">
	<div class="lft">
		<a href="/view/view.php?wt=<?=$wt_view['idx']?>"><div class="img"><span style="background-image: url(../images/common/img_viewer.png);"></span></div></a>
		<div class="txt"><p><span><?=$wt_view['title']?></span><span><?=$round_view['title']?></span></p></div>
	</div>
	<div class="rgh">
		<button type="button" class="bt" id="go-button"><span><?=getTransLangMsg("전체화면")?></span></button>
		<button type="button" class="bt" onclick="vshow();"><span><?=getTransLangMsg("도움말")?></span></button>
	</div>
</header><!-- //header -->


<div id="container_viewer" class="container_viewer">
	<div class="viewer_help" style="display: none;">
		<p class="tl"><span><?=getTransLangMsg("제목 &amp; 회차 표시")?></span></p>
		<p class="tr1"><span><?=getTransLangMsg("전체화면으로 보기")?></span></p>
		<p class="tr2"><span><?=getTransLangMsg("도움말 보기")?></span></p>
		<p class="bl"><span><?=getTransLangMsg("이전회차로 이동")?></span></p>
		<p class="bc"><span><?=getTransLangMsg("목록 보기")?></span></p>
		<p class="br"><span><?=getTransLangMsg("다음회차로 이동")?></span></p>

		<button type="button" class="btn_help" onclick="vhide();"><span><i></i><?=getTransLangMsg("도움말 닫기")?></span></button>
	</div>

	<div class="viewer_cont">
		<?for ($i=0; $i<count($image_list); $i++) {?>
			<div class="img"><img src="<?=filePathCheck($image_list[$i]['up_file_path'] .'/'. getUpfileName($image_list[$i]['up_file']))?>"></div>
		<?}?>
	</div>

	<div class="viewer_bottom">
		<?if (chkBlank($prev_idx)) {?>
			<button type="button" class="bt btn_vleft" onclick="alert('<?=getTransLangMsg("이전 회차가 없습니다.")?>')"><span class="i-aft i_viewer_left"><?=getTransLangMsg("이전 회차")?></span></button>
		<?} else {?>
			<?for ($i=0; $i<count($round_list); $i++) {?>
				<?if ($round_list[$i]['idx'] == $prev_idx) {?>
					<?if ($round_list[$i]['href'] == "javascript:;") {?>
						<button type="button" class="bt btn_vleft" <?=$round_list[$i]['onclick']?>><span class="i-aft i_viewer_left"><?=getTransLangMsg("이전 회차")?></span></button>
					<?} else {?>
						<button type="button" class="bt btn_vleft" onclick="location='<?=$round_list[$i]['href']?>'"><span class="i-aft i_viewer_left"><?=getTransLangMsg("이전 회차")?></span></button>
					<?}?>
				<?}?>
			<?}?>
		<?}?>
		<button type="button" class="bt btn_vmenu" onclick="location='/view/view.php?wt=<?=$wt_view['idx']?>'"><span class="i-set i_viewer_menu"><?=getTransLangMsg("메뉴")?></span></button>
		<?if (chkBlank($next_idx)) {?>
			<button type="button" class="bt btn_vright" onclick="alert('<?=getTransLangMsg("다음 회차가 없습니다.")?>')"><span class="i-aft i_viewer_right"><?=getTransLangMsg("다음 회차")?></span></button>
		<?} else {?>
			<?for ($i=0; $i<count($round_list); $i++) {?>
				<?if ($round_list[$i]['idx'] == $next_idx) {?>
					<?if ($round_list[$i]['href'] == "javascript:;") {?>
						<button type="button" class="bt btn_vright" <?=$round_list[$i]['onclick']?>><span class="i-aft i_viewer_right"><?=getTransLangMsg("다음 회차")?></span></button>
					<?} else {?>
						<button type="button" class="bt btn_vright" onclick="location='<?=$round_list[$i]['href']?>'"><span class="i-aft i_viewer_right"><?=getTransLangMsg("다음 회차")?></span></button>
					<?}?>
				<?}?>
			<?}?>
		<?}?>
	</div>

	<div class="viewer_slider">
		<div class="inr-c">
			<div class="slider">
				<div class="owl-carousel">
					<?for ($i=0; $i<count($round_list); $i++) {?>
						<div class="item"><a href="<?=$round_list[$i]['href']?>" <?=$round_list[$i]['onclick']?>>
							<div class="img"><span style="background-image: url('<?=filePathCheck('/upload/webtoon/'. $round_list[$i]['webtoon_idx'] .'/list/'. getUpfileName($round_list[$i]['up_file_1']))?>');"></span></div>
							<p><?=$round_list[$i]['title']?></p>
						</a></div>
					<?}?>
				</div>
			</div>
		</div>
	</div>

	<div class="viewer_comment"></div>
</div><!--//container -->

<!-- 닉네임 -->
<div id="popNick1" class="layerPopup pop_nick"></div>

<!-- 신고하기 -->
<div id="popReport" class="layerPopup pop_report"></div>

<!-- 구매 -->
<div id="popViewPurchase" class="layerPopup pop_view_all"></div>

<script>
	$(function(){
		//웹툰 목록
		var subSlider = $(".slider .owl-carousel");
		subSlider.owlCarousel({
			loop:false,
			margin:0,
			nav:true,
			navText : ['<span class="i-set i_arr_l1"><?=getTransLangMsg("이전")?></span>','<span class="i-set i_arr_r1"><?=getTransLangMsg("다음")?></span>'],
			dots:false,
			margin:20,
			responsive : {
				0 : {
					items:3,
				},
				961 : {
					items:4,
				},
				1200 : {
					items:9,
				}
			},
		});

		commentListGo(1,1);
	});

	//댓글목록
	function commentListGo(page, ordby) {
		$(".viewer_comment").load("comment_list.php?wt=<?=$params['webtoon_idx']?>&ep=<?=$params['round_idx']?>&page="+ page +"&ordby="+ ordby);
	}

	//웹툰 구매하기
	function purchaseGo(ep, tid, tidx) {
		<?if (!isUser()) {?>
			popupNonLogin();
		<?} else {?>
			AJ.callAjax("/popup/webtoon_purchase.php", {"wt": "<?=$params['webtoon_idx']?>", "ep": ep, "tid": tid, "tidx": tidx}, function(data){
				$("#popViewPurchase").html(data);
				openLayerPopup('popViewPurchase');
			}, "html");
		<?}?>
	}

	//도움말 보기
	function vshow(){
		$('.viewer_help').fadeIn(200);
	}

	//도움말 숨김
	function vhide(){
		$('.viewer_help').fadeOut(100);
	}

	/* 전체화면 */
	function GoInFullscreen(element) {
		if(element.requestFullscreen)
			element.requestFullscreen();
		else if(element.mozRequestFullScreen)
			element.mozRequestFullScreen();
		else if(element.webkitRequestFullscreen)
			element.webkitRequestFullscreen();
		else if(element.msRequestFullscreen)
			element.msRequestFullscreen();
	}

	function GoOutFullscreen() {
		if(document.exitFullscreen)
			document.exitFullscreen();
		else if(document.mozCancelFullScreen)
			document.mozCancelFullScreen();
		else if(document.webkitExitFullscreen)
			document.webkitExitFullscreen();
		else if(document.msExitFullscreen)
			document.msExitFullscreen();
	}

	function IsFullScreenCurrently() {
		var full_screen_element = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement || null;

		if(full_screen_element === null)
			return false;
		else
			return true;
	}

	$("#go-button").on('click', function() {
		if(IsFullScreenCurrently()){
			GoOutFullscreen();
		} else {
			GoInFullscreen(document.documentElement);
		}
	});

	$(document).on('fullscreenchange webkitfullscreenchange mozfullscreenchange MSFullscreenChange', function() {
		if(IsFullScreenCurrently()) {
			$("#go-button").text('<?=getTransLangMsg("전체화면 닫기")?>');
		} else {
			$("#go-button").text('<?=getTransLangMsg("전체화면")?>');
		}
	});
</script>

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>