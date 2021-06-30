<!-- 메뉴 -->
<div id="popMenu" class="layerPopup pop_menu">
	<section class="popup">
		<header class="p_head">
			<h2 class="tit hidden"><span>메뉴</span></h2>
			<button type="button" class="btn_close b-close"><span>닫기</span></button>
		</header>
		<!-- 로그인전 -->
		<?If($pageNum=="0") { ?>
		<div class="p_cont menu1">
			<p class="h1">로그인</p>
			<div class="btns">
				<a href="javascript:;" class="btn-pk red2 rv b bdrs w100p" onclick="openLayerPopup('popLogin');">로그인</a>
				<a href="member/join.html" class="btn-pk gray rv b bdrs w100p">회원가입</a>
				<div class="line-top list1">
					<a href="#"><span class="i-aft i_menu5">고객센터</span></a>
				</div>
			</div>
		</div>
		<? } else { ?>
		<!-- 로그인후 -->
		<div class="p_cont menu2" style="display: block;">
			<div class="box">
				<div class="img"><span style="background-image: url(/images/common/img_mem.png);"></span></div>
				<div class="txt">
					<p class="t1"><span class="i-aft i_facebook_s">abc123@naver.com</span></p>
					<p class="t2 t_line"><span><strong class="c-red">100</strong> G캐시</span><span><strong class="c-red">100</strong> Point</span></p>
				</div>
			</div>
			<div>
				<div class="list1">
					<a href="/mypage/mypage_library_collection.php"><span class="i-aft i_menu1">내 서재</span></a>
					<a href="/mypage/mypage_history_payment.php"><span class="i-aft i_menu2">충전하기</span></a>
					<a href="/mypage/mypage_coupon_list.php"><span class="i-aft i_menu3">쿠폰함</span></a>
					<a href="/mypage/mypage_info.php"><span class="i-aft i_menu4">정보수정</span></a>
					<a href="/customer/notice.php"><span class="i-aft i_menu5">고객센터</span></a>
				</div>
				<div class="btn-bot">
					<a href="/translation/translatable_list.php" class="btn-pk green rv b bdrs w100p view-m">번역회원</a>
					<a href="#" class="btn-pk gray rv b bdrs w100p">로그아웃</a>
				</div>
			</div>
		</div>
		<? } ?>
	</section>
</div>

<!-- 로그인 -->
<div id="popLogin" class="layerPopup pop_login pop_member">
	<section class="popup">
		<header class="p_head ty2">
			<h2 class="tit blind"><span>로그인</span></h2>
			<button type="button" class="btn_close b-close"><span>닫기</span></button>
		</header>
		<div class="p_cont">
			<div class="img ta-c pr-mb2"><img src="/images/common/logo.png" alt="고고툰"></div>
			<div class="hd_titbox1">
				<h2 class="title1 ta-c">SNS 로그인</h2>
			</div>
			<ul class="list1 pr-mb2">
				<li><a href="#"><span class="i-set i_sns1">구글</span></a></li>
				<li><a href="#"><span class="i-set i_sns2">애플</span></a></li>
				<li><a href="#"><span class="i-set i_sns3">페이스북</span></a></li>
				<li><a href="#"><span class="i-set i_sns4">위챗</span></a></li>
			</ul>
			<div class="hd_titbox1">
				<h2 class="title1 ta-c">이메일 로그인</h2>
			</div>
			<input type="text" class="inp_txt w100p" placeholder="이메일 아이디를 입력해주세요.">
			<input type="password" class="inp_txt w100p" placeholder="비밀번호는 8자 이상 입력해 주세요.">
			<label class="inp_checkbox"><input type="checkbox"><span>자동로그인</span></label>
			<div class="btn-bot ta-c pr-mb2 mbtn_ty1">
				<a href="/member/join.php" class="btn-pk b gray rv bdrs">회원가입</a>
				<a href="#" class="btn-pk b red2 rv bdrs">로그인</a>
			</div>
			<div class="ta-c pb20">
				<a href="/member/find_id.php" class="t1 c-black">비밀번호 찾기</a>
			</div>
		</div>
	</section>
</div>