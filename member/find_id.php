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
		<div class="pop_find pop_member">
			<section class="popup">
				<div class="p_cont">
					<form name="findFrm" id="findFrm" method="post">
					<div class="hd_titbox1 ta-c pr-mb1">
						<h2 class="title1 pr-mb2"><?=getTransLangMsg("비밀번호 찾기")?></h2>
						<p class="t1 c-red"><?=getTransLangMsg("아래의 입력란에 이메일 주소를 입력해 주십시오.")?></p>
					</div>
					<input type="text" name="email_id" id="email_id" class="inp_txt w100p mb20" placeholder="<?=getTransLangMsg("이메일 아이디를 입력해주세요.")?>">
					<a href="javascript:;" class="btn-pk b red2 rv bdrs w100p" onclick="passwdFindGo()"><?=getTransLangMsg("확인")?></a>
					</form>

					<p class="t1 c-black ta-c mt1"><?=getTransLangMsg("메일이 도착하지 않는다면, 스팸 메일함을 확인해 주십시오.")?></p>
				</div>
			</section>
		</div>
	</div>
</div><!--//container -->

<script>
	function passwdFindGo() {
		AJ.ajaxForm($("#findFrm"), "/member/find_id_proc.php", function(data) {
			if (data.result == 200) {
				alert("<?=getTransLangMsg("비밀번호 변경안내 메일을 발송하였습니다.")?>");

				location.replace("<?=$return_url?>");
			} else {
				alert(data.message);
			}
		});
	}
</script>

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>