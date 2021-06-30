<? include "../inc/config.php" ?>
<?
	//if (!chkReferer()) fnMsgGo(500, getTransLangMsg("잘못된 접근 입니다."), "/", "");
	if (isUser()) fnMsgGo(501, "", "/", "");

	$token = chkReqRpl("token", "", "max", "GET", "STR");

	$cls_member = new CLS_MEMBER;
	$cls_jwt = new CLS_JWT;

	$token_data = $cls_jwt->dehashing($token, $error_msg);
	if ($token_data == false) fnMsgGo(502, $error_msg, "/", "");

	//이메일 아이디 체크
	if (!$cls_member->is_login_check("", $token_data['usr_email'], "", $usr_idx)) fnMsgGo(503, getTransLangMsg("잘못된 요청 정보 입니다.\n고객센터로 문의해주세요."), "/", "");

    //사용자 정보 불러오기
    $user_view = $cls_member->user_view($usr_idx);
	if ($user_view == false) fnMsgGo(504, getTransLangMsg("일치하는 사용자 데이터가 없습니다."), "/", "");

	//탈퇴회원 확인
	if (isStrpos("80,81", $user_view['usr_gubun'])) fnMsgGo(505, getTransLangMsg("탈퇴 처리된 사용자 입니다.\n고객센터에 문의 주세요."), "/", "");

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
					<form name="changeFrm" id="changeFrm" method="post">
					<input type="hidden" name="token" value="<?=$token?>" />
					<div class="hd_titbox1 ta-c pr-mb1">
						<h2 class="title1 pr-mb2"><?=getTransLangMsg("비밀번호 재설정")?></h2>
						<p class="t1 c-red"><?=getTransLangMsg("아래의 입력란에 새로운 비밀번호를 입력해 주십시오.")?></p>
					</div>
					<input type="password" name="new_passwd" id="new_passwd" class="inp_txt w100p mb10" placeholder="<?=getTransLangMsg("새로운 비밀번호")?>">
					<input type="password" name="chk_passwd" id="chk_passwd" class="inp_txt w100p mb20" placeholder="<?=getTransLangMsg("새로운 비밀번호 재입력")?>">
					<a href="javascript:;" class="btn-pk b red2 rv bdrs w100p" onclick="changePasswdGo()"><?=getTransLangMsg("확인")?></a>
					</form>

					<p class="t1 c-black ta-c mt1"><?=getTransLangMsg("비밀번호는 영문,숫자,특수문자 혼합 8자 이상 입력해주세요.")?></p>
				</div>
			</section>
		</div>
	</div>
</div><!--//container -->

<script>
	function changePasswdGo() {
		AJ.ajaxForm($("#changeFrm"), "find_pw_change_proc.php", function(data) {
			if (data.result == 200) {
				alert("<?=getTransLangMsg("비밀번호가 변경되었습니다.\\n로그인을 해주세요.")?>");

				location.replace("login.php");
			} else {
				alert(data.message);
				$("#"+data.id).focus();
			}
		});
	}
</script>

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>