<? include "../inc/config.php" ?>
<?
	$params['types'] = chkReqRpl("tp", "W", 1, "", "STR");
	$params['week']  = chkReqRpl("week", weekDay(date('Y-m-d')), "", "", "INT");

	$cls_wt = new CLS_WEBTOON;

	$wt_list = $cls_wt->series_list($params, SITE_SAVE_LANG);

	$pageNum = "1";
	$pageSubNum = "1";
?>
<? include "../inc/top.php" ?>
<? include "../inc/header.php" ?>

<div id="container"  class="container sub2">
	<div class="top_sub_menu inr-c">
		<div class="tab ty1">
			<ul class="f-gm">
				<?for ($i=1; $i<=7; $i++) {?>
					<li class="<?=iif($params['types']=='W' && $params['week']==$i, "on", "")?> <?=iif(weekDay(date('Y-m-d'))==$i, "today", "")?>">
						<a href="?tp=W&week=<?=$i?>"><?=$CONST_WEEK_NAME_ENG2[$i-1]?></a>
					</li>
				<?}?>
				<li class="<?=iif($params['types']=='N', "on", "")?>"><a href="?tp=N"><?=getTransLangMsg("비정기")?></a></li>
			</ul>
		</div>
	</div>

	<div class="contents">
		<div class="inr-c">
			<div class="lst_prd1">
				<?if (count($wt_list) > 0) {?>
					<ul>
						<?for ($i=0; $i<count($wt_list); $i++) {?>
							<li class="box"><a href="/view/view.php?wt=<?=$wt_list[$i]['idx']?>">
								<div class="img">
									<span><img src="<?=filePathCheck('/upload/webtoon/'. $wt_list[$i]['idx'] .'/list/'. getUpfileName($wt_list[$i]['up_file_1']))?>"></span>
									<?if ($wt_list[$i]['series_status']=='20') {?>
										<div class="ico_l"><i class="i_comp"><?=getTransLangMsg("완결")?></i></div>
									<?} else if ($wt_list[$i]['billing_type']=='20') {?>
										<div class="ico_l"><i class="i-set i_free"><?=getTransLangMsg("무료")?></i></div>
									<?}?>
								</div>
								<div class="txt">
									<p class="h1"><?=$wt_list[$i]['title']?></p>
									<p class="t1">
										<?=str_replace("{{round}}", $wt_list[$i]['latest_open_round'], getTransLangMsg("제{{round}}화"))?>
										<span class="r"><i class="i-aft i_favorit1"><?=round($wt_list[$i]['total_rating'],1)?></i></span>
									</p>
									<p class="t2 mt10 c-pink">
										<?if ($wt_list[$i]['keyword'] != "") {?>
											<span>#<?=implode("</span><span>#", explode(",", $wt_list[$i]['keyword']))?></span>
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
		</div>
	</div>
</div><!--//container -->

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>