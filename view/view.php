<? include "../inc/config.php" ?>
<?
	setSession("RETURN_URL", NOW_URL);

	$params['webtoon_idx']   = chkReqRpl("wt", null, "", "", "INT");
	$params['translator_id'] = chkReqRpl("tid", "", 100, "", "STR");

	$cls_wt = new CLS_WEBTOON;
	$cls_set_code = new CLS_SETTING_CODE;

    //웹툰 상세정보 불러오기
	$wt_view = $cls_wt->wt_view($params['webtoon_idx'], SITE_SAVE_LANG, 'Y');
	if ($wt_view == false) fnMsgGo(500, getTransLangMsg("일치하는 웹툰 데이터가 없습니다."), "/", "");
	$wt_view['genre'] = $cls_set_code->code_lang_view($wt_view['genre'],SITE_SAVE_LANG)['code_name'];

    //웹툰 회차 목록 불러오기
    $round_list = $cls_wt->round_list($wt_view['idx'], SITE_SAVE_LANG);

	//웹툰 번역자 목록 불러오기
	$translator_list = $cls_wt->webtoon_translator_list($wt_view['idx'], SITE_SAVE_LANG);

	//웹툰 번역자 번역회차 목록 불러오기
	if (!chkBlank($params['translator_id'])) {
		$translator_round_list = $cls_wt->webtoon_translator_round_list($wt_view['idx'], SITE_SAVE_LANG, decryption($params['translator_id']));
	}

	//기다리면 무료 설정
	if ($wt_view['billing_type'] == '20') {
		//기다리면 무료시간 설정
		if ($wt_view['wait_free_time'] == 12) {
			$wt_view['wait_free_time_txt'] = $wt_view['wait_free_time'] .'시간';
		} else {
			$wt_view['wait_free_time_txt'] = $wt_view['wait_free_time']/24 .'일';
		}

		//기다리면 무료 회차 범위 설정
		$wt_view['wait_free_start'] = "";
		$wt_view['wait_free_end']   = "";
		sort($round_list);
		for ($i=0; $i<count($round_list) - $wt_view['wait_free_exception']; $i++) {
			if ($round_list[$i]['free_status'] == '10') {
				if ($wt_view['wait_free_start'] != $wt_view['wait_free_end']) {
					$wt_view['wait_free_end'] = $round_list[$i]['sort'];
				}

				if (chkBlank($wt_view['wait_free_start'])) {
					$wt_view['wait_free_start'] = $round_list[$i]['sort'];
				}
			}
		}

		//기다리면 무료제외 회차 강제유료 전환 설정
		rsort($round_list);
		for ($i=0; $i<count($round_list); $i++) {
			if ($i < $wt_view['wait_free_exception']) {
				$round_list[$i]['free_status']    = '10';
				$round_list[$i]['free_exception'] = 'Y';
			} else {
				break;
			}
		}
		sort($round_list);
	}

	//작가 목록 불러오기
	$writer_list = $cls_wt->wt_writer_list($wt_view['idx'], 'Y', SITE_SAVE_LANG);

	//키워드 목록 불러오기
	$keyword_list = $cls_wt->wt_keyword_list($wt_view['idx'], 'Y', SITE_SAVE_LANG);

	//웹툰 공지사항 불러오기
	$notice_list = $cls_wt->notice_list($wt_view['idx'], SITE_SAVE_LANG);

	//로그인 회원일경우 각 정보 불러오기
	if (isUser()) {
		//이용권 사용여부 체크
		$used_free_ticket = $cls_wt->freeticket_used_check($MEM_USR['usr_idx'], $wt_view['idx'], $wt_view['wait_free_time'], $free_ticket_remaining_time, decryption($params['translator_id']));

		//웹툰 구매내역 목록 불러오기
		$purchase_history_list = $cls_wt->purchase_history_list($MEM_USR['usr_idx'], $wt_view['idx'], decryption($params['translator_id']));

		//좋아요 체크
		$is_favorit = $cls_wt->favorit_is_check($wt_view['idx'], $MEM_USR['usr_idx']);

		//공유하기 체크
		$is_facebook = $cls_wt->sns_share_is_check($wt_view['idx'], $MEM_USR['usr_idx'], 'facebook');
		$is_twitter = $cls_wt->sns_share_is_check($wt_view['idx'], $MEM_USR['usr_idx'], 'twitter');
	}

	$wait_free_cnt = 0;
	for ($i=0; $i<count($round_list); $i++) {
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
			if (chkBlank($round_list[$i]['trans_idx'])) {
				$href = "/viewer/viewer.php?wt=".$round_list[$i]['webtoon_idx'] ."&ep=". $round_list[$i]['idx'];
			} else {
				$href = "/viewer/viewer_trans.php?wt=".$round_list[$i]['webtoon_idx'] ."&ep=". $round_list[$i]['idx'] ."&tid=". $round_list[$i]['trans_id'] ."&tidx=". $round_list[$i]['trans_idx'];
			}
		} else {
			if (!isUser()) {
				if ($round_list[$i]['free_status']=='20' && $round_list[$i]['non_member_flag']=='Y') {
					if (chkBlank($round_list[$i]['trans_idx'])) {
						$href = "/viewer/viewer.php?wt=".$round_list[$i]['webtoon_idx'] ."&ep=". $round_list[$i]['idx'];
					} else {
						$href = "/viewer/viewer_trans.php?wt=".$round_list[$i]['webtoon_idx'] ."&ep=". $round_list[$i]['idx'] ."&tid=". $round_list[$i]['trans_id'] ."&tidx=". $round_list[$i]['trans_idx'];
					}
				} else {
					$href    = "javascript:;";
					$onclick = "onclick=\"popupNonLogin()\"";
				}
			} else {
				if ($round_list[$i]['free_status'] == '10') {
					if ($wt_view['billing_type']=='20' && $round_list[$i]['free_exception']!='Y' && $used_free_ticket==false) {
						if (chkBlank($round_list[$i]['trans_idx'])) {
							$href = "/viewer/viewer.php?wt=".$round_list[$i]['webtoon_idx'] ."&ep=". $round_list[$i]['idx'];
						} else {
							$href = "/viewer/viewer_trans.php?wt=".$round_list[$i]['webtoon_idx'] ."&ep=". $round_list[$i]['idx'] ."&tid=". $round_list[$i]['trans_id'] ."&tidx=". $round_list[$i]['trans_idx'];
						}

						$wait_free_cnt++;
					} else {
						$href    = "javascript:;";
						$onclick = "onclick=\"purchaseGo(". $round_list[$i]['idx'] .", '". $round_list[$i]['trans_id'] ."', '". $round_list[$i]['trans_idx'] ."')\"";
					}
				} else if ($round_list[$i]['free_status'] == '20') {
					if (chkBlank($round_list[$i]['trans_idx'])) {
						$href = "/viewer/viewer.php?wt=".$round_list[$i]['webtoon_idx'] ."&ep=". $round_list[$i]['idx'];
					} else {
						$href = "/viewer/viewer_trans.php?wt=".$round_list[$i]['webtoon_idx'] ."&ep=". $round_list[$i]['idx'] ."&tid=". $round_list[$i]['trans_id'] ."&tidx=". $round_list[$i]['trans_idx'];
					}
				} else if ($round_list[$i]['free_status'] == '30') {
					$href    = "javascript:;";
					$onclick = "onclick=\"purchaseGo(". $round_list[$i]['idx'] .", '". $round_list[$i]['trans_id'] ."', '". $round_list[$i]['trans_idx'] ."')\"";
				}
			}
		}

		if($round_list[$i]['open_flag'] == 'N') {
			$href    = "javascript:;";
			$onclick = "onclick=\"notAvailableGo('". getTransLangMsg("번역이 안된 회차는 이용이 불가능합니다.") ."')\"";
		}

		$round_list[$i]['is_free_ticket'] = $is_free_ticket;
		$round_list[$i]['is_adview']      = $is_adview;
		$round_list[$i]['is_rental']      = $is_rental;
		$round_list[$i]['is_purchase']    = $is_purchase;
		$round_list[$i]['href']           = $href;
		$round_list[$i]['onclick']        = $onclick;
	}

	//웹툰 조회 로그 저장
	$cls_wt->view_log_save($wt_view['idx'], null, $MEM_USR['usr_idx']);

	$pageNum = "0";
	$pageSubNum = "0";
?>
<? include "../inc/top.php" ?>
<? include "../inc/header.php" ?>

<div id="container" class="container sub view">
	<div class="area_cview">
		<div class="top">
			<div class="inr-c">
				<div class="img" style="background-color: #121420;">
					<span class="hide-m"><img src="<?=filePathCheck('/upload/webtoon/'. $wt_view['idx'] .'/detail/'. getUpfileName($wt_view['up_file_2']))?>"></span>
					<span class="view-m"><img src="<?=filePathCheck('/upload/webtoon/'. $wt_view['idx'] .'/detail/'. getUpfileName($wt_view['up_file_3']))?>"></span>
				</div>
				<div class="txt">
					<p class="h1"><?=$wt_view['genre']?></p>
					<p class="h2"><?=$wt_view['title']?></p>
					<p class="t1 t_line">
						<?for ($i=0; $i<count($writer_list); $i++) {?>
							<span><a href="javascript:;" onclick="webtoonWriterView(<?=$writer_list[$i]['writer_idx']?>)"><?=$writer_list[$i]['nick_name']?>(<?=getCpWriterPartName(explode(",", $writer_list[$i]['part']))?>)</a></span>
						<?}?>
					</p>
				</div>
			</div>
		</div>
		<div class="cont">
			<div class="inr-c">
				<div class="info">
					<div class="h">
						<p class="t1"><span class="i-aft i_view1"><?=formatNumbers($wt_view['total_subscription_cnt'])?></span></p>
						<p class="t1">
							<button type="button" onclick="webtoonRatingGo()"><span class="i-aft i_favorit1"><?=round($wt_view['total_rating'],1)?></span></button>
							<button type="button" class="btn-pk ss bdrs <?=iif($is_favorit, "on", "")?>" onclick="favoritGo()"><span class="i-aft i_favorit2"><?=getTransLangMsg("좋아요")?></span></button>
						</p>
						<div class="r">
							<button type="button" class="btn <?=iif($is_facebook, "on", "")?>" onclick="snsShareGo('facebook');"><span class="i-set i_facebook1"></span></button>
							<button type="button" class="btn <?=iif($is_twitter, "on", "")?>" onclick="snsShareGo('twitter');"><span class="i-set i_twitter1"></span></button>
						</div>
					</div>

					<div class="t">
						<p class="t1"><?=textareaDecode($wt_view['introduce'])?></p>
						<p class="t1 t_hash c-pink">
							<?
								for ($i=0; $i<count($keyword_list); $i++) {
									echo "<span>#". $keyword_list[$i]['code_name'] ."</span>";
								}
							?>
						</p>
					</div>

					<?if (count($round_list) > 0) {?>
						<div class="btn-bot">
							<button type="button" class="btn-pk nb red rv bdrs" onclick="purchaseGo('', '<?=$params['translator_id']?>', '')"><span><?=getTransLangMsg("전체구매")?></span></button>

							<?if ($round_list[0]['href'] == 'javascript:;') {?>
								<button type="button" class="btn-pk nb red bdrs" <?=$round_list[0]['onclick']?>><span><?=getTransLangMsg("첫회보기")?></span></button>
							<?} else {?>
								<button type="button" class="btn-pk nb red bdrs" onclick="location='<?=$round_list[0]['href']?>'"><span><?=getTransLangMsg("첫회보기")?></span></button>
							<?}?>
						</div>
					<?}?>
				</div>

				<div class="list">
					<?if (count($notice_list) > 0) {?>
						<div class="t_news">
							<p>
								<a href="javascript:;" onclick="noticeList()">
									<span class="h">NEWS</span><span class="t"><?=$notice_list[0]['title']?></span>
									<span class="d"><?=formatDates($notice_list[0]['reg_date'],'Y.m.d')?></span><span class="v"><?=getTransLangMsg("더 보기+")?></span>
								</a>
							</p>
						</div>
					<?}?>
					<?if ($wt_view['billing_type'] == '20') {?>
						<div class="t_notice">
							<p class="t1">
								<span class="i-aft i_notice1">
									<?if ($wt_view['wait_free_end'] != "" && $wt_view['wait_free_end']!="") {?>
										<?=str_replace("{{round1}}", $wt_view['wait_free_start'], str_replace("{{round2}}", $wt_view['wait_free_end'], getTransLangMsg("{{round1}}화 부터 {{round2}}화 까지")))?>
									<?} else if ($wt_view['wait_free_start'] != "" && chkBlank($wt_view['wait_free_end'])) {?>
										<?=str_replace("{{round}}", $wt_view['wait_free_start'], getTransLangMsg("{{round}}화 부터"))?>
									<?} else if (chkBlank($wt_view['wait_free_start']) && $wt_view['wait_free_end'] != "") {?>
										<?=str_replace("{{round}}", $wt_view['wait_free_end'], getTransLangMsg("{{round}}화 까지"))?>
									<?} else if (chkBlank($wt_view['wait_free_start']) && chkBlank($wt_view['wait_free_end'])) {?>
										<?if ($wt_view['wait_free_exception'] > 0) {?>
											<?=str_replace("{{round}}", $wt_view['wait_free_exception'], getTransLangMsg("최근 {{round}}화 제외"))?>
										<?}?>
									<?}?>
									<?=getTransLangMsg("“". $wt_view['wait_free_time_txt'] ."”마다 기다리면 무료 작품입니다.")?>
								</span>

								<?if ($used_free_ticket) {?>
									<span class="r c-red"><?=$free_ticket_remaining_time?></span>
								<?} else {?>
									<span class="r c-red"><?=iif($wait_free_cnt > 0, getTransLangMsg("무료 열람 가능"), '')?></span>
								<?}?>
							</p>
						</div>
					<?}?>

					<div class="box_list">
						<?for ($i=0; $i<count($round_list); $i++) {?>
							<div class="box episode-item"
								data-episode="<?=$round_list[$i]['idx']?>"
								data-free="<?=iif($round_list[$i]['free_status']=='20', 'Y', 'N')?>"
								data-collection="<?=$round_list[$i]['collection_cost']-$round_list[$i]['dc_cost']?>"
								data-rental="<?=$round_list[$i]['rental_cost']-$round_list[$i]['dc_cost']?>"
								<?if ($round_list[$i]['open_flag']=='N') {?>style="background-color:#f1f1f1;"<?}?>
							>
								<a href="<?=$round_list[$i]['href']?>" <?=$round_list[$i]['onclick']?>>
									<div class="im"><span style="background-image: url('<?=filePathCheck('/upload/webtoon/'. $round_list[$i]['webtoon_idx'] .'/list/'. getUpfileName($round_list[$i]['up_file_1']))?>');"></span></div>
									<div class="tx">
										<p class="num"><?=$round_list[$i]['sort']?></p>
										<div class="hh">
											<p class="h"><?=$round_list[$i]['title']?></p>
											<!-- <p class="t t_line"><span>VIEW <?=formatNumbers($round_list[$i]['total_view_cnt'],0)?></span></p> -->
										</div>
										<div class="bt">
											<?if ($round_list[$i]['is_free_ticket'] || $round_list[$i]['is_adview'] || $round_list[$i]['is_rental']) { //이용권,광고뷰,대여 사용?>
												<span class="c-gray"><?=getTransLangMsg("대여중")?><br><?=$round_list[$i]['remaining_time']?></span>
											<?} else if ($round_list[$i]['is_purchase']) { //구매 사용?>
												<span class="btn-pk ss gray rv bdrs"><span><?=getTransLangMsg("구매완료")?></span></span>
											<?} else {?>
												<?if ($round_list[$i]['free_status'] == '10') {?>
													<?if ($wt_view['billing_type']=='20' && $round_list[$i]['free_exception']!='Y' && $used_free_ticket==false) {?>
														<p class="mb5">
															<?if ($round_list[$i]['dc_cost'] > 0) {?>
																<span class="line"><?=$round_list[$i]['rental_cost']?> <?=getTransLangMsg("G캐시")?></span><br>
																<span><?=$round_list[$i]['rental_cost'] - $round_list[$i]['dc_cost']?> <?=getTransLangMsg("G캐시")?></span>
															<?} else {?>
																<span><?=$round_list[$i]['rental_cost']?> <?=getTransLangMsg("G캐시")?></span><br>
															<?}?>
														</p>
														<span class="btn-pk ss red rv bdrs"><span><?=getTransLangMsg("이용권<em class=\"hide-m\">사용</em>")?></span></span>
													<?} else {?>
														<p>
															<?if ($round_list[$i]['dc_cost'] > 0) {?>
																<span class="line"><?=$round_list[$i]['rental_cost']?> <?=getTransLangMsg("G캐시")?></span><br>
																<span><?=$round_list[$i]['rental_cost'] - $round_list[$i]['dc_cost']?> <?=getTransLangMsg("G캐시")?></span>
															<?} else {?>
																<span><?=$round_list[$i]['rental_cost']?> <?=getTransLangMsg("G캐시")?></span><br>
															<?}?>
														</p>
													<?}?>
												<?} else if ($round_list[$i]['free_status'] == '20') {?>
													<span class="btn-pk ss red rv bdrs"><span><?=getTransLangMsg("무료")?></span></span>
												<?} else if ($round_list[$i]['free_status'] == '30') {?>
													<p class="mb5">
														<?if ($round_list[$i]['dc_cost'] > 0) {?>
															<span class="line"><?=$round_list[$i]['rental_cost']?> <?=getTransLangMsg("G캐시")?></span><br>
															<span><?=$round_list[$i]['rental_cost'] - $round_list[$i]['dc_cost']?> <?=getTransLangMsg("G캐시")?></span>
														<?} else {?>
															<span><?=$round_list[$i]['rental_cost']?> <?=getTransLangMsg("G캐시")?></span><br>
														<?}?>
													</p>
													<span class="btn-pk ss red bdrs"><span><?=getTransLangMsg("광고 후<em class=\"hide-m\"> 감상</em>")?></span></span>
												<?}?>
											<?}?>
										</div>
									</div>
								</a>
							</div>
						<?}?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?if (SITE_SAVE_LANG != 'KO' && count($translator_list) > 0) {?>
		<div class="view_bottom <?=iif(!chkBlank($params['translator_id']), 'on', '')?>" >
			<div class="inner">
				<button type="button" class="btn_more f-gm" onclick="$('.view_bottom').toggleClass('on');"><span><?=getTransLangMsg("번역 웹툰 보기")?><span class="i-set i_arr_w"></span></span></button>
				<div class="box">
					<select class="select1" onchange="location='?wt=<?=$params['webtoon_idx']?>&tid='+this.value">
						<option value=""><?=getTransLangMsg("번역자 선택")?></option>
						<?for ($i=0; $i<count($translator_list); $i++) {?>
							<option value="<?=encryption($translator_list[$i]['usr_idx'])?>" <?=chkCompare($params['translator_id'], encryption($translator_list[$i]['usr_idx']), 'selected')?>><?=$translator_list[$i]['nick_name']?></option>
						<?}?>
					</select>
				</div>
			</div>
		</div>
	<?}?>
</div><!--//container -->

<!-- 공지사항 -->
<div id="popFooterNews" class="layerPopup pop_footer_news" ></div>

<!-- 작가소개 -->
<div id="popWriter" class="layerPopup pop_writer"></div>

<!-- 좋아요 평점 참여 -->
<div id="popFavorit" class="layerPopup pop_favorit"></div>

<!-- 구매 -->
<div id="popViewPurchase" class="layerPopup pop_view_all"></div>

<!-- 이용불가 안내 -->
<div id="popNotAvailable" class="layerPopup pop_trans_refusal">
	<section class="popup">
		<header class="p_head">
			<h2 class="tit"><span><?=getTransLangMsg("서비스 이용불가 안내")?></span></h2>
			<button type="button" class="btn_close b-close"><span><?=getTransLangMsg("닫기")?></span></button>
		</header>
		<div class="p_cont">
			<div class="box">
				<p class="reason"></p>
			</div>
		</div>
		<div class="p_botm">
			<button type="button" class="btn-pk n red rv b-close"><span><?=getTransLangMsg("닫기")?></span></button>
		</div>
	</section>
</div>

<script>
	$(function(){
		window.onpageshow = function(event) {
			if ( event.persisted || (window.performance && window.performance.navigation.type == 2)) {
				location.reload();
			}
		}
	})

	//작가 상세보기
	function webtoonWriterView(writer_idx) {
		AJ.callAjax("writer_view.php", {"wt": "<?=$params['webtoon_idx']?>", "writer_idx": writer_idx}, function(data){
			$("#popWriter").html(data);
			openLayerPopup('popWriter');
		}, "html");
	}

	//웹툰 SNS공유
	function snsShareGo(sns_type) {
		<?if (!isUser()) {?>
			popupNonLogin();
		<?} else {?>
			AJ.callAjax("sns_share_save_proc.php", {"wt": "<?=$params['webtoon_idx']?>", "sns": sns_type}, function(data){
				if (data.result == 200) {
					var $target = $(".i_"+ sns_type +"1").parent();
					if ($target.hasClass("on")) {
						$target.removeClass("on");
					} else {
						$target.addClass("on");
						if (data.point_save == 'Y') {
							popupPointActiveComplete();
						}

						var sendText = "<?=$wt_view['title']?>";
						var sendUrl = "<?=SITE_URL?>/view/view.php?wt=<?=$wt_view['idx']?>";
						if (sns_type == 'facebook') {
							window.open("https://twitter.com/intent/tweet?text=" + sendText + "&url=" + sendUrl, "sns_share", "width=600,height=500");
						} else if (sns_type == 'twitter') {
							window.open("http://www.facebook.com/sharer/sharer.php?u=" + sendUrl, "sns_share", "width=600,height=500");
						}
					}
				} else {
					alert(data.message);
				}
			});
		<?}?>
	}

	//웹툰 평점
	function webtoonRatingGo() {
		<?if (!isUser()) {?>
			popupNonLogin();
		<?} else {?>
			AJ.callAjax("/popup/webtoon_rating.php", {"wt": "<?=$params['webtoon_idx']?>"}, function(data){
				$("#popFavorit").html(data);
				openLayerPopup('popFavorit');
			}, "html");
		<?}?>
	}

	//웹툰 좋아요
	function favoritGo() {
		<?if (!isUser()) {?>
			popupNonLogin();
		<?} else {?>
			AJ.callAjax("favorit_save_proc.php", {"wt": "<?=$params['webtoon_idx']?>"}, function(data){
				if (data.result == 200) {
					var $target = $(".i_favorit2").parent();
					if ($target.hasClass("on")) {
						$target.removeClass("on");
					} else {
						$target.addClass("on");
						if (data.point_save == 'Y') {
							popupPointActiveComplete();
						}
					}
				} else {
					alert(data.message);
				}
			});
		<?}?>
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

	//웹툰 공지사항 보기
	function noticeList() {
		AJ.callAjax("notice_list.php", {"wt": "<?=$params['webtoon_idx']?>"}, function(data){
			$("#popFooterNews").html(data);
			openLayerPopup('popFooterNews');
		}, "html");
	}

	//이용불가 안내
	function notAvailableGo(msg) {
		openLayerPopup('popNotAvailable');
		$("#popNotAvailable").find(".reason").html(msg)
	}
</script>

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>