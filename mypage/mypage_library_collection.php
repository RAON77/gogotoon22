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

	//사용자 마이페이지 소장 웹툰 목록 불러오기
	$list = $cls_member->mypage_library_collection_list($params, $total_cnt, $total_page);

	$pageNum = "7";
	$pageSubNum = "1";
	$pageSubNum2 = "1";
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
						<p class="title1"><?=getTransLangMsg("소장 목록")?></p>
					</div>
					<div class="lst_prd1 pr-mb2 chk">
						<?if (count($list) > 0) {?>
							<ul>
								<?for ($i=0; $i<count($list); $i++) {?>
									<?
										if (chkBlank($list[$i]['trans_usr_idx'])) {
											$href = "mypage_library_collection_view.php?wt=". $list[$i]['webtoon_idx'];
										} else {
											$href = "mypage_library_collection_view.php?wt=". $list[$i]['webtoon_idx'] ."&tid=". encryption($list[$i]['trans_usr_idx']) ."&lang=". $list[$i]['lang'];
										}
									?>
									<li class="box"><a href="<?=$href?>">
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
												<?if ($list[$i]['total_round_cnt'] > 0) {?>
													<?
														$percent = round(($list[$i]['purchase_cnt'] / $list[$i]['total_round_cnt']) * 100,0);
													?>
													<span class="bar" style="width: <?=$percent?>%;"></span>
													<span class="bar_txt" style="width: <?=iif(100-$percent <=30, 30, 100-$percent)?>%;">
														<em class="c-red"><?=$percent?>%</em>
													</span>
												<?} else {?>
													<span style="width: 0%;"><em class="c-red">0%</em></span>
												<?}?>
											</div>
											<p class="h1"><?=$list[$i]['title']?></p>
											<p class="t1">
												<?=str_replace("{{round}}", $list[$i]['latest_open_round'], getTransLangMsg("제{{round}}화"))?>
												<span class="r"><i class="i-aft i_favorit1"><?=round($list[$i]['total_rating'],1)?></i></span>
											</p>
											<p class="t2 mt10 c-pink">
												<?if ($list[$i]['keyword'] != "") {?>
													<span>#<?=implode("</span><span>#", explode(",", $list[$i]['keyword']))?></span>
												<?}?>
											</p>
											<p class="t2 mt10"><?=getTransLangMsg("소장일")?> <?=formatDates($list[$i]['latest_purchase_date'],'Y.m.d')?></p>
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

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>