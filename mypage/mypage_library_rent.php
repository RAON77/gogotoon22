<? include "../inc/config.php" ?>
<?
	setSession("RETURN_URL", NOW_URL);
	if (!isUser()) fnMsgGo(500, "", "/member/login.php?return_flag=Y", "");

	$params['page']       = chkReqRpl("page", 1, "", "", "INT");
	$params['list_size']  = 20;
	$params['block_size'] = 10;
	$params['sch_usr']    = $MEM_USR['usr_idx'];
	$params['sch_lang']   = SITE_SAVE_LANG;
	$page_params          = setPageParamsValue($params, "page,list_size,block_size");

	$cls_member = new CLS_MEMBER;

	//사용자 마이페이지 대여 웹툰 목록 불러오기
	$list = $cls_member->mypage_library_rent_list($params, $total_cnt, $total_page);

	$pageNum = "7";
	$pageSubNum = "1";
	$pageSubNum2 = "2";
?>
<? include "../inc/top.php" ?>
<? include "../inc/header.php" ?>

<div id="container" class="container mypage">
	<div class="contents">
		<div class="inr-c">
			<? include "top.php" ?>

			<div class="my_cont">
				<? include "__tab_library.php" ?>

				<div class="inner">
					<div class="hd_titbox1">
						<p class="title1"><?=getTransLangMsg("대여 목록")?></p>
						<div class="rgh">
							<button type="button" class="btn-pk white2 s" onclick="viewbtn(this);"><span><?=getTransLangMsg("삭제")?></span></button>
						</div>
					</div>
					<div class="lst_prd1 pr-mb2 chk">
						<?if (count($list) > 0) {?>
							<ul>
								<?for ($i=0; $i<count($list); $i++) {?>
									<?
										if (chkBlank($list[$i]['trans_usr_idx'])) {
											$href = "mypage_library_rent_view.php?wt=". $list[$i]['webtoon_idx'] ."&ep=". $list[$i]['round_idx'];
										} else {
											$href = "mypage_library_rent_view.php?wt=". $list[$i]['webtoon_idx'] ."&ep=". $list[$i]['round_idx'] ."&tid=". encryption($list[$i]['trans_usr_idx']) ."&tidx=". $list[$i]['trans_idx'] ."&lang=". $list[$i]['lang'];
										}
									?>
									<li class="box"><a href="<?=$href?>">
										<?if ($list[$i]['remaining_time'] > 0) {?>
											<label class="inp_checkbox"><input type="checkbox" name="chk_wt[]" value="<?=$list[$i]['rent_idx']?>" class="chk_wt"><span></span></label>
										<?}?>
										<div class="img">
											<span><img src="<?=filePathCheck('/upload/webtoon/'. $list[$i]['webtoon_idx'] .'/list/'. getUpfileName($list[$i]['up_file_1']))?>"></span>
											<?if ($list[$i]['series_status']=='20') {?>
												<div class="ico_l"><i class="i_comp"><?=getTransLangMsg("완결")?></i></div>
											<?} else if ($list[$i]['billing_type']=='20') {?>
												<div class="ico_l"><i class="i-set i_free"><?=getTransLangMsg("무료")?></i></div>
											<?}?>
										</div>
										<div class="txt">
											<div class="per">
												<span class="bar" style="width: 100%;"></span>
												<span class="bar_txt ta-c" style="width: 100%;">
													<em class="c-red pl10 pr10">
													<?
														if ($list[$i]['remaining_time'] > 0) {
															echo getTransLangMsg("기간만료");
														} else {
															$remaining_time = abs($list[$i]['remaining_time']);

															if ($remaining_time <= 60) {
																$remaining_time = getTransLangMsg("1분 미만 남음");
															} else {
																$result = "";
																$day    = timeToKor($remaining_time, "D");
																$hour   = timeToKor($remaining_time, "G");
																$minute = timeToKor($remaining_time, "I");
																if ($day > 0) $result .= iif($day>1, "{{days}}일 ", "{{day}}일 ");
																if ($hour > 0) $result .= iif($hour>1, "{{hours}}시간 ", "{{hour}}시간 ");
																if ($day == 0 && $minute > 0) {
																	$result .= iif($minute>1, "{{minutes}}분 ", "{{minute}}분 ");
																}

																$remaining_time = getTransLangMsg($result ."남음");
																$remaining_time = str_replace("{{days}}", $day, $remaining_time);
																$remaining_time = str_replace("{{day}}", $day, $remaining_time);
																$remaining_time = str_replace("{{hours}}", $hour, $remaining_time);
																$remaining_time = str_replace("{{hour}}", $hour, $remaining_time);
																$remaining_time = str_replace("{{minutes}}", $minute, $remaining_time);
																$remaining_time = str_replace("{{minute}}", $minute, $remaining_time);
															}

															echo $remaining_time;
														}
													?>
													</em>
												</span>
											</div>
											<p class="h1"><?=$list[$i]['title']?></p>
											<p class="t1">
												<?=str_replace("{{round}}", $list[$i]['round_num'], getTransLangMsg("제{{round}}화"))?>
												<span class="r"><i class="i-aft i_favorit1"><?=round($list[$i]['total_rating'],1)?></i></span>
											</p>
											<p class="t2 mt10 c-pink">
												<?if ($list[$i]['keyword'] != "") {?>
													<span>#<?=implode("</span><span>#", explode(",", $list[$i]['keyword']))?></span>
												<?}?>
											</p>
											<p class="t2 mt10"><?=getTransLangMsg("대여일")?> <?=formatDates($list[$i]['rent_date'],'Y.m.d')?></p>
											<p class="t2 mt10">
												<?if (chkBlank($list[$i]['trans_usr_idx'])) {?>
													&nbsp;
												<?} else {?>
													<?=getTransLangMsg("번역자")?> <?=$list[$i]['trans_nickname']?>
												<?}?>
											</p>
									</div>
									</a></li>
								<?}?>
							</ul>
						<?} else {?>
							<div class="ta-c"><?=getTransLangMsg("등록된 데이터가 없습니다.")?></div>
						<?}?>
					</div>

					<div class="pagenation">
                        <? frontPaging($total_page, $params['block_size'], $params['page'], $page_params, "") ?>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div><!--//container -->

<script>
	$(".tab.ty4 li").on("click", function(){
		$(".pos-r .rgh.bt a").remove();
		$(".rgh .btn-pk").show();
		$(".lst_prd1.chk .inp_checkbox").hide();
	});

	function viewbtn(e){
		var obj = '<div class="rgh bt"><a href="javascript:;" class="b1"><?=getTransLangMsg("취소")?></a><a href="javascript:;" class="b2" onclick="deleteGo()"><?=getTransLangMsg("확인")?></a></div>';

		$(e).hide();
		$(".pos-r").append(obj);
		$(".lst_prd1.chk .inp_checkbox").show();

		$(".my_cont .pos-r .rgh a.b1").on("click", function(){
			$(".pos-r .rgh.bt a").remove();
			$(e).show();
			$(".lst_prd1.chk .inp_checkbox").hide();
		});
	}

	function deleteGo() {
		if($(".chk_wt:checked").length == 0) {
			alert("<?=getTransLangMsg("웹툰을 선택해주세요.")?>");
			return false;
		}

		var chk_wt = [];
		$.each($(".chk_wt:checked"), function(){
			chk_wt.push( $(this).val() );
		});

		if (!confirm("<?=getTransLangMsg('선택한 웹툰을 삭제 하시겠습니까?\n삭제시 영구 삭제되며 복구는 불가능합니다.')?>")) return false;

		AJ.callAjax("mypage_library_rent_delete_proc.php", {"chk_wt": chk_wt}, function(data){
			if (data.result == 200) {
				alert("<?=getTransLangMsg("삭제 처리가 완료되었습니다.")?>");
				location.reload();
			} else {
				alert(data.message)
			}
		});
	}
</script>

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>