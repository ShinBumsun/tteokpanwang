#!/usr/bin/env python3
"""
정적 사이트(루트의 index.html · css · js · images) → 그누보드 테마(gnuboard/theme/tteokpanwang)

화면은 정적 사이트 하나만 고치고 이 스크립트로 테마를 다시 만듭니다.
다른 점은 딱 두 가지입니다.
  1) js/data.js 대신 DB 내용을 window.TPW_DATA 로 찍습니다. (관리자에서 고친 내용)
  2) 주소(OG · canonical · 이미지 경로)를 그누보드 기준으로 바꿉니다.

실행 : python3 tools/build_theme.py
"""
import json, os, re, shutil, subprocess, sys

ROOT  = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
THEME = os.path.join(ROOT, 'gnuboard', 'theme', 'tteokpanwang')
STATIC_URL = 'https://shinbumsun.github.io/tteokpanwang/'

T = '<?php echo G5_THEME_URL ?>'


def copytree(src, dst, pattern=None):
	os.makedirs(dst, exist_ok=True)
	for name in sorted(os.listdir(src)):
		if name.startswith('.') or (pattern and not re.search(pattern, name)):
			continue
		shutil.copy2(os.path.join(src, name), os.path.join(dst, name))


def main():
	for sub in ('css', 'js', 'img', 'data', 'mobile'):
		d = os.path.join(THEME, sub)
		if os.path.isdir(d):
			shutil.rmtree(d)

	copytree(os.path.join(ROOT, 'css'), os.path.join(THEME, 'css'))
	copytree(os.path.join(ROOT, 'js'), os.path.join(THEME, 'js'), r'^(?!data\.js$).*\.js$')
	copytree(os.path.join(ROOT, 'images'), os.path.join(THEME, 'img'))

	# 처음 설치할 때 DB 를 채울 씨앗 : js/data.js 를 그대로 JSON 으로 뽑는다
	js = "global.window={};require(process.argv[1]);process.stdout.write(JSON.stringify(window.TPW_DATA))"
	seed = subprocess.check_output(['node', '-e', js, os.path.join(ROOT, 'js', 'data.js')]).decode('utf-8')
	os.makedirs(os.path.join(THEME, 'data'), exist_ok=True)
	with open(os.path.join(THEME, 'data', 'seed.json'), 'w', encoding='utf-8') as f:
		json.dump(json.loads(seed), f, ensure_ascii=False, indent='\t')

	html = open(os.path.join(ROOT, 'index.html'), encoding='utf-8').read()

	# 경로
	html = html.replace('./css/', T + '/css/').replace('./js/', T + '/js/').replace('./images/', T + '/img/')
	html = html.replace(STATIC_URL + 'images/', T + '/img/')
	html = html.replace(STATIC_URL, '<?php echo G5_URL ?>/')

	# 데이터 : data.js → DB
	html = html.replace('<script src="' + T + '/js/data.js"></script>',
		'<script>window.TPW_DATA = <?php echo json_encode($tteok, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script>')
	if 'window.TPW_DATA = <?php' not in html:
		sys.exit('data.js 를 찾지 못했습니다.')

	# 검색엔진용 매장 정보도 DB 기준으로
	html, n = re.subn(r'(<script type="application/ld\+json">\s*).*?(\s*</script>)',
		lambda m: m.group(1) + '<?php echo tteok_jsonld($tteok) ?>' + m.group(2), html, count=1, flags=re.S)
	if not n:
		sys.exit('ld+json 블록을 찾지 못했습니다.')

	head = ("<?php\n"
		"if (!defined('_INDEX_')) define('_INDEX_', true);\n"
		"if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가\n"
		"/* 이 파일은 tools/build_theme.py 가 루트의 index.html 로 만듭니다. 여기서 고치지 마세요. */\n"
		"$tteok = tteok_front_data();   // 관리자 → 떡판왕 사이트 에서 고친 내용\n"
		"?>\n")
	with open(os.path.join(THEME, 'index.php'), 'w', encoding='utf-8') as f:
		f.write(head + html)

	# 모바일도 같은 반응형 파일을 쓴다
	os.makedirs(os.path.join(THEME, 'mobile'), exist_ok=True)
	with open(os.path.join(THEME, 'mobile', 'index.php'), 'w', encoding='utf-8') as f:
		f.write("<?php\nif (!defined('_GNUBOARD_')) exit;\ninclude_once(G5_THEME_PATH.'/index.php');\n")

	print('테마를 만들었습니다 :', os.path.relpath(THEME, ROOT))


if __name__ == '__main__':
	main()
