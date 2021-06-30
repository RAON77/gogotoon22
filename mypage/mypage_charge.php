<? include "../inc/config.php" ?>
<?
	setSession("RETURN_URL", NOW_URL);
	if (!isUser()) fnMsgGo(500, "", "/member/login.php?return_flag=Y", "");

	//사용자 정보 불러오기
	$user_view = $cls_member->user_view($MEM_USR['usr_idx']);
	if ($user_view == false) fnMsgGo(500, "", "/member/logout.php", "");

	//G캐시 상품 카테고리 불러오기
	$goods_list = getGcashChargeList();

	//결제수단 불러오기
	$paymethod_list = getPayMethodList(SITE_SAVE_LANG);

	$pageNum = "7";
	$pageSubNum = "1";
?>
<? include "../inc/top.php" ?>
<? include "../inc/header.php" ?>

<div id="container" class="container mypage">
	<div class="contents">
		<div class="inr-c">
			<div class="pop_charge">
				<div class="hd_tit1 ta-c">
					<h2 class="h f-gm"><?=getTransLangMsg("G캐시 충전")?></h2>
				</div>
				<section class="popup">
					<div class="p_cont">
						<div class="info">
							<p>
								<span><?=formatNumbers($user_view['total_gcash'])?> <?=getTransLangMsg("G캐시")?></span>
								<span><?=formatNumbers($user_view['total_point'])?> Point</span>
							</p>
						</div>
						<div class="list">
							<ul>
								<?for ($i=0; $i<count($goods_list); $i++) {?>
									<li>
										<label class="inp_radio">
											<input type="radio" name="goods" value="<?=$goods_list[$i]['code']?>" data-goods-amt="<?=formatNumbers($goods_list[$i]['amount'],2)?> USD">
											<span>
												<em class="ta-l"><?=formatNumbers($goods_list[$i]['gcash'])?> <?=getTransLangMsg("G캐시")?></em>
												<em class="ta-c">
													<?
														if ($goods_list[$i]['bonus'] > 0) {
															echo "+";
															echo formatNumbers($goods_list[$i]['gcash'] * ($goods_list[$i]['bonus'] / 100));
															echo " Point (". $goods_list[$i]['bonus'] ."%)";
														}
													?>
												</em>
												<em class="ta-r"><?=formatNumbers($goods_list[$i]['amount'],2)?> USD</em>
											</span>
										</label>
									</li>
								<?}?>
							</ul>
						</div>
						<div class="total">
							<p><?=getTransLangMsg("결제할 금액")?></p>
							<p><strong class="total-amount">0 USD</strong></p>
						</div>
						<div class="botm">
							<p><?=getTransLangMsg("결제수단")?></p>
							<div>
								<div class="inp_radiotx">
									<?for ($i=0; $i<count($paymethod_list); $i++) {?>
										<label class="inp_radio">
											<input type="radio" name="pay_method" value="<?=$paymethod_list[$i]['code']?>">
											<span><?=$paymethod_list[$i]['name']?></span>
										</label>
									<?}?>
								</div>
							</div>
						</div>
					</div>
					<div class="p_botm">
						<button type="button" class="btn-pk n red rv w100p" onclick="paymentGo()"><span>결제하기</span></button>
					</div>
				</section>
			</div>
		</div>
	</div>
</div><!--//container -->

<script>
	$(function(){
		$(":radio[name=goods]").click(function(){
			$(".total-amount").text( $(this).data("goods-amt") );
		})
	})

	//결제시작
	function paymentGo() {
		if ($(":radio[name=goods]:checked").length == 0) {
			alert("충전할 캐시를 선택해주세요.");
			return false;
		}

		if ($(":radio[name=pay_method]:checked").length == 0) {
			alert("결제수단을 선택해주세요.");
			return false;
		}

		AJ.callAjax("mypage_charge_proc.php", {"goods": $(":radio[name=goods]:checked").val(), "pay_method": $(":radio[name=pay_method]:checked").val()}, function(data){
			if (data.result == 200) {
				location.replace("mypage_history_payment.php");
			} else {
				alert(data.message)
			}
		});
	}
</script>

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>