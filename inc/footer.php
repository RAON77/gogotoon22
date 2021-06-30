<?
	$footer_notice = CLS_BOARD::footer_list("notice", SITE_SAVE_LANG);
?>
<footer id="footer" class="footer">
	<div class="foo_top">
		<div class="inr-c">
			<p><span><?=getTransLangMsg("공지사항")?></span></p>
			<ul id="ticker">
				<?for ($i=0; $i<count($footer_notice); $i++) {?>
					<li>
						<a href="/customer/notice_view.php?idx=<?=$footer_notice[$i]['idx']?>">
						<span class="t t-dot-solo"><?=$footer_notice[$i]['title']?></span><span class="d"><?=formatDates($footer_notice[$i]['reg_date'], "Y.m.d")?></span>
						</a>
					</li>
				<?}?>
			</ul>
		</div>
	</div>
	<div class="foo_cont">
		<div class="inr-c">
			<div class="link">
				<ul>
					<!-- <li><a href="javascript:;"><?=getTransLangMsg("고고툰 회사소개")?></a></li> -->
					<li><a href="/member/terms.php"><?=getTransLangMsg("이용약관")?></a></li>
					<li><a href="/member/privacy.php" class="c-white"><?=getTransLangMsg("개인정보처리방침")?></a></li>
					<li><a href="/member/youth.php"><?=getTransLangMsg("청소년보호정책")?></a></li>
					<li><a href="/customer/notice.php"><?=getTransLangMsg("공지사항")?></a></li>
				</ul>
			</div>
			<div class="down">
				<p>DOWNLOAD <?=getTransLangMsg("고고툰")?><i>!</i></p>
				<a href="javascript:;"><img src="/images/common/ico_app.jpg" alt="App Store"></a>
				<a href="javascript:;"><img src="/images/common/ico_google.jpg" alt="Google"></a>
			</div>
			<div class="copy">
				<div><img src="/images/common/logo_footer.png" alt="<?=getTransLangMsg("고고툰")?>"></div>
				<p>(주)고고툰엔터테인먼트</p>
				<p>&copy; GOGOTOON Inc.</p>
			</div>
		</div>
	</div>
</footer>