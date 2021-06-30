<? include "../inc/config.php" ?>
<?
	if (isUser()) fnMsgGo(200, "", "/", "");

	$pageNum = "6";
	$pageSubNum = "0";

	echo mb_strlen("닉네임123abc");
	echo mb_strwidth("닉네임123abc");
	echo strlen("닉네임123abc");
?>
<? include "../inc/top.php" ?>
<? include "../inc/header.php" ?>

<div id="container" class="container">
	<div class="inr-c area_member">
		<div class="pop_join pop_member">
			<section class="popup">
				<header class="p_head ty2">
					<h2 class="tit hidden"><span><?=getTransLangMsg("이메일 회원가입")?></span></h2>
				</header>
				<div class="p_cont">
					<div class="img ta-c pr-mb2"><img src="/images/common/logo.png" alt="<?=getTransLangMsg("고고툰")?>"></div>
					<form name="joinFrm" id="joinFrm" method="post">
					<div class="hd_titbox1">
						<h2 class="title1 ta-c"><?=getTransLangMsg("이메일 회원가입")?></h2>
					</div>
					<input type="text" name="join_email" id="join_email" class="inp_txt w100p" maxlength="50" placeholder="<?=getTransLangMsg("이메일 아이디를 입력해주세요.")?>">
					<input type="password" name="join_passwd" id="join_passwd" class="inp_txt w100p" maxlength="20" placeholder="<?=getTransLangMsg("영문,숫자,특수문자 혼합 8자 이상 입력해주세요.")?>">
					<input type="text" name="join_nick" id="join_nick" class="inp_txt w100p" maxlength="20" placeholder="<?=getTransLangMsg("닉네임")?>">
					<!-- <div class="inp_btn">
						<input type="text" name="join_nick" class="inp_txt w100p" placeholder="<?=getTransLangMsg("닉네임")?>">
						<button type="button" class="btn-pk n gray rv" onclick="nickDuplCheck()"><span><?=getTransLangMsg("중복확인")?></span></button>
					</div> -->
					<div class="inp_radiotx pr-mb2">
						<label class="inp_radio"><input type="radio" name="join_gender" id="join_gender_1" value="M" checked><span><?=getTransLangMsg("남자")?></span></label>
						<label class="inp_radio"><input type="radio" name="join_gender" id="join_gender_2" value="F"><span><?=getTransLangMsg("여자")?></span></label>
					</div>

					<div class="lst_inp">
						<label class="inp_checkbox">
							<input type="checkbox" id="all_check"><span><?=getTransLangMsg("전체동의")?></span>
						</label>
						<label class="inp_checkbox">
							<input type="checkbox" name="agree_1" id="agree_1" class="agree" value="Y">
							<span>
								<em class="c-red">(<?=getTransLangMsg("필수")?>)</em> <?=getTransLangMsg("서비스 이용약관에 동의합니다.")?>
								<a href="/member/terms.php" target="_blank">[<?=getTransLangMsg("보기")?>]</a>
							</span>
						</label>
						<label class="inp_checkbox">
							<input type="checkbox" name="agree_2" id="agree_2" class="agree" value="Y">
							<span>
								<em class="c-red">(<?=getTransLangMsg("필수")?>)</em> <?=getTransLangMsg("개인정보처리방침에 동의합니다.")?>
								<a href="/member/privacy.php" target="_blank">[<?=getTransLangMsg("보기")?>]</a>
							</span>
						</label>
						<label class="inp_checkbox">
							<input type="checkbox" name="agree_3" id="agree_3" class="agree" value="Y">
							<span>
								<em class="c-red">(<?=getTransLangMsg("필수")?>)</em> <?=getTransLangMsg("청소년 보호정책에 동의합니다.")?>
								<a href="/member/youth.php" target="_blank">[<?=getTransLangMsg("보기")?>]</a>
							</span>
						</label>
					</div>
					<div class="btn-bot">
						<a href="javascript:;" class="btn-pk b red2 rv bdrs w100p" onclick="joinGo()"><?=getTransLangMsg("회원가입")?></a>
					</div>
					</form>
				</div>
			</section>
		</div>
	</div>
</div><!--//container -->

<script>
	$(function(){
		$("#all_check").click(function(){
			$(".agree").prop("checked", this.checked);
		})
		$(".agree").click(function(){
			var total_cnt = $(".agree").length;
			var check_cnt = $(".agree").filter(":checked").length;

			$("#all_check").prop("checked", total_cnt==check_cnt?true:false);
		})
	})
	function joinGo() {
		if (!$("#agree_1").is(":checked")) {
			alert("<?=getTransLangMsg("서비스 이용약관에 동의해주세요.")?>");
			return false;
		}
		if (!$("#agree_2").is(":checked")) {
			alert("<?=getTransLangMsg("개인정보처리방침에 동의해주세요.")?>");
			return false;
		}
		if (!$("#agree_3").is(":checked")) {
			alert("<?=getTransLangMsg("청소년 보호정책에 동의해주세요.")?>");
			return false;
		}

		AJ.ajaxForm($("#joinFrm"), "/member/join_proc.php", function(data) {
			if (data.result == 200) {
				location.replace("/member/join2.php");
			} else {
				alert(data.message);
				$("#"+data.id).focus();
			}
		});
	}
</script>

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>