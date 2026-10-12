<?php
$sub_menu = '960400';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');
tteok_setup();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	check_demo();
	auth_check_menu($auth, $sub_menu, 'w');
	check_admin_token();
	$act = isset($_POST['act_button']) ? $_POST['act_button'] : '';

	// SNS 저장 ($_POST 는 이미 이스케이프돼 있으므로 원래 값으로 되돌려 저장 함수에 넘긴다)
	if ($act === 'SNS 저장') {
		tteok_conf_set('brand_instagram', stripslashes(tteok_post('brand_instagram')));
		tteok_conf_set('brand_youtube',   stripslashes(tteok_post('brand_youtube')));
		goto_url('./tteok_store.php');
	}

	$chk = isset($_POST['chk']) ? (array)$_POST['chk'] : array();
	if (!count($chk)) alert('처리할 매장을 선택해 주세요.');
	foreach ($chk as $k) {
		$k  = (int)$k;
		$id = (int)tteok_post('st_id', $k);
		if (!$id) continue;
		if ($act === '선택삭제') {
			sql_query(" DELETE FROM ".G5_TTEOK_STORE_TABLE." WHERE st_id = '$id' ");
		} else {
			sql_query(" UPDATE ".G5_TTEOK_STORE_TABLE." SET
				st_order = '".(int)tteok_post('st_order', $k)."',
				st_use   = '".(isset($_POST['st_use'][$k]) ? 1 : 0)."'
				WHERE st_id = '$id' ");
		}
	}
	goto_url('./tteok_store.php');
}

$result = sql_query(" SELECT * FROM ".G5_TTEOK_STORE_TABLE." ORDER BY st_order, st_id ");

$g5['title'] = '매장 정보';
include_once(G5_ADMIN_PATH.'/admin.head.php');
?>

<div class="local_desc01 local_desc">
	<p>메인 화면 <b>'오시는 길'</b> 카드와 맨 아래 푸터에 나오는 매장 정보입니다. 매장을 추가하면 카드가 자동으로 늘어납니다. (PC 에서 한 줄에 3개)</p>
	<p>네이버 지도·카카오맵 버튼은 <b>주소 + 떡판왕</b> 으로 검색하는 링크가 자동으로 만들어집니다.</p>
</div>

<div class="local_ov01 local_ov">
	<a href="./tteok_store_form.php" class="ov_listall">새 매장 등록</a>
</div>

<form name="flist" method="post" onsubmit="return tteok_list_submit(this);">
<input type="hidden" name="token" value="">

<div class="tbl_head01 tbl_wrap">
	<table>
	<caption>매장 목록</caption>
	<thead>
	<tr>
		<th scope="col"><input type="checkbox" onclick="tteok_check_all(this)" aria-label="전체선택"></th>
		<th scope="col">매장명</th>
		<th scope="col">주소</th>
		<th scope="col">영업시간</th>
		<th scope="col">전화</th>
		<th scope="col">순서</th>
		<th scope="col">노출</th>
		<th scope="col">관리</th>
	</tr>
	</thead>
	<tbody>
	<?php for ($i = 0; $row = sql_fetch_array($result); $i++) { ?>
	<tr class="bg<?php echo $i % 2 ?>">
		<td class="td_chk">
			<input type="hidden" name="st_id[<?php echo $i ?>]" value="<?php echo $row['st_id'] ?>">
			<input type="checkbox" name="chk[]" value="<?php echo $i ?>" aria-label="선택">
		</td>
		<td class="td_left"><a href="./tteok_store_form.php?w=u&amp;st_id=<?php echo $row['st_id'] ?>"><b><?php echo get_text($row['st_name']) ?></b></a><br><span style="color:#999"><?php echo get_text($row['st_badge']) ?></span></td>
		<td class="td_left"><?php echo $row['st_address'] !== '' ? get_text($row['st_address']) : '<span style="color:#c33">미입력</span>' ?></td>
		<td><?php echo get_text($row['st_hours']) ?></td>
		<td><?php echo get_text($row['st_tel']) ?></td>
		<td class="td_num"><input type="text" name="st_order[<?php echo $i ?>]" value="<?php echo (int)$row['st_order'] ?>" class="frm_input" size="4"></td>
		<td class="td_chk"><input type="checkbox" name="st_use[<?php echo $i ?>]" value="1"<?php echo $row['st_use'] ? ' checked' : '' ?>></td>
		<td class="td_mng td_mng_s"><a href="./tteok_store_form.php?w=u&amp;st_id=<?php echo $row['st_id'] ?>" class="btn btn_03">수정</a></td>
	</tr>
	<?php }
	if ($i === 0) echo '<tr><td colspan="8" class="empty_table">등록된 매장이 없습니다.</td></tr>'; ?>
	</tbody>
	</table>
</div>

<div class="btn_fixed_top">
	<input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_03">
	<input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
	<a href="./tteok_store_form.php" class="btn btn_01">새 매장 등록</a>
</div>
</form>

<h2 class="h2_frm">SNS</h2>
<form name="fsns" method="post">
<input type="hidden" name="token" value="">
<div class="tbl_frm01 tbl_wrap">
	<table>
	<caption>SNS 주소</caption>
	<colgroup><col class="grid_4"><col></colgroup>
	<tbody>
	<tr>
		<th scope="row"><label for="brand_instagram">인스타그램</label></th>
		<td><input type="url" name="brand_instagram" value="<?php echo get_text(tteok_conf('brand_instagram')) ?>" id="brand_instagram" class="frm_input" size="60" placeholder="https://www.instagram.com/..."></td>
	</tr>
	<tr>
		<th scope="row"><label for="brand_youtube">유튜브</label></th>
		<td><input type="url" name="brand_youtube" value="<?php echo get_text(tteok_conf('brand_youtube')) ?>" id="brand_youtube" class="frm_input" size="60" placeholder="https://www.youtube.com/..."></td>
	</tr>
	</tbody>
	</table>
	<p class="frm_info" style="margin:8px 0">맨 아래 푸터에 링크로 나옵니다. 비워 두면 해당 SNS 는 나오지 않습니다.</p>
	<input type="submit" name="act_button" value="SNS 저장" class="btn btn_submit">
</div>
</form>

<?php
include_once(G5_ADMIN_PATH.'/tteok_admin.inc.php');
include_once(G5_ADMIN_PATH.'/admin.tail.php');
