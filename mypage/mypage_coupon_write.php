<? include "../inc/config.php" ?>
<?
	setSession("RETURN_URL", NOW_URL);
	if (!isUser()) fnMsgGo(500, "", "/member/login.php?return_flag=Y", "");

	$pageNum = "7";
	$pageSubNum = "3";
?>
<? include "../inc/top.php" ?>
<? include "../inc/header.php" ?>

<div id="container" class="container mypage">
	<div class="contents">
		<div class="inr-c">
			<? include "top.php" ?>

			<div class="my_cont">
				<div class="tab ty4">
					<ul>
						<li><a href="mypage_coupon_list.php"><span><?=getTransLangMsg("쿠폰 내역")?></span></a></li>
						<li class="on"><a href="mypage_coupon_write.php"><span><?=getTransLangMsg("쿠폰 등록")?></span></a></li>
					</ul>
				</div>
				<div class="inner">
                    <div class="area_coupon pr-mb2">
                        <input type="text" name="coupon_num" id="coupon_num" class="inp_txt w100p" maxlength="10" placeholder="<?=getTransLangMsg("쿠폰 번호를 입력해 주십시오.")?>">
                        <button type="button" class="btn-pk b red rv w100p" onclick="couponUseGo()"><span><?=getTransLangMsg("확인")?></span></button>
                    </div>

                    <div class="lst_txt1">
                        <p class="h1"><?=getTransLangMsg("유의사항")?></p>
                        <ol>
                            <li><?=getTransLangMsg("쿠폰은 무료 회원가입 및 로그인 후 사용이 가능합니다.")?></li>
                            <li><?=getTransLangMsg("지급된 포인트로 모든 콘텐츠 이용이 가능합니다.")?></li>
                            <li><?=getTransLangMsg("쿠폰 등록은 명의 당 1회 참여가 가능합니다. (새로운 아이디로 가입하시더라도 명의가 동일하다면 중복 참여로 인해 쿠폰 등록이 불가합니다.)")?></li>
                            <li><?=getTransLangMsg("쿠폰 등록 및 사용 정책은 고고툰 정책에 따라 변경될 수 있습니다.")?></li>
                            <li><?=getTransLangMsg("쿠폰 등록 및 이용 관련 문의사항은 고객센터 및 사이트 내 문의하기 게시판에 문의하여 주시기 바랍니다.")?></li>
                            <li><?=getTransLangMsg("고고툰 정책에 따라 쿠폰 등록 가능 횟수가 변경될 수 있습니다.")?></li>
                        </ol>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div><!--//container -->

<script>
	$(function(){
		$("#coupon_num").blur(function(){
			var this_val = $(this).val();
				this_val = this_val.replace(/\s/gi, "");

			$(this).val(this_val);
		})
	})

	//쿠폰 사용 저장
	function couponUseGo() {
		AJ.callAjax("mypage_coupon_write_proc.php", {"coupon_num": $("#coupon_num").val()}, function(data){
			if (data.result == 200) {
				alert("정상적으로 등록되었습니다.");
				location.reload();
			} else {
				alert(data.message);
			}
		});
	}
</script>

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>