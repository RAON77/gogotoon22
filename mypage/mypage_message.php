<? include "../inc/config.php" ?>
<?
	setSession("RETURN_URL", NOW_URL);
	if (!isUser()) fnMsgGo(500, "", "/member/login.php?return_flag=Y", "");

	$params['page']       = chkReqRpl("page", 1, "", "", "INT");
	$params['list_size']  = 10;
	$params['block_size'] = 10;
	$params['sch_usr']    = $MEM_USR['usr_idx'];
	$params['sch_lang']   = SITE_SAVE_LANG;
	$page_params          = setPageParamsValue($params, "page,list_size,block_size,sch_usr,sch_lang");

	//사용자 쿠폰함 내역 목록 불러오기
	$list = $cls_member->message_list($params, $total_cnt, $total_page);

	$pageNum = "7";
	$pageSubNum = "5";
?>
<? include "../inc/top.php" ?>
<? include "../inc/header.php" ?>

<div id="container" class="container mypage">
	<div class="contents">
		<div class="inr-c">
			<? include "top.php" ?>

			<div class="my_cont">
				<div class="inner">
					<div class="hd_tit1">
						<h2 class="h f-gm"><?=getTransLangMsg("메세지함")?></h2>
					</div>

					<div class="tbl_msg">
						<?if (count($list) > 0) {?>
							<ul>
								<?for ($i=0; $i<count($list);$i++) {?>
									<li <?=iif($list[$i]['view_flag']=='Y', 'class="on"', '')?>>
										<a href="mypage_message_view.php?idx=<?=$list[$i]['idx']?>" class="tit">
											<?
												$content = getTransLangMsg($list[$i]['content']);
												$content = str_replace("{{title}}", $list[$i]['webtoon_title'], $content);
												$content = str_replace("{{round}}", $list[$i]['webtoon_round'], $content);

												echo "<strong class='mr10'>". formatDates($list[$i]['reg_date'], 'Y.m.d') ."</strong>";
												echo htmlDecode($content);
											?>
										</a>
									</li>
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