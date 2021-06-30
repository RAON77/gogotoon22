<? include "../inc/config.php" ?>
<?
	$return_flag = chkReqRpl("return_flag", "", 1, "", "STR");
	$return_url  = getSession("RETURN_URL");
	if (chkBlank($return_url)) $return_url = "/";
	if ($return_flag != 'Y') $return_url = "/";

	if (isUser()) fnMsgGo(500, "", "/", "");

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
					<h2 class="tit blind"><span><?=getTransLangMsg("로그인")?></span></h2>
				</header>
				<div class="p_cont">
					<form name="loginFrm" id="loginFrm" method="post">
					<input type="hidden" name="return_flag" id="return_flag" value="<?=$return_flag?>" />
					<div class="img ta-c pr-mb2"><img src="/images/common/logo.png" alt="<?=getTransLangMsg("고고툰")?>"></div>
					<div class="hd_titbox1">
						<h2 class="title1 ta-c"><?=getTransLangMsg("SNS 가입 / 로그인")?></h2>
					</div>
					<ul class="list1 pr-mb2">
						<li><a href="/member/sns/oauth.php?sns=google&return_flag=<?=$return_flag?>"><span class="i-set i_sns1"><?=getTransLangMsg("구글")?></span></a></li>
						<!-- <li><a href="javascript:;" onclick="alert('<?=getTransLangMsg("준비중 입니다.")?>')"><span class="i-set i_sns2"><?=getTransLangMsg("애플")?></span></a></li> -->
						<li><a href="/member/sns/oauth.php?sns=facebook&return_flag=<?=$return_flag?>"><span class="i-set i_sns3"><?=getTransLangMsg("페이스북")?></span></a></li>
						<!-- <li><a href="javascript:;" onclick="alert('<?=getTransLangMsg("준비중 입니다.")?>')"><span class="i-set i_sns4"><?=getTransLangMsg("위챗")?></span></a></li> -->
					</ul>
					<div class="hd_titbox1">
						<h2 class="title1 ta-c"><?=getTransLangMsg("이메일 로그인")?></h2>
					</div>
					<input type="text" name="login_email" id="login_email" class="inp_txt w100p" maxlength="50" placeholder="<?=getTransLangMsg("이메일 아이디를 입력해주세요.")?>">
					<input type="password" name="login_passwd" id="login_passwd" class="inp_txt w100p" maxlength="20" placeholder="<?=getTransLangMsg("비밀번호를 입력해주세요.")?>">
					<label class="inp_checkbox">
						<input type="checkbox" name="auto_login" id="auto_login" value="Y"><span><?=getTransLangMsg("자동로그인")?></span>
						</label>
					<div class="btn-bot ta-c pr-mb2 mbtn_ty1">
						<a href="/member/join.php" class="btn-pk n gray rv bdrs"><?=getTransLangMsg("이메일 회원가입")?></a>
						<a href="javascript:;" class="btn-pk n red2 rv bdrs" onclick="loginGo()"><?=getTransLangMsg("로그인")?></a>
					</div>
					<div class="ta-c pb20">
						<a href="/member/find_id.php" class="t1 c-black"><?=getTransLangMsg("비밀번호 찾기")?></a>
					</div>
					</form>
				</div>
			</section>
		</div>
	</div>
</div><!--//container -->

<script>
	function loginGo() {
		AJ.ajaxForm($("#loginFrm"), "/member/login_proc.php", function(data) {
			if (data.result == 200) {
				location.replace("<?=$return_url?>");
			} else {
				alert(data.message);
			}
		});
	}

	function snsLogin(sns) {
		popupOpen("/member/sns/oauth.php?sns="+sns, "sns_pop", 600, 600);
	}
</script>
<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>