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
	$list = $cls_member->mypage_library_favorit_list($params, $total_cnt, $total_page);

	$pageNum = "7";
	$pageSubNum = "1";
	$pageSubNum2 = "3";
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
									<li class="box"><a href="/view/view.php?wt=<?=$list[$i]['webtoon_idx']?>">
										<label class="inp_checkbox"><input type="checkbox" name="wt[]" value="<?=$list[$i]['webtoon_idx']?>" class="chk_wt"><span></span></label>
										<div class="img">
											<span><img src="<?=filePathCheck('/upload/webtoon/'. $list[$i]['webtoon_idx'] .'/list/'. getUpfileName($list[$i]['up_file_1']))?>"></span>
											<?if ($list[$i]['series_status']=='20') {?>
												<div class="ico_l"><i class="i_comp"><?=getTransLangMsg("완결")?></i></div>
											<?} else if ($list[$i]['billing_type']=='20') {?>
												<div class="ico_l"><i class="i-set i_free"><?=getTransLangMsg("무료")?></i></div>
											<?}?>
										</div>
										<div class="txt">
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
											<p class="t2 mt10"><?=getTransLangMsg("저장일")?> <?=formatDates($list[$i]['favorit_date'],'Y.m.d')?></p>
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

		AJ.callAjax("mypage_library_favorit_delete_proc.php", {"chk_wt": chk_wt}, function(data){
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