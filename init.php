<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: init.php,v 1.94 2004/07/25 14:29:28 henoheno Exp $
//

/////////////////////////////////////////////////
// 初期設定 (エラー出力レベル)
error_reporting(E_ERROR | E_PARSE);	// (E_WARNING | E_NOTICE)を除外しています
//error_reporting(E_ALL);

/////////////////////////////////////////////////
// 初期設定 (文字エンコード、言語)
define('LANG','ja');	// Select 'ja' or 'en'
define('SOURCE_ENCODING','EUC-JP');

// mbstring extension 関連
mb_language('Japanese');
mb_internal_encoding(SOURCE_ENCODING);
ini_set('mbstring.http_input', 'pass');
mb_http_output('pass');
mb_detect_order('auto');

/////////////////////////////////////////////////
// 初期設定(設定ファイルの場所)
define('LANG_FILE', DATA_HOME . LANG . '.lng');
define('INI_FILE',  DATA_HOME . './pukiwiki.ini.php');

/////////////////////////////////////////////////
// 初期設定 (バージョン/著作権)
define('S_VERSION','1.4.3');
define('S_COPYRIGHT','
<strong>"PukiWiki" '.S_VERSION.'</strong> Copyright &copy; 2001-2004
<a href="http://pukiwiki.org">PukiWiki Developers Team</a>.
License is <a href="http://www.gnu.org/">GNU/GPL</a>.<br />
Based on "PukiWiki" 1.3 by <a href="http://factage.com/sng/">sng</a>
');

/////////////////////////////////////////////////
// 初期設定 (サーバ変数)
foreach (array('SCRIPT_NAME', 'SERVER_ADMIN', 'SERVER_NAME',
	'SERVER_PORT', 'SERVER_SOFTWARE') as $key) {
	define($key, isset($_SERVER[$key]) ? $_SERVER[$key] : '');
	unset(${$key}, $_SERVER[$key], $HTTP_SERVER_VARS[$key]);
}

/////////////////////////////////////////////////
// 初期設定 (グローバル変数)

// 脚注
$foot_explain = array();
// 関連するページ
$related = array();
// <head>内に追加するタグ
$head_tags = array();

/////////////////////////////////////////////////
// 初期設定(時間)
define('LOCALZONE',date('Z'));
define('UTIME',time() - LOCALZONE);
define('MUTIME',getmicrotime());

/////////////////////////////////////////////////
// ファイル読み込み
$die = '';
foreach(array('LANG_FILE', 'INI_FILE') as $file){
	if (!file_exists(constant($file)) || !is_readable(constant($file))) {
		$die = "${die}File is not found. ($file)\n";
	} else {
		require(constant($file));
	}
}
if ($die) { die_message(nl2br("\n\n" . $die)); }

/////////////////////////////////////////////////
// INI_FILE: $script: 初期設定
if (!isset($script) or $script == '') {
	$script = get_script_uri();
	if ($script === FALSE or (php_sapi_name() == 'cgi' and !is_url($script,TRUE))) {
		die_message('get_script_uri() failed: Please set $script at INI_FILE manually.');
	}
}

/////////////////////////////////////////////////
// INI_FILE: $agents:  UserAgentの識別

$ua = 'HTTP_USER_AGENT';
$user_agent = $matches = array();

$user_agent['agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
unset(${$ua}, $_SERVER[$ua], $HTTP_SERVER_VARS[$ua], $ua);	// safety

foreach ($agents as $agent) {
	if (preg_match($agent['pattern'], $user_agent['agent'], $matches)) {
		$user_agent['profile'] = isset($agent['profile']) ? $agent['profile'] : '';
		$user_agent['name']    = isset($matches[1]) ? $matches[1] : '';	// device or browser name
		$user_agent['vers']    = isset($matches[2]) ? $matches[2] : ''; // 's version
		break;
	}
}
unset($agents, $matches);

// Profile-related init and setting
define('UA_PROFILE', isset($user_agent['profile']) ? $user_agent['profile'] : '');

//// Not needed now
// define('INI_PRO_FILE', SUB_DIR . 'init.' . UA_PROFILE . '.php');
// if (file_exists(INI_PRO_FILE) && is_readable(INI_PRO_FILE)) {
//	require(INI_PRO_FILE);	// A chance to set or rewrite $user_agent['name'] and ['vers'] automatically
// }

define('UA_INI_FILE', DATA_HOME . UA_PROFILE . '.ini.php');
if (!file_exists(UA_INI_FILE) || !is_readable(UA_INI_FILE)) {
	die_message('UA_INI_FILE for "' . UA_PROFILE . '" not found.');
} else {
	require(UA_INI_FILE); // Also manually
}

define('UA_NAME', isset($user_agent['name']) ? $user_agent['name'] : '');
define('UA_VERS', isset($user_agent['vers']) ? $user_agent['vers'] : '');
unset($user_agent);	// Unset after reading UA_INI_FILE

/////////////////////////////////////////////////
// ディレクトリのチェック

$die = '';
foreach(array('DATA_DIR', 'DIFF_DIR', 'BACKUP_DIR', 'CACHE_DIR') as $dir){
	if(!is_writable(constant($dir))) {
		$die = "${die}Directory is not found or not writable ($dir)\n";
	}
}

// 設定ファイルの変数チェック
$temp = '';
foreach(array('rss_max', 'page_title', 'note_hr', 'related_link', 'show_passage',
	'rule_related_str', 'load_template_func') as $var){
	if (!isset(${$var})) { $temp .= "\$$var\n"; }
}
if ($temp) {
	if ($die) { $die .= "\n"; }	// A breath
	$die = "${die}Variable(s) not found: (Maybe the old *.ini.php?)\n" . $temp;
}

$temp = '';
foreach(array('LANG', 'PLUGIN_DIR') as $def){
	if (!defined($def)) $temp .= "$def\n";
}
if ($temp) {
	if ($die) { $die .= "\n"; }	// A breath
	$die = "${die}Define(s) not found: (Maybe the old *.ini.php?)\n" . $temp;
}

if($die){ die_message(nl2br("\n\n" . $die)); }
unset($die, $temp);

/////////////////////////////////////////////////
// 必須のページが存在しなければ、空のファイルを作成する

foreach(array($defaultpage, $whatsnew, $interwiki) as $page){
	if (!is_page($page)) { touch(get_filename($page)); }
}

/////////////////////////////////////////////////
// 外部からくる変数をチェック

// Prohibit $_GET attack
foreach (array('msg', 'pass') as $key) {
	if (isset($_GET[$key])) die_message("Sorry, already reserved: $key=");
}

$_GET    = input_filter($_GET);    $get    = & $_GET;
$_POST   = input_filter($_POST);   $post   = & $_POST;
$_COOKIE = input_filter($_COOKIE); $cookie = & $_COOKIE;

// Expire risk
unset($HTTP_GET_VARS, $HTTP_POST_VARS);	//, 'SERVER', 'ENV', 'SESSION', ...
unset($_REQUEST);	// Seems not reliable

/////////////////////////////////////////////////
// 文字コードを変換

// <form> で送信された文字 (ブラウザがエンコードしたデータ) のコードを変換
// post は常に <form> なので、必ず変換
if (isset($post['encode_hint']) && $post['encode_hint'] != '')
{
	// html.php の中で、<form> に encode_hint を仕込んでいるので、
	// encode_hint を用いてコード検出する。
	// 全体を見てコード検出すると、機種依存文字や、妙なバイナリ
	// コードが混入した場合に、コード検出に失敗する恐れがある。
	$encode = mb_detect_encoding($post['encode_hint']);
	mb_convert_variables(SOURCE_ENCODING, $encode, $post);
}
else if (isset($post['charset']) && $post['charset'] != '')
{
	// TrackBack Pingに含まれていることがある
	// 指定された場合は、その内容で変換を試みる
	// うまくいかなかった場合はコード検出の設定で変換しなおし
	if (mb_convert_variables(SOURCE_ENCODING, $post['charset'], $post) !== $post['charset'])
		mb_convert_variables(SOURCE_ENCODING, 'auto', $post);
}
else if (count($post) > 0)
{
	// デバッグ用に、取りあえず、警告メッセージを出しておきます。
	// echo "<p>Warning: 'encode_hint' field is not found in the posted data.</p>\n";

	// 全部まとめて、コード検出、変換
	mb_convert_variables(SOURCE_ENCODING, 'auto', $post);
}

// get は <form> からの場合と、<a href="http;//script/?query> の場合がある
if (isset($get['encode_hint']) && $get['encode_hint'] != '')
{
	// <form> の場合は、ブラウザがエンコードしているので、コード検出・変換が必要。
	// encode_hint が含まれているはずなので、それを見て、コード検出した後、変換する。
	// 理由は、post と同様
	$encode = mb_detect_encoding($get['encode_hint']);
	mb_convert_variables(SOURCE_ENCODING, $encode, $get);
}	
// <a href...> の場合は、サーバーが rawurlencode しているので、コード変換は不要

// QUERY_STRINGを取得
// cmdもpluginも指定されていない場合は、QUERY_STRINGをページ名かInterWikiNameであるとみなす為
// また、URI を urlencode せずに手打ちで入力した場合に対処する為
$arg = '';
if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) {
	$arg = $_SERVER['QUERY_STRING'];
} else if (isset($_SERVER['argv']) && count($_SERVER['argv'])) {
	$arg = $_SERVER['argv'][0];
}
// \0 除去
$arg = input_filter($arg);

// unset QUERY_STRINGs
foreach (array('QUERY_STRING', 'argv', 'argc') as $key) {
	unset(${$key}, $_SERVER[$key], $HTTP_SERVER_VARS[$key]);
}
// $_SERVER['REQUEST_URI'] is used by func.php NOW
unset($REQUEST_URI, $HTTP_SERVER_VARS['REQUEST_URI']);

// URI 手打の場合、コード変換し、get[] に上書き
// mb_convert_variablesのバグ(?)対策 配列で渡さないと落ちる
$arg = array($arg);
mb_convert_variables(SOURCE_ENCODING, 'auto', $arg);
$arg = $arg[0];

$matches = array();
foreach (explode('&', $arg) as $tmp_string)
{
	if (preg_match('/^([^=]+)=(.+)/', $tmp_string, $matches)
		and mb_detect_encoding($matches[2]) != 'ASCII')
	{
		$get[$matches[1]] = $matches[2];
	}
}
unset($matches);

/////////////////////////////////////////////////
// GET + POST = $vars

$vars = array_merge($get, $post);	// Seems more reliable than using $_REQUEST

// 入力チェック: cmd, plugin の文字列は英数字以外ありえない
foreach(array('cmd', 'plugin') as $var){
	if (array_key_exists($var, $vars) &&
	    ! preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $vars[$var])) {
		unset($get[$var], $post[$var], $vars[$var]);
	}
}

// 整形: page, strip_bracket()
if (array_key_exists('page', $vars)) {
	$get['page'] = $post['page'] = $vars['page']  = strip_bracket($vars['page']);
} else {
	$get['page'] = $post['page'] = $vars['page'] = '';
}

// 整形: msg, 改行を取り除く
if (isset($vars['msg'])) {
	$get['msg'] = $post['msg'] = $vars['msg'] = str_replace("\r",'',$vars['msg']);
}

// 後方互換性 (?md5=...)
if (array_key_exists('md5', $vars) and $vars['md5'] != '') {
	$get['cmd'] = $post['cmd'] = $vars['cmd'] = 'md5';
}

// TrackBack Ping
if (array_key_exists('tb_id', $vars) and $vars['tb_id'] != '') {
	$get['cmd'] = $post['cmd'] = $vars['cmd'] = 'tb';
}

// cmdもpluginも指定されていない場合は、QUERY_STRINGをページ名かInterWikiNameであるとみなす
if (!array_key_exists('cmd',$vars)  and !array_key_exists('plugin',$vars))
{
	if ($arg == '')
	{
		//なにも指定されていなかった場合は$defaultpageを表示
		$arg = $defaultpage;
	}		
	$arg = rawurldecode($arg);
	$arg = strip_bracket($arg);
	$arg = input_filter($arg);

	$get['cmd']  = $post['cmd']  = $vars['cmd']  = 'read';
	$get['page'] = $post['page'] = $vars['page'] = $arg;
}

// 入力チェック: 'cmd=' prohibits nasty 'plugin='
if (isset($vars['cmd']) && isset($vars['plugin']))
	unset($get['plugin'], $post['plugin'], $vars['plugin']);


/////////////////////////////////////////////////
// 初期設定($WikiName,$BracketNameなど)
// $WikiName = '[A-Z][a-z]+(?:[A-Z][a-z]+)+';
// $WikiName = '\b[A-Z][a-z]+(?:[A-Z][a-z]+)+\b';
// $WikiName = '(?<![[:alnum:]])(?:[[:upper:]][[:lower:]]+){2,}(?![[:alnum:]])';
// $WikiName = '(?<!\w)(?:[A-Z][a-z]+){2,}(?!\w)';

// BugTrack/304暫定対処
$WikiName = '(?:[A-Z][a-z]+){2,}(?!\w)';

// $BracketName = ':?[^\s\]#&<>":]+:?';
$BracketName = '(?!\s):?[^\r\n\t\f\[\]<>#&":]+:?(?<!\s)';

// InterWiki
$InterWikiName = "(\[\[)?((?:(?!\s|:|\]\]).)+):(.+)(?(1)\]\])";

// 注釈
$NotePattern = '/\(\(((?:(?>(?:(?!\(\()(?!\)\)(?:[^\)]|$)).)+)|(?R))*)\)\)/ex';

/////////////////////////////////////////////////
// 初期設定(ユーザ定義ルール読み込み)
require(DATA_HOME . 'rules.ini.php');

/////////////////////////////////////////////////
// 初期設定(その他のグローバル変数)

// 現在時刻
$now = format_date(UTIME);

// skin内でDTD宣言を切り替えるのに使用。paint.inc.php対策
$html_transitional = FALSE;
// FALSE:XHTML 1.1
// TRUE :XHTML 1.0 Transitional

// フェイスマークを$line_rulesに加える
if ($usefacemark) { $line_rules += $facemark_rules; }
unset($facemark_rules);

// 実体参照パターンおよびシステムで使用するパターンを$line_rulesに加える
//$entity_pattern = '[a-zA-Z0-9]{2,8}';
$entity_pattern = trim(join('',file(CACHE_DIR.'entities.dat')));

$line_rules = array_merge(array(
	'&amp;(#[0-9]+|#x[0-9a-f]+|' . $entity_pattern . ');' => '&$1;',
	"\r"          => "<br />\n",	/* 行末にチルダは改行 */
	'#related$'   => '<del>#related</del>',
	'^#contents$' => '<del>#contents</del>'
), $line_rules);

?>
