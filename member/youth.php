<? include "../inc/config.php" ?>
<?
	$cls_set_youth = new SETTING_YOUTH_POLICY;

	$view = $cls_set_youth->youth_view('', SITE_SAVE_LANG, 'Y');

	$pageNum = "6";
	$pageSubNum = "0";
?>
<? include "../inc/top.php" ?>
<? include "../inc/header.php" ?>

<div id="container" class="container sub">
	<div class="inr-c">
		<div class="tab ty3 mb0">
			<ul>
				<li><a href="terms.php"><span>이용약관</span></a></li>
				<li><a href="privacy.php"><span>개인정보처리방침</span></a></li>
				<li class="on"><a href="youth.php"><span>청소년보호정책</span></a></li>
			</ul>
		</div>

		<div class="area_terms ty2"><?=htmlDecode($view['content'])?></div>
	</div>
</div><!--//container -->

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>