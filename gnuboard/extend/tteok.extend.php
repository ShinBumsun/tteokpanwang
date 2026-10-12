<?php
/**
 * 떡판왕 공용 정의
 * /extend 안의 파일은 그누보드가 모든 페이지에서 자동으로 읽습니다.
 *
 * 관리자 → 떡판왕 사이트 에서 고치는 내용(시그니처 메뉴 · 전체 메뉴 · 푸드 갤러리 · 매장 정보)은
 * 모두 DB 에 있고, 메인 화면은 tteok_front_data() 가 그 내용을 읽어 그립니다.
 */
if (!defined('_GNUBOARD_')) exit;

define('G5_TTEOK_SIG_TABLE',     G5_TABLE_PREFIX.'tteok_sig');
define('G5_TTEOK_CAT_TABLE',     G5_TABLE_PREFIX.'tteok_cat');
define('G5_TTEOK_ITEM_TABLE',    G5_TABLE_PREFIX.'tteok_item');
define('G5_TTEOK_GALLERY_TABLE', G5_TABLE_PREFIX.'tteok_gallery');
define('G5_TTEOK_STORE_TABLE',   G5_TABLE_PREFIX.'tteok_store');
define('G5_TTEOK_CONF_TABLE',    G5_TABLE_PREFIX.'tteok_conf');

// 갤러리 칸 모양. 화면 그리드(4열)에서 차지하는 칸 수입니다.
$g5['tteok_gallery_types'] = array(
	't1' => '큰 정사각 (2×2칸)',
	't2' => '가로형 (2×1칸)',
	't3' => '세로형 (1×2칸)',
	''   => '작은 정사각 (1×1칸)',
);

function tteok_dir() { return G5_DATA_PATH.'/tteok'; }
function tteok_url() { return G5_DATA_URL.'/tteok'; }

/** 올린 그림 파일명 → 주소. 파일이 없으면 빈 값 */
function tteok_image_url($name)
{
	$name = trim((string)$name);
	if ($name === '' || !is_file(tteok_dir().'/'.$name)) return '';
	return tteok_url().'/'.rawurlencode($name).'?'.filemtime(tteok_dir().'/'.$name);
}


/* =========================================================
	표 만들기 + 처음 내용 채우기
	관리자 화면을 처음 열 때(또는 메인 화면이 표를 못 찾을 때) 한 번 돕니다.
	이미 있으면 아무 일도 하지 않습니다.
========================================================= */
function tteok_setup()
{
	static $done = false;
	if ($done) return;
	$done = true;

	$res = sql_query(" SHOW TABLES LIKE '".G5_TTEOK_CONF_TABLE."' ", false);
	if ($res && sql_num_rows($res)) return;

	$cs = " ENGINE=MyISAM DEFAULT CHARSET=".G5_DB_CHARSET." ";

	sql_query(" CREATE TABLE IF NOT EXISTS ".G5_TTEOK_SIG_TABLE." (
		ts_id         int(11)      NOT NULL AUTO_INCREMENT,
		ts_order      int(11)      NOT NULL DEFAULT 0,
		ts_label      varchar(50)  NOT NULL DEFAULT '',
		ts_name       varchar(100) NOT NULL DEFAULT '',
		ts_en         varchar(100) NOT NULL DEFAULT '',
		ts_desc       text         NOT NULL,
		ts_price_note varchar(50)  NOT NULL DEFAULT '',
		ts_price      int(11)      NOT NULL DEFAULT 0,
		ts_from       tinyint(4)   NOT NULL DEFAULT 0,
		ts_image      varchar(255) NOT NULL DEFAULT '',
		ts_use        tinyint(4)   NOT NULL DEFAULT 1,
		PRIMARY KEY (ts_id), KEY ts_order (ts_order)
	) $cs ", false);

	sql_query(" CREATE TABLE IF NOT EXISTS ".G5_TTEOK_CAT_TABLE." (
		tc_id    int(11)      NOT NULL AUTO_INCREMENT,
		tc_order int(11)      NOT NULL DEFAULT 0,
		tc_title varchar(50)  NOT NULL DEFAULT '',
		tc_en    varchar(50)  NOT NULL DEFAULT '',
		tc_note  varchar(50)  NOT NULL DEFAULT '',
		tc_use   tinyint(4)   NOT NULL DEFAULT 1,
		PRIMARY KEY (tc_id), KEY tc_order (tc_order)
	) $cs ", false);

	sql_query(" CREATE TABLE IF NOT EXISTS ".G5_TTEOK_ITEM_TABLE." (
		ti_id    int(11)      NOT NULL AUTO_INCREMENT,
		tc_id    int(11)      NOT NULL DEFAULT 0,
		ti_order int(11)      NOT NULL DEFAULT 0,
		ti_name  varchar(100) NOT NULL DEFAULT '',
		ti_sub   varchar(50)  NOT NULL DEFAULT '',
		ti_price int(11)      NOT NULL DEFAULT 0,
		ti_best  tinyint(4)   NOT NULL DEFAULT 0,
		ti_use   tinyint(4)   NOT NULL DEFAULT 1,
		PRIMARY KEY (ti_id), KEY tc_id (tc_id, ti_order)
	) $cs ", false);

	sql_query(" CREATE TABLE IF NOT EXISTS ".G5_TTEOK_GALLERY_TABLE." (
		tg_id      int(11)      NOT NULL AUTO_INCREMENT,
		tg_order   int(11)      NOT NULL DEFAULT 0,
		tg_type    varchar(10)  NOT NULL DEFAULT '',
		tg_caption varchar(100) NOT NULL DEFAULT '',
		tg_en      varchar(50)  NOT NULL DEFAULT '',
		tg_image   varchar(255) NOT NULL DEFAULT '',
		tg_use     tinyint(4)   NOT NULL DEFAULT 1,
		PRIMARY KEY (tg_id), KEY tg_order (tg_order)
	) $cs ", false);

	sql_query(" CREATE TABLE IF NOT EXISTS ".G5_TTEOK_STORE_TABLE." (
		st_id        int(11)      NOT NULL AUTO_INCREMENT,
		st_order     int(11)      NOT NULL DEFAULT 0,
		st_name      varchar(100) NOT NULL DEFAULT '',
		st_badge     varchar(50)  NOT NULL DEFAULT '',
		st_station   varchar(50)  NOT NULL DEFAULT '',
		st_line      varchar(20)  NOT NULL DEFAULT '',
		st_address   varchar(255) NOT NULL DEFAULT '',
		st_jibun     varchar(100) NOT NULL DEFAULT '',
		st_hours     varchar(100) NOT NULL DEFAULT '',
		st_tel       varchar(50)  NOT NULL DEFAULT '',
		st_instagram varchar(255) NOT NULL DEFAULT '',
		st_use       tinyint(4)   NOT NULL DEFAULT 1,
		PRIMARY KEY (st_id), KEY st_order (st_order)
	) $cs ", false);

	// conf 를 맨 마지막에 만든다. 이 표가 있으면 '설치 끝' 으로 보기 때문.
	sql_query(" CREATE TABLE IF NOT EXISTS ".G5_TTEOK_CONF_TABLE." (
		cf_key   varchar(50) NOT NULL DEFAULT '',
		cf_value text        NOT NULL,
		PRIMARY KEY (cf_key)
	) $cs ", false);

	tteok_seed();
}

/** 정적 사이트(js/data.js)에서 뽑아 둔 seed.json 으로 비어 있는 표를 채웁니다. */
function tteok_seed()
{
	$file = G5_THEME_PATH.'/data/seed.json';
	if (!defined('G5_THEME_PATH') || !is_file($file)) {
		$file = G5_PATH.'/'.G5_THEME_DIR.'/tteokpanwang/data/seed.json';
		if (!is_file($file)) return;
	}
	$seed = json_decode(file_get_contents($file), true);
	if (!is_array($seed)) return;

	$img_src = dirname(dirname($file)).'/img';
	if (!is_dir(tteok_dir())) @mkdir(tteok_dir(), G5_DIR_PERMISSION, true);

	// 테마에 들어 있는 사진을 data/tteok 로 복사해 두고 파일명만 표에 넣는다.
	$copy = function ($path) use ($img_src) {
		$name = basename((string)$path);
		if ($name === '' || !is_file($img_src.'/'.$name)) return '';
		if (!is_file(tteok_dir().'/'.$name)) {
			@copy($img_src.'/'.$name, tteok_dir().'/'.$name);
			@chmod(tteok_dir().'/'.$name, G5_FILE_PERMISSION);
		}
		return is_file(tteok_dir().'/'.$name) ? $name : '';
	};
	$e = function ($v) { return sql_escape_string((string)$v); };

	if (tteok_count(G5_TTEOK_SIG_TABLE) === 0 && !empty($seed['menu'])) {
		foreach ($seed['menu'] as $i => $m) {
			sql_query(" INSERT INTO ".G5_TTEOK_SIG_TABLE." SET
				ts_order = '".(($i + 1) * 10)."', ts_label = '".$e($m['label'])."', ts_name = '".$e($m['name'])."',
				ts_en = '".$e($m['en'])."', ts_desc = '".$e($m['desc'])."', ts_price_note = '".$e(isset($m['priceNote']) ? $m['priceNote'] : '')."',
				ts_price = '".(int)$m['price']."', ts_from = '".(!empty($m['from']) ? 1 : 0)."',
				ts_image = '".$e($copy(isset($m['image']) ? $m['image'] : ''))."', ts_use = 1 ", false);
		}
	}

	if (tteok_count(G5_TTEOK_CAT_TABLE) === 0 && !empty($seed['menuBoard'])) {
		foreach ($seed['menuBoard'] as $i => $g) {
			sql_query(" INSERT INTO ".G5_TTEOK_CAT_TABLE." SET
				tc_order = '".(($i + 1) * 10)."', tc_title = '".$e($g['title'])."', tc_en = '".$e($g['en'])."',
				tc_note = '".$e(isset($g['note']) ? $g['note'] : '')."', tc_use = 1 ", false);
			$tc_id = sql_insert_id();
			foreach ($g['items'] as $j => $it) {
				sql_query(" INSERT INTO ".G5_TTEOK_ITEM_TABLE." SET
					tc_id = '".(int)$tc_id."', ti_order = '".(($j + 1) * 10)."', ti_name = '".$e($it['name'])."',
					ti_sub = '".$e(isset($it['sub']) ? $it['sub'] : '')."', ti_price = '".(int)$it['price']."',
					ti_best = '".(!empty($it['best']) ? 1 : 0)."', ti_use = 1 ", false);
			}
		}
	}

	if (tteok_count(G5_TTEOK_GALLERY_TABLE) === 0 && !empty($seed['gallery'])) {
		foreach ($seed['gallery'] as $i => $g) {
			sql_query(" INSERT INTO ".G5_TTEOK_GALLERY_TABLE." SET
				tg_order = '".(($i + 1) * 10)."', tg_type = '".$e($g['type'])."', tg_caption = '".$e($g['caption'])."',
				tg_en = '".$e($g['en'])."', tg_image = '".$e($copy($g['image']))."', tg_use = 1 ", false);
		}
	}

	if (tteok_count(G5_TTEOK_STORE_TABLE) === 0 && !empty($seed['stores'])) {
		foreach ($seed['stores'] as $i => $s) {
			$v = function ($k) use ($s) { return isset($s[$k]) ? $s[$k] : ''; };
			sql_query(" INSERT INTO ".G5_TTEOK_STORE_TABLE." SET
				st_order = '".(($i + 1) * 10)."', st_name = '".$e($v('name'))."', st_badge = '".$e($v('badge'))."',
				st_station = '".$e($v('station'))."', st_line = '".$e($v('line'))."', st_address = '".$e($v('address'))."',
				st_jibun = '".$e($v('jibun'))."', st_hours = '".$e($v('hours'))."', st_tel = '".$e($v('tel'))."',
				st_instagram = '".$e($v('instagram'))."', st_use = 1 ", false);
		}
	}

	if (!empty($seed['brand'])) {
		foreach ($seed['brand'] as $k => $val) tteok_conf_set('brand_'.$k, $val);
	}
}

function tteok_count($table)
{
	$row = sql_fetch(" SELECT COUNT(*) AS cnt FROM $table ", false);
	return $row ? (int)$row['cnt'] : 0;
}

function tteok_conf($key, $default = '')
{
	$row = sql_fetch(" SELECT cf_value FROM ".G5_TTEOK_CONF_TABLE." WHERE cf_key = '".sql_escape_string($key)."' ", false);
	return $row ? $row['cf_value'] : $default;
}

function tteok_conf_set($key, $value)
{
	sql_query(" REPLACE INTO ".G5_TTEOK_CONF_TABLE." SET cf_key = '".sql_escape_string($key)."', cf_value = '".sql_escape_string((string)$value)."' ", false);
}


/* =========================================================
	그림 올리기 (관리자 저장 화면에서 사용)
	확장자를 믿지 않고 getimagesize 로 진짜 그림인지 본 뒤, 이름은 새로 짓는다.
	돌려주는 값 : 새 파일명 / 지우기 체크면 '' / 아무 변화 없으면 $old
========================================================= */
function tteok_upload_image($field, $prefix, $old)
{
	$up = isset($_FILES[$field]) ? $_FILES[$field] : null;
	if ($up && isset($up['tmp_name']) && $up['error'] === UPLOAD_ERR_OK && is_uploaded_file($up['tmp_name'])) {
		if ($up['size'] > 8 * 1024 * 1024) alert('사진은 8MB 까지 올릴 수 있습니다.');
		$info = @getimagesize($up['tmp_name']);
		$ext  = array(IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp');
		if (!$info || !isset($ext[$info[2]])) alert('jpg, png, webp 그림만 올릴 수 있습니다.');

		if (!is_dir(tteok_dir())) @mkdir(tteok_dir(), G5_DIR_PERMISSION, true);
		$name = $prefix.'_'.date('YmdHis').'_'.substr(md5(uniqid('', true)), 0, 6).'.'.$ext[$info[2]];
		if (!@move_uploaded_file($up['tmp_name'], tteok_dir().'/'.$name)) alert('사진을 저장하지 못했습니다.');
		@chmod(tteok_dir().'/'.$name, G5_FILE_PERMISSION);
		return $name;
	}
	if (isset($_POST[$field.'_del'])) return '';
	return $old;
}

/** 더 이상 어느 표에서도 쓰지 않는 그림 파일을 지웁니다. */
function tteok_discard_image($name)
{
	$name = trim((string)$name);
	if ($name === '' || strpos($name, '/') !== false) return;
	$q = sql_escape_string($name);
	$used = 0;
	$r = sql_fetch(" SELECT COUNT(*) AS cnt FROM ".G5_TTEOK_SIG_TABLE." WHERE ts_image = '$q' ", false);     if ($r) $used += (int)$r['cnt'];
	$r = sql_fetch(" SELECT COUNT(*) AS cnt FROM ".G5_TTEOK_GALLERY_TABLE." WHERE tg_image = '$q' ", false); if ($r) $used += (int)$r['cnt'];
	if ($used === 0 && is_file(tteok_dir().'/'.$name)) @unlink(tteok_dir().'/'.$name);
}

/**
 * 폼 값 꺼내기. 그누보드 common.php 가 $_POST 전체를 이미 sql_escape_string 해 두므로
 * 여기서 다시 이스케이프하지 않는다. (두 번 하면 따옴표가 \' 로 저장된다)
 */
function tteok_post($key, $i = null)
{
	if ($i === null) return isset($_POST[$key]) && !is_array($_POST[$key]) ? trim($_POST[$key]) : '';
	return isset($_POST[$key][$i]) && !is_array($_POST[$key][$i]) ? trim($_POST[$key][$i]) : '';
}

/** 관리자 목록에 찍는 작은 미리보기 */
function tteok_thumb($name, $w = 64, $h = 48)
{
	$url = tteok_image_url($name);
	if ($url === '') return '<span style="color:#c33">없음</span>';
	return '<img src="'.$url.'" alt="" style="width:'.$w.'px;height:'.$h.'px;object-fit:cover;border:1px solid #ccc;vertical-align:middle">';
}


/* =========================================================
	메인 화면용 데이터
	정적 사이트의 js/data.js(window.TPW_DATA)와 같은 모양으로 돌려줍니다.
	그래서 화면 스크립트(js/lib.js)는 정적/그누보드 어느 쪽이든 그대로 씁니다.
========================================================= */
function tteok_front_data()
{
	tteok_setup();

	$out = array(
		'brand'     => array('instagram' => tteok_conf('brand_instagram'), 'youtube' => tteok_conf('brand_youtube')),
		'stores'    => array(),
		'menu'      => array(),
		'menuBoard' => array(),
		'gallery'   => array(),
	);

	$res = sql_query(" SELECT * FROM ".G5_TTEOK_STORE_TABLE." WHERE st_use = 1 ORDER BY st_order, st_id ", false);
	while ($res && $r = sql_fetch_array($res)) {
		$out['stores'][] = array(
			'name' => $r['st_name'], 'badge' => $r['st_badge'], 'station' => $r['st_station'], 'line' => $r['st_line'],
			'address' => $r['st_address'], 'jibun' => $r['st_jibun'], 'hours' => $r['st_hours'], 'tel' => $r['st_tel'],
			'instagram' => $r['st_instagram'],
		);
	}

	$res = sql_query(" SELECT * FROM ".G5_TTEOK_SIG_TABLE." WHERE ts_use = 1 ORDER BY ts_order, ts_id ", false);
	while ($res && $r = sql_fetch_array($res)) {
		$out['menu'][] = array(
			'label' => $r['ts_label'], 'name' => $r['ts_name'], 'en' => $r['ts_en'], 'desc' => $r['ts_desc'],
			'priceNote' => $r['ts_price_note'], 'price' => (int)$r['ts_price'], 'from' => (bool)$r['ts_from'],
			'image' => tteok_image_url($r['ts_image']),
		);
	}

	$items = array();
	$res = sql_query(" SELECT * FROM ".G5_TTEOK_ITEM_TABLE." WHERE ti_use = 1 ORDER BY ti_order, ti_id ", false);
	while ($res && $r = sql_fetch_array($res)) {
		$it = array('name' => $r['ti_name'], 'price' => (int)$r['ti_price']);
		if ($r['ti_sub'] !== '') $it['sub'] = $r['ti_sub'];
		if ($r['ti_best']) $it['best'] = true;
		$items[(int)$r['tc_id']][] = $it;
	}
	$res = sql_query(" SELECT * FROM ".G5_TTEOK_CAT_TABLE." WHERE tc_use = 1 ORDER BY tc_order, tc_id ", false);
	while ($res && $r = sql_fetch_array($res)) {
		if (empty($items[(int)$r['tc_id']])) continue;   // 항목이 없는 분류는 화면에 내지 않는다
		$g = array('title' => $r['tc_title'], 'en' => $r['tc_en'], 'items' => $items[(int)$r['tc_id']]);
		if ($r['tc_note'] !== '') $g['note'] = $r['tc_note'];
		$out['menuBoard'][] = $g;
	}

	$res = sql_query(" SELECT * FROM ".G5_TTEOK_GALLERY_TABLE." WHERE tg_use = 1 ORDER BY tg_order, tg_id ", false);
	while ($res && $r = sql_fetch_array($res)) {
		$out['gallery'][] = array('type' => $r['tg_type'], 'caption' => $r['tg_caption'], 'en' => $r['tg_en'], 'image' => tteok_image_url($r['tg_image']));
	}

	return $out;
}

/** 검색엔진용 매장 정보 (schema.org Restaurant) */
function tteok_jsonld($data)
{
	$list = array();
	foreach ($data['stores'] as $s) {
		$row = array('@context' => 'https://schema.org', '@type' => 'Restaurant', 'name' => $s['name'], 'servesCuisine' => 'Tteokbokki');
		if ($s['tel'] !== '') $row['telephone'] = $s['tel'];
		if ($s['address'] !== '') $row['address'] = array('@type' => 'PostalAddress', 'streetAddress' => $s['address'], 'addressCountry' => 'KR');
		if ($s['instagram'] !== '') $row['sameAs'] = array($s['instagram']);
		$list[] = $row;
	}
	return json_encode($list, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
}


/* =========================================================
	관리자 정리
	떡판왕 관리 메뉴와 꼭 필요한 환경설정만 남기고 나머지 그누보드 메뉴는 숨깁니다.
	(그누보드 본체 파일은 지우지 않습니다. 주소로 직접 들어가면 여전히 열립니다.)
========================================================= */
$g5['tteok_admin_keep'] = array(
	'menu100' => array('100000', '100100', '100280'),   // 환경설정 : 기본환경설정 · 테마설정
	'menu960' => true,                                   // 떡판왕 사이트 전체
);

add_replace('admin_amenu', function ($amenu) {
	global $g5;
	foreach ($amenu as $k => $v) if (!isset($g5['tteok_admin_keep']['menu'.$k])) unset($amenu[$k]);
	return $amenu;
}, G5_HOOK_DEFAULT_PRIORITY, 1);

add_replace('admin_menu', function ($menu) {
	global $g5;
	foreach ($menu as $k => $rows) {
		if (!isset($g5['tteok_admin_keep'][$k])) { unset($menu[$k]); continue; }
		$keep = $g5['tteok_admin_keep'][$k];
		if ($keep === true) continue;
		$menu[$k] = array_values(array_filter($rows, function ($row) use ($keep) {
			return isset($row[0]) && in_array($row[0], $keep, true);
		}));
	}
	return $menu;
}, G5_HOOK_DEFAULT_PRIORITY, 1);

// 관리자 첫 화면(대시보드)은 떡판왕 관리로 바로 보낸다.
if (defined('G5_IS_ADMIN') && basename($_SERVER['SCRIPT_NAME']) === 'index.php'
	&& basename(dirname($_SERVER['SCRIPT_NAME'])) === G5_ADMIN_DIR) {
	header('Location: '.G5_ADMIN_URL.'/tteok_sig.php');
	exit;
}
