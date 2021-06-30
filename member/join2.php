<? include "../inc/config.php" ?>
<?
	if (isUser()) fnMsgGo(200, "", "/", "");

	$pageNum = "6";
	$pageSubNum = "0";
?>
<? include "../inc/top.php" ?>
<? include "../inc/header.php" ?>

<div id="container" class="container">
	<div class="inr-c area_member">
		<div class="pop_join pop_member">
			<section class="popup">
				<header class="p_head ty2">
					<h2 class="tit hidden"><span><?=getTransLangMsg("회원가입완료")?></span></h2>
				</header>
				<div class="p_cont">
					<div class="txt_comp">
						<div class="img"><img src="/images/common/img_comp.png" alt="완료"></div>
						<p class="t1"><?=getTransLangMsg("고고툰 회원가입에 감사드립니다.<br>가입축하 ". POINT_MEMBER_JOIN ."Point가 지급되었습니다.")?></p>
					</div>
					<div class="btn-bot">
						<a href="/" class="btn-pk b red2 rv bdrs w100p"><?=getTransLangMsg("메인 화면으로 이동")?></a>
					</div>
				</div>
			</section>
		</div>
	</div>
</div><!--//container -->

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>