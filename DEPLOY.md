# 떡판왕 그누보드 배포

정적 사이트(GitHub Pages)와 별개로, 관리자에서 내용을 고칠 수 있는 **그누보드5 버전**을 카페24에 올립니다.
구조와 배포 방식은 BAR JUNGLE 과 같습니다.

```
index.html · css/ · js/ · images/    화면 원본 (GitHub Pages 에서도 그대로 보임)
gnuboard/theme/tteokpanwang/         그누보드 테마 ← tools/build_theme.py 가 위 원본으로 만듦
gnuboard/adm/                        관리자 화면 (떡판왕 사이트 메뉴)
gnuboard/extend/tteok.extend.php     표 만들기 · 처음 데이터 · 화면용 데이터 · 관리자 메뉴 정리
tools/deploy_ftp.py                  카페24 FTP 업로드
```

## 처음 한 번 : 카페24 에 설치

### 1. 카페24 정보 확인
카페24 호스팅 관리 화면에서 아래를 확인해 둡니다.

| 항목 | 보통 값 |
|---|---|
| FTP 주소 | `아이디.mycafe24.com` |
| FTP 아이디 / 비밀번호 | 호스팅 아이디 / FTP 비밀번호 |
| DB 호스트 | `localhost` |
| DB 이름 · DB 아이디 | 호스팅 아이디와 같음 |
| DB 비밀번호 | 호스팅 신청 때 정한 DB 비밀번호 |
| PHP 버전 | 7.4 이상 (8.x 권장) |

### 2. 접속 정보 파일 만들기
저장소 루트에 `.deploy.env` 를 만듭니다. `.gitignore` 에 들어 있어 GitHub 에 올라가지 않습니다.

```bash
cat > .deploy.env <<'EOF'
FTP_HOST=아이디.mycafe24.com
FTP_USER=아이디
FTP_PASSWORD=FTP_비밀번호
EOF
chmod 600 .deploy.env
git config core.hooksPath .githooks
```

### 3. 그누보드 본체 올리기
```bash
python3 tools/deploy_ftp.py --core ~/Downloads/gnuboard5.6.24
```
본체 파일 전부를 `/www` 에 올리고 `data` 폴더 권한을 707 로 엽니다. (몇 분 걸립니다)

### 4. 설치 마법사
브라우저에서 `http://아이디.mycafe24.com/` 을 열면 설치 화면이 나옵니다.
- DB 호스트 `localhost`, DB 아이디·이름은 호스팅 아이디, 비밀번호는 DB 비밀번호
- 테이블 접두사 `g5_` 그대로
- 관리자 아이디·비밀번호는 원하는 값으로 (잊지 않게 따로 보관)
- **영카트(쇼핑몰) 설치는 체크하지 않습니다**

### 5. 떡판왕 파일 올리기
```bash
python3 tools/deploy_ftp.py
```
(이후로는 `git push` 할 때 자동으로 올라갑니다)

### 6. 테마 적용
관리자(`/adm`) 로그인 → **환경설정 → 테마설정** → `떡판왕 TTEOKPANWANG` **테마적용**.
메인 화면을 한 번 열면 떡판왕 표가 만들어지고, 지금 사이트의 메뉴 · 갤러리 · 매장 정보와 사진이 자동으로 들어갑니다.

## 관리자에서 고치는 곳

관리자 로그인 → 왼쪽 **떡판왕 사이트**

| 메뉴 | 화면에서 바뀌는 곳 |
|---|---|
| 시그니처 메뉴 | '왕의 한 판을 만나보세요' 카드 (사진 · 라벨 · 이름 · 소개 · 가격) |
| 전체 메뉴 | '전체 메뉴' 판. 표에서 바로 고치고 **선택수정**. 분류는 **분류 관리** |
| 푸드 갤러리 | '보는 순간, 이미 배고프다' 사진과 칸 모양 |
| 매장 정보 | '오시는 길' 카드 · 푸터 · SNS 링크 |

저장하는 즉시 사이트에 반영됩니다. 배포와 무관합니다.
그누보드 기본 메뉴(게시판 · 회원 · 포인트 등)는 숨겨 두었습니다. 환경설정은 **기본환경설정 · 테마설정**만 남겼습니다.

## 화면(디자인)을 고칠 때

1. 루트의 `index.html` · `css/` · `js/lib.js` 를 고칩니다. (GitHub Pages 에 바로 보임)
2. `python3 tools/build_theme.py` 로 테마를 다시 만들고 함께 커밋합니다.
3. `git push` → 카페24 에도 올라갑니다.

`gnuboard/theme/tteokpanwang/index.php` 는 직접 고치지 않습니다. 다시 만들 때 덮어써집니다.
push 할 때 테마가 원본과 다르면 훅이 멈추고 알려 줍니다.

`js/data.js` 는 정적 사이트용 데이터이자, 그누보드를 **처음 설치할 때 채울 초기값**입니다.
설치 뒤에는 관리자 화면(DB)이 원본입니다.

## 배포 규칙 (정글과 같음)

- 카페24 는 한국 IP 에서만 FTP 쓰기가 됩니다. 그래서 작업 컴퓨터에서 push 할 때 올립니다.
- 파일을 지우지 않고 올리기만 합니다. 서버에서 빼야 할 파일은 FTP 로 직접 지웁니다.
- 바뀐 파일만 올리려고 서버 `/www/data/.deploy-manifest.json` 에 목록을 둡니다. 지우면 다음에 전부 다시 올립니다.
- 배포 없이 push : `SKIP_DEPLOY=1 git push` / 전부 다시 올리기 : `DEPLOY_FORCE=1 python3 tools/deploy_ftp.py`
