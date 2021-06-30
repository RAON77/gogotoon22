</div><!--//wrap -->

<!-- 메뉴 -->
<div id="popMenu" class="layerPopup pop_menu">
	<section class="popup">
		<header class="p_head">
			<h2 class="tit hidden"><span><?=getTransLangMsg("메뉴")?></span></h2>
			<button type="button" class="btn_close b-close"><span><?=getTransLangMsg("닫기")?></span></button>
		</header>
		<!-- 로그인전 -->
		<?if(!isUser()) { ?>
			<div class="p_cont menu1" style="display: block;">
				<p class="h1"><?=getTransLangMsg("로그인")?></p>
				<div class="btns">
					<a href="/member/login.php" class="btn-pk red2 rv b bdrs w100p"><?=getTransLangMsg("로그인")?></a>
					<a href="/member/join.php" class="btn-pk gray rv b bdrs w100p"><?=getTransLangMsg("회원가입")?></a>
					<div class="line-top list1">
						<a href="/customer/notice.php"><span class="i-aft i_menu5"><?=getTransLangMsg("고객센터")?></span></a>
					</div>
				</div>
			</div>
		<?} else {?>
			<!-- 로그인후 -->
			<div class="p_cont menu2" style="display: block;">
				<div class="box">
					<div class="img"><span style="background-image: url(/images/common/img_mem.png);"></span></div>
					<div class="txt">
						<p class="t1">
							<?if (!chkBlank($MEM_USR['sns_gubun'])) {?>
								<span class="i-aft i_<?=$MEM_USR['sns_gubun']?>_s">
							<?}?>
							<?=$MEM_USR['usr_email']?></span>
						</p>
						<p class="t2 t_line">
							<span><strong class="c-red"><?=formatNumbers($MEM_USR['total_gcash'])?></strong> <?=getTransLangMsg("G캐시")?></span>
							<span><strong class="c-red"><?=formatNumbers($MEM_USR['total_point'])?></strong> Point</span>
						</p>
					</div>
				</div>
				<div>
					<div class="list1">
						<a href="/mypage/mypage_library_collection.php"><span class="i-aft i_menu1"><?=getTransLangMsg("내 서재")?></span></a>
						<a href="/mypage/mypage_history_payment.php"><span class="i-aft i_menu2"><?=getTransLangMsg("충전내역")?></span></a>
						<a href="/mypage/mypage_coupon_list.php"><span class="i-aft i_menu3"><?=getTransLangMsg("쿠폰함")?></span></a>
						<a href="/mypage/mypage_info.php"><span class="i-aft i_menu4"><?=getTransLangMsg("정보수정")?></span></a>
						<a href="/customer/notice.php"><span class="i-aft i_menu5"><?=getTransLangMsg("고객센터")?></span></a>
					</div>
					<div class="btn-bot">
						<?if ($MEM_USR['usr_gubun'] == '20') {?>
							<a href="/translation/translatable_list.php" class="btn-pk green rv b bdrs w100p view-m"><?=getTransLangMsg("번역회원")?></a>
						<?}?>
						<a href="/member/logout.php?tp=btn" class="btn-pk gray rv b bdrs w100p"><?=getTransLangMsg("로그아웃")?></a>
					</div>
				</div>
			</div>
		<?}?>
	</section>
</div>

<!-- 비로그인 체크-->
<div id="popNonLogin" class="layerPopup pop_login pop_member">
	<section class="popup">
		<header class="p_head ty2">
			<h2 class="tit blind"><span><?=getTransLangMsg("로그인")?></span></h2>
			<button type="button" class="btn_close b-close"><span><?=getTransLangMsg("닫기")?></span></button>
		</header>
		<div class="p_cont">
			<div class="img ta-c pr-mb2"><img src="/images/common/logo.png"></div>
			<div class="hd_titbox1">
				<h2 class="title1 ta-c"><?=getTransLangMsg("로그인 후 이용가능합니다.")?></h2>
			</div>
			<div class="btn-bot ta-c pr-mb2 mbtn_ty1">
				<a href="/member/join.php" class="btn-pk b gray rv bdrs"><?=getTransLangMsg("회원가입")?></a>
				<a href="/member/login.php?return_flag=Y" class="btn-pk b red2 rv bdrs"><?=getTransLangMsg("로그인")?></a>
			</div>
			<div class="ta-c pb20">
				<a href="/member/find_id.php" class="t1 c-black"><?=getTransLangMsg("비밀번호 찾기")?></a>
			</div>
		</div>
	</section>
</div>

<!-- 포인트적립 -->
<div id="popPointActiveComplete" class="layerPopup pop_point"></div>

<script type="text/javascript" src="/js/jquery-ui.js"></script>

<script type="text/javascript" src="/module/js/jquery.form.js"></script>
<script type="text/javascript" src="/module/js/jquery.tmpl.js"></script>
<script type="text/javascript" src="/module/js/jquery.moment.js"></script>
<script type="text/javascript" src="/module/js/fn.util.js"></script>
<script type="text/javascript" src="/module/js/fn.check.field.js"></script>
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
	function popupNonLogin() {
		openLayerPopup('popNonLogin');
	}

	function popupPointActiveComplete() {
		AJ.callAjax("/popup/point_active_complete.php", null, function(data){
			$("#popPointActiveComplete").html(data);
			openLayerPopup('popPointActiveComplete');
		}, "html");
	}
</script>
<?=$ACTION_IFRAME?>
</body>
</html>