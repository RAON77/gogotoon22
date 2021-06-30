<? include "../inc/config.php" ?>
<?
	setSession("RETURN_URL", NOW_URL);
	if (!isUser()) fnMsgGo(500, "", "/member/login.php?return_flag=Y", "");

	//사용자 정보 불러오기
	$user_view = $cls_member->user_view($MEM_USR['usr_idx']);
	if ($user_view == false) fnMsgGo(501, "", "/member/logout.php", "");

    //언어설정 목록
    $lang_list = getSiteLangList(SITE_SAVE_LANG);

	$pageNum = "7";
	$pageSubNum = "4";
?>
<? include "../inc/top.php" ?>
<? include "../inc/header.php" ?>

<div id="container" class="container mypage">
	<div class="contents">
		<div class="inr-c">
			<? include "top.php" ?>

			<div class="my_cont">
				<div class="inner">
					<div class="tbl_basic">
						<form name="saveFrm" id="saveFrm" method="post">
						<table class="view">
							<caption><?=getTransLangMsg("정보수정")?></caption>
							<colgroup>
								<col class="wid1">
								<col>
							</colgroup>
							<tbody>
								<tr>
									<th><?=iif($user_view['reg_gubun']==20, getTransLangMsg("이메일 계정 연결"), getTransLangMsg("이메일"))?></th>
									<td>
										<?if ($user_view['reg_gubun'] == 20) {?>
											<?if (!chkBlank($user_view['usr_pwd'])) {?>
												<?=$user_view['usr_email']?>
												<a href="javascript:;" class="a_link ml10 " onclick="emailChangePopup();"><?=getTransLangMsg("이메일 계정 변경")?></a>
												<a href="javascript:;" class="c-black ml10" onclick="passwdChangePopup('change');"><strong>[<?=getTransLangMsg("비밀번호 설정")?>]</strong></a>
											<?} else {?>
												<a href="javascript:;" class="c-black ml10" onclick="passwdChangePopup('init');"><strong>[<?=getTransLangMsg("비밀번호 설정")?>]</strong></a>
											<?}?>
										<?} else {?>
											<?=$user_view['usr_email']?>
											<?if ($user_view['reg_gubun'] == 30) {?>
												<!-- <a href="javascript:;" class="a_link ml10 " onclick="emailChangePopup();"><?=getTransLangMsg("이메일 계정 변경")?></a> -->
											<?}?>
											<a href="javascript:;" class="c-black ml10" onclick="passwdChangePopup('change');"><strong>[<?=getTransLangMsg("비밀번호 설정")?>]</strong></a>
										<?}?>
									</td>
								</tr>
								<tr>
									<th><?=getTransLangMsg("SNS 계정")?></th>
									<td>
										<?if ($user_view['reg_gubun']==20) {?>
											<span class="i-aft i_<?=$user_view['sns_gubun']?>"><?=getTransLangMsg("연결")?></span>
										<?} else {?>
											<?if (!chkBlank($user_view['sns_gubun'])) {?>
												<a href="javascript:;" class="mr20" onclick="snsUnconnGo('<?=$user_view['sns_gubun']?>')">
													<span class="i-aft i_<?=$user_view['sns_gubun']?>"><?=getTransLangMsg("연결해제")?></span>
													<span class="ml10 c-gray">[연결일: <?=formatDates($user_view['sns_date'], "Y.m.d")?>]</span>
												</a>
											<?} else {?>
												<a href="/mypage/sns/oauth.php?sns=google" class="mr20"><span class="i-aft i_google"><?=getTransLangMsg("연결")?></span></a>
												<!-- <a href="javascript:;" class="mr20" onclick="alert('<?=getTransLangMsg("준비중 입니다.")?>')"><span class="i-aft i_apple"><?=getTransLangMsg("연결")?></span></a> -->
												<a href="/mypage/sns/oauth.php?sns=facebook" class="mr20"><span class="i-aft i_facebook"><?=getTransLangMsg("연결")?></span></a>
												<!-- <a href="javascript:;" class="mr20" onclick="alert('<?=getTransLangMsg("준비중 입니다.")?>')"><span class="i-aft i_chat"><?=getTransLangMsg("연결")?></span></a> -->
											<?}?>
										<?}?>
									</td>
								</tr>
								<tr>
									<th><?=getTransLangMsg("닉네임")?></th>
									<td>
										<div class="inp_btn">
											<input type="text" name="nick_name" id="nick_name" value="<?=$user_view['nick_name']?>" class="inp_txt wid1" maxlength="20">
										</div>
									</td>
								</tr>
								<tr>
									<th><?=getTransLangMsg("성별")?></th>
									<td>
										<div class="inp_radiotx">
											<label class="inp_radio"><input type="radio" name="gender" id="gender_1" value="M" <?=chkCompare($user_view['gender'], "M", "checked")?>><span><?=getTransLangMsg("남자")?></span></label>
											<label class="inp_radio"><input type="radio" name="gender" id="gender_2" value="F" <?=chkCompare($user_view['gender'], "F", "checked")?> ><span><?=getTransLangMsg("여자")?></span></label>
										</div>
									</td>
								</tr>
								<tr>
									<th><?=getTransLangMsg("기본언어설정")?></th>
									<td>
										<select name="default_lang" id="default_lang" class="select1 wid1">
											<?for ($i=0; $i<count($lang_list); $i++) {?>
												<option value="<?=$lang_list[$i]['code']?>" <?=chkCompare($user_view['default_lang'], $lang_list[$i]['code'], "selected")?>><?=$lang_list[$i]['name']?></option>
											<?}?>
										</select>
									</td>
								</tr>
								<tr>
									<th><?=getTransLangMsg("알림설정")?></th>
									<td>
										<label class="inp_checkbox mr20"><input type="checkbox" name="notice_flag" id="notice_flag" value="Y" <?=chkCompare($user_view['recv_notice_flag'], "Y", "checked")?>><span><?=getTransLangMsg("공지사항 및 이벤트 정보 알림")?></span></label>
										<label class="inp_checkbox"><input type="checkbox" name="webtoon_flag" id="webtoon_flag" value="Y" <?=chkCompare($user_view['recv_webtoon_flag'], "Y", "checked")?>><span><?=getTransLangMsg("구독 회차 업데이트 알림")?></span></label>
									</td>
								</tr>
								<tr>
									<th><?=getTransLangMsg("번역회원")?></th>
									<td>
										<a href="mypage_leg.php" class="c-black"><strong>[<?=getTransLangMsg("번역회원 설정")?>]</strong></a>
									</td>
								</tr>
							</tbody>
						</table>
						</form>
					</div>
					<div class="ta-r mt15">
						<a href="javascript:;" class="c-black" onclick="withdrawalPopup()"><?=getTransLangMsg("회원탈퇴")?></a>
					</div>

					<div class="btn-bot ta-c mbtn_ty1 wid1">
						<a href="javascript:;" class="btn-pk b red rv" onclick="saveGo()"><span><?=getTransLangMsg("저장")?></span></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!--//container -->

<!-- 비밀번호 설정 -->
<div id="popPw" class="layerPopup pop_pw"></div>

<!-- 이메일 계정 변경 -->
<div id="popEmailChange" class="layerPopup pop_set"></div>

<!-- 이메일 -->
<div id="popEmailAuth" class="layerPopup pop_email"></div>

<!-- 회원탈퇴 -->
<div id="popWithdrawal" class="layerPopup pop_withdrawal"></div>

<script>
	//비밀번호 변경
	function passwdChangePopup(gubun) {
		AJ.callAjax("popup_passwd_change.php", {"gubun": gubun}, function(data){
			$("#popPw").html(data);
			openLayerPopup('popPw');
		}, "html");
	}

	//이메일 계정 변경
	function emailChangePopup() {
		AJ.callAjax("popup_email_change.php", null, function(data){
			$("#popEmailChange").html(data);
			openLayerPopup('popEmailChange');
		}, "html");
	}

	//이메일 계정 변경 인증
	function emailAuthPopup(token) {
		AJ.callAjax("popup_email_auth.php", {"token": token}, function(data){
			$("#popEmailAuth").html(data);
			openLayerPopup('popEmailAuth');
		}, "html");
	}

	//탈퇴신청
	function withdrawalPopup(token) {
		AJ.callAjax("popup_withdrawal.php", null, function(data){
			$("#popWithdrawal").html(data);
			openLayerPopup('popWithdrawal');
		}, "html");
	}

	//회원정보 수정
	function saveGo() {
		AJ.ajaxForm($("#saveFrm"), "mypage_info_proc.php", function(data) {
			if (data.result == 200) {
				alert("<?=getTransLangMsg("저장 처리가 완료되었습니다.")?>");

				location.reload();
			} else {
				alert(data.message);
				$("#"+data.id).focus();
			}
		});
	}

	//SNS 계정 연결 해제
	function snsUnconnGo(gubun) {
		AJ.callAjax("__sns_unconn_proc.php", {"gubun": gubun}, function(data){
			if (data.result == 200) {
				location.reload();
			} else {
				alert(data.message);
			}
		});
	}
</script>

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>