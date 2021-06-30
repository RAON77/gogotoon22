<? include "../inc/config.php" ?>
<?
	setSession("RETURN_URL", NOW_URL);
	if (!isUser()) fnMsgGo(500, "", "/member/login.php?return_flag=Y", "");

	//사용자 정보 불러오기
	$user_view = $cls_member->user_view($MEM_USR['usr_idx']);
	if ($user_view == false) fnMsgGo(500, "", "/member/logout.php", "");

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
					<div class="hd_titbox1">
						<h2 class="title1"><?=getTransLangMsg("번역회원 신청")?></h2>
					</div>
					<div class="tbl_basic">
						<form naem="saveFrm" id="saveFrm" method="post" enctype="multipart/form-data">
						<table class="view">
							<caption>신청</caption>
							<colgroup>
								<col class="wid1">
								<col>
							</colgroup>
							<tbody>
								<tr>
									<th><?=getTransLangMsg("이메일")?></th>
									<td><?=$user_view['usr_email']?></td>
								</tr>
								<tr>
									<th><?=getTransLangMsg("닉네임")?></th>
									<td><?=$user_view['nick_name']?></td>
								</tr>
								<tr>
									<th><?=getTransLangMsg("프로필 사진")?></th>
									<td>
										<?if ($user_view['trans_status']=='1' || $user_view['trans_status']=='2') {?>
											<a href="javascript:;" onclick="imgPreviwePopupOpen('<?=filePathCheck("/upload/member/profile/".getUpfileName($user_view['trans_up_file']))?>')">
												<img src="<?=filePathCheck("/upload/member/profile/".getUpfileName($user_view['trans_up_file']))?>" style="max-width:100px; max-height:100px;" />
											</a>
										<?} else {?>
											<div class="filebox" >
												<input type="file" name="up_file" id="up_file" class="upload-hidden" upload-size="2" upload-type="img">
												<label for="up_file"><?=getTransLangMsg("파일")?></label>
												<input type="text" class="inp_txt upload-name" placeholder="<?=getTransLangMsg("이미지파일만 첨부 가능합니다.")?>" readonly>
											</div>

											<?if ($user_view['trans_up_file'] != '') {?>
												<p class="mt5">
													<a href="javascript:;" onclick="imgPreviwePopupOpen('<?=filePathCheck("/upload/member/profile/".getUpfileName($user_view['trans_up_file']))?>')">
														<img src="<?=filePathCheck("/upload/member/profile/".getUpfileName($user_view['trans_up_file']))?>" style="max-width:100px; max-height:100px;" />
													</a>
												</p>
											<?}?>
										<?}?>
									</td>
								</tr>
								<tr>
									<th><?=getTransLangMsg("언어")?></th>
									<td>
										<?if ($user_view['trans_status']=='1' || $user_view['trans_status']=='2') {?>
											<?=getSiteLangName($user_view['trans_lang'], 'name', SITE_SAVE_LANG)?>

											<?for ($i=1; $i<count($lang_list); $i++) {?>
												<label class="inp_checkbox mr10" style="display:none">
													<input type="checkbox" name="service_lang[]" id="service_lang_<?=$i?>" value="<?=$lang_list[$i]['code']?>" class="service_lang" <?=chkCompare($user_view['trans_lang'], $lang_list[$i]['code'], 'checked')?>>
													<span><?=$lang_list[$i]['name']?></span>
												</label>
											<?}?>
										<?} else {?>
											<?for ($i=1; $i<count($lang_list); $i++) {?>
												<label class="inp_checkbox mr10">
													<input type="checkbox" name="service_lang[]" id="service_lang_<?=$i?>" value="<?=$lang_list[$i]['code']?>" class="service_lang" <?=chkCompare($user_view['trans_lang'], $lang_list[$i]['code'], 'checked')?>>
													<span><?=$lang_list[$i]['name']?></span>
												</label>
											<?}?>
										<?}?>
									</td>
								</tr>
								<tr>
									<th><?=getTransLangMsg("페이팔 ID")?></th>
									<td>
										<?if ($user_view['trans_status']=='1') {?>
											<?=$user_view['trans_paypal_id']?>
										<?} else {?>
											<input type="text" name="paypal_id" id="paypal_id" value="<?=$user_view['trans_paypal_id']?>" class="inp_txt wid1" maxlength="50">
										<?}?>
									</td>
								</tr>
								<?if ($user_view['trans_status']!='') {?>
									<tr>
										<th><?=getTransLangMsg("승인결과")?></th>
										<td>
											<?
												if ($user_view['trans_status']=='1') {
													echo getTransLangMsg("승인대기");
													echo " | ". formatDates($user_view['trans_request_dt'], "Y.m.d H:i:s");
												} else if ($user_view['trans_status'] == 2) {
													echo getTransLangMsg("승인완료");
													echo " | ". formatDates($user_view['trans_complete_dt'], "Y.m.d H:i:s");
												} else if ($user_view['trans_status'] == 3) {
													echo getTransLangMsg("미승인");
													echo " | ". formatDates($user_view['trans_complete_dt'], "Y.m.d H:i:s");
													echo " | ". getTransLangMsg("사유") .": ". $user_view['trans_status_memo'];
												}
											?>
										</td>
									</tr>
								<?}?>
							</tbody>
						</table>
						</form>
					</div>

					<div class="btn-bot ta-c">
						<?if ($user_view['trans_status']=='1') {?>
							<a href="javascript:;" class="btn-pk b white rv mw100p" onclick="alert('<?=getTransLangMsg("승인 대기중 입니다.")?>')"><span><?=getTransLangMsg("승인 대기중")?></span></a>
						<?} else if ($user_view['trans_status']=='2') {?>
							<a href="javascript:;" class="btn-pk b red rv mw100p" onclick="saveGo()"><span><?=getTransLangMsg("저장")?></span></a>
						<?} else if ($user_view['trans_status']=='3') {?>
							<a href="javascript:;" class="btn-pk b gray rv mw100p" onclick="saveGo()"><span><?=getTransLangMsg("재신청")?></span></a>
						<?} else {?>
							<a href="javascript:;" class="btn-pk b red rv mw100p" onclick="saveGo()"><span><?=getTransLangMsg("신청")?></span></a>
						<?}?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!--//container -->

<script>
	//회원정보 수정
	function saveGo() {
		AJ.ajaxForm($("#saveFrm"), "mypage_leg_proc.php", function(data) {
			if (data.result == 200) {
				<?if ($user_view['trans_status']=='2') {?>
					alert("<?=getTransLangMsg("저장 처리가 완료되었습니다.")?>");
				<?} else {?>
					alert("<?=getTransLangMsg("신청이 완료되었습니다.")?>");
				<?}?>

				location.reload();
			} else {
				alert(data.message);
			}
		});
	}
</script>

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>