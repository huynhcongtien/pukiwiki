<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: pukiwiki.ini.php,v 1.36 2003/06/02 10:04:12 arino Exp $
//
// PukiWiki setting file

/////////////////////////////////////////////////
// ディレクトリ指定 最後に / が必要 属性は 777
/////////////////////////////////////////////////
// データの格納ディレクトリ
define('DATA_DIR','./wiki/');
/////////////////////////////////////////////////
// 差分ファイルの格納ディレクトリ
define('DIFF_DIR','./diff/');
/////////////////////////////////////////////////
// バックアップファイル格納先ディレクトリ
define('BACKUP_DIR','./backup/');
/////////////////////////////////////////////////
// プラグインファイル格納先ディレクトリ
define('PLUGIN_DIR','./plugin/');
/////////////////////////////////////////////////
// キャッシュファイル格納ディレクトリ
define('CACHE_DIR','./cache/');

/////////////////////////////////////////////////
// ローカル時間
define('ZONE','JST');
define('ZONETIME',9 * 3600); // JST = GMT+9
/////////////////////////////////////////////////
// index.php などに変更した場合のスクリプト名の設定
// とくに設定しなくても問題なし
//$script = 'http://hogehoge/pukiwiki/';

/////////////////////////////////////////////////
// トップページの名前
$defaultpage = 'FrontPage';
/////////////////////////////////////////////////
// 更新履歴ページの名前
$whatsnew = 'RecentChanges';
/////////////////////////////////////////////////
// InterWikiNameページの名前
$interwiki = 'InterWikiName';
/////////////////////////////////////////////////
// 編集者の名前(自由に変えてください)
$modifier = 'me';
/////////////////////////////////////////////////
// 編集者のホームページ(自由に変えてください)
$modifierlink = 'http://change me!/';

/////////////////////////////////////////////////
// ホームページのタイトル(自由に変えてください)
// RSS に出力するチャンネル名
$page_title = 'PukiWiki';

/////////////////////////////////////////////////
// WikiNameを*無効に*する
$nowikiname = 0;

/////////////////////////////////////////////////
// AutoLinkを有効にする場合は、AutoLink対象となる
// ページ名の最短バイト数を指定
// AutoLinkを無効にする場合は0
$autolink = 0;

/////////////////////////////////////////////////
// 凍結機能を有効にする
$function_freeze = 1;
/////////////////////////////////////////////////
// 凍結解除用の管理者パスワード(MD5)
// pukiwiki.php?md5=pass のようにURLに入力し
// MD5にしてからどうぞ。面倒なら以下のように。
// $adminpass = md5("pass");
// 以下は pass のMD5パスワードになってます。
$adminpass = '1a1dc91c907325c69271ddf0c944bc72';

/////////////////////////////////////////////////
// 編集時に認証が必要
$edit_auth = 0;

/////////////////////////////////////////////////
// 編集時認証のアカウントとパスワード
// ユーザ名とパスワードを記入。
$edit_auth_users = array(
 '' => '',
);
/////////////////////////////////////////////////
// 更新履歴を表示するときの最大件数
$maxshow = 80;
/////////////////////////////////////////////////
// 編集することのできないページの名前 , で区切る
$cantedit = array( $whatsnew, );

/////////////////////////////////////////////////
// Last-Modified ヘッダを出力する
$lastmod = 0;

/////////////////////////////////////////////////
// 日付フォーマット
$date_format = 'Y-m-d';
/////////////////////////////////////////////////
// 時刻フォーマット
$time_format = 'H:i:s';
/////////////////////////////////////////////////
// 曜日配列
$weeklabels = $_msg_week;

/////////////////////////////////////////////////
// RSS に出力するページ数
$rss_max = 15;

/////////////////////////////////////////////////
// バックアップを行う
$do_backup = 1;
/////////////////////////////////////////////////
// ページを削除した際にバックアップもすべて削除する
$del_backup = 0;
/////////////////////////////////////////////////
// 定期バックアップの間隔を時間(hour)で指定します(0で更新毎)
$cycle = 6;
/////////////////////////////////////////////////
// バックアップの最大世代数
$maxage = 20;
/////////////////////////////////////////////////
// バックアップの世代を区切る文字列
// (通常はこのままで良いが、文章中で使われる可能性
// があれば、使われそうにない文字を設定する)
$splitter = ">>>>>>>>>>";
/////////////////////////////////////////////////
// ページの更新時にバックグランドで実行されるコマンド(mknmzなど)
$update_exec = '';
//$update_exec = '/usr/bin/mknmz --media-type=text/pukiwiki -O /var/lib/namazu/index/ -L ja -c -K /var/www/wiki/';

/////////////////////////////////////////////////
// 一覧・更新一覧に含めないページ名(正規表現で)
$non_list = '^\:';

/////////////////////////////////////////////////
// $non_listを文字列検索の対象ページとするか
// 0にすると、上記ページ名が単語検索からも除外されます。
$search_non_list = 1;

/////////////////////////////////////////////////
// ページ名に従って自動で、雛形とするページの読み込み
$auto_template_func = 1;
$auto_template_rules = array(
'((.+)\/([^\/]+))' => '\2/template'
);

/////////////////////////////////////////////////
// 見出し行に固有のアンカーを自動挿入する
$fixed_heading_anchor = 0;
/////////////////////////////////////////////////
// <pre>の行頭スペースをひとつ取り除く
$preformat_ltrim = 0;

/////////////////////////////////////////////////
// ユーザーエージェント対応設定
// デフォルト
$user_agent = array('name'=>'default');
// 携帯端末
$agents = array(
	array('name'=>'jphone','pattern'=>'#^J-PHONE.+(Profile/)#'),
	array('name'=>'jphone','pattern'=>'#^J-PHONE#'),
	array('name'=>'i_mode','pattern'=>'#DoCoMo/(1\.0)/(?:[^/]+/c([0-9]+))?#'),
	array('name'=>'i_mode','pattern'=>'#DoCoMo/(2\.0) [^(]+\(c([0-9]+)#'),
	array('name'=>'i_mode','pattern'=>'#DDIPOCKET;JRC/[^/]+/(1\.0)/0100/c([0-9]+)#'),
);

/////////////////////////////////////////////////
// ユーザ定義ルール
//
//  正規表現で記述してください。?(){}-*./+\$^|など
//  は \? のようにクォートしてください。
//  前後に必ず / を含めてください。行頭指定は ^ を頭に。
//  行末指定は $ を後ろに。
//
/////////////////////////////////////////////////
// ユーザ定義ルール(直接ソースを置換)
$str_rules = array(
	'now\?' => format_date(UTIME),
	'date\?' => get_date($date_format),
	'time\?' => get_date($time_format),
//	'&now;' => format_date(UTIME),
//	'&date;' => get_date($date_format),
//	'&time;' => get_date($time_format),
);

/////////////////////////////////////////////////
// フェイスマーク定義ルール(コンバート時に置換)
// $usefacemark = 1ならフェイスマークが置換されます
// 文章内にXDなどが入った場合にfacemarkに置換されてしまうので
// 必要のない方は $usefacemarkを0にしてください。
$facemark_rules = array(
'\s(\:\))' => ' <img src="./face/smile.png" alt="$1" />',
'\s(\:D)' => ' <img src="./face/bigsmile.png" alt="$1" />',
'\s(\:p)' => ' <img src="./face/huh.png" alt="$1" />',
'\s(\:d)' => ' <img src="./face/huh.png" alt="$1" />',
'\s(XD)' => ' <img src="./face/oh.png" alt="$1" />',
'\s(X\()' => ' <img src="./face/oh.png" alt="$1" />',
'\s(;\))' => ' <img src="./face/wink.png" alt="$1" />',
'\s(;\()' => ' <img src="./face/sad.png" alt="$1" />',
'\s(\:\()' => ' <img src="./face/sad.png" alt="$1" />',
'&(smile);' => ' <img src="./face/smile.png" alt="$1" />',
'&(bigsmile);' => ' <img src="./face/bigsmile.png" alt="$1" />',
'&(huh);' => ' <img src="./face/huh.png" alt="$1" />',
'&(oh);' => ' <img src="./face/oh.png" alt="$1" />',
'&(wink);' => ' <img src="./face/wink.png" alt="$1" />',
'&(sad);' => ' <img src="./face/sad.png" alt="$1" />',
'&(heart);' => '<img src="./face/heart.png" alt="$1" />',
);

?>
