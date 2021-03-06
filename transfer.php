<?php
/**
 * Article module for XOOPS
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         article
 * @since           1.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 */

use Xmf\Request;
use XoopsModules\Article;

require_once __DIR__ . '/header.php';

/** @var Article\Helper $helper */
$helper = Article\Helper::getInstance();

if (art_parse_args($args_num, $args, $args_str)) {
    $args['article'] = @$args_num[0];
    $args['op']      = @$args_str[0];
}

$category_id = Xmf\Request::getInt('category', @$args['category']);
$article_id  = Xmf\Request::getInt('article', @$args['article']);
$page        = Xmf\Request::getInt('page', @$args['page']);
$op          = \Xmf\Request::getCmd('op', @$args['op']);
$op          = mb_strtolower(trim($op));

if (empty($article_id)) {
    if (empty(Request::getString('HTTP_REFERER', '', 'SERVER'))) {
        require_once XOOPS_ROOT_PATH . '/header.php';
        xoops_error(_NOPERM);
        $xoopsOption['output_type'] = 'plain';
        require_once XOOPS_ROOT_PATH . '/footer.php';
        exit();
    }
    $ref_parser = parse_url(Request::getString('HTTP_REFERER', '', 'SERVER'));
    $uri_parser = parse_url($_SERVER['REQUEST_URI']);
    if ((!empty($ref_parser['host']) && !empty($uri_parser['host']) && $uri_parser['host'] != $ref_parser['host'])
        || ($ref_parser['path'] != $uri_parser['path'])) {
        require_once XOOPS_ROOT_PATH . '/header.php';
        require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';
        xoops_confirm([], 'javascript: window.close();', _MD_TRANSFER_DONE, _CLOSE, Request::getString('HTTP_REFERER', '', 'SERVER'));
        $xoopsOption['output_type'] = 'plain';
        require_once XOOPS_ROOT_PATH . '/footer.php';
        exit();
    }
    require_once XOOPS_ROOT_PATH . '/header.php';
    xoops_error(_NOPERM);
    $xoopsOption['output_type'] = 'plain';
    require_once XOOPS_ROOT_PATH . '/footer.php';
    exit();
}

$articleHandler = $helper->getHandler('Article', $GLOBALS['artdirname']);
$article_obj    = $articleHandler->get($article_id);
if (empty($category_id)) {
    $category_id = $article_obj->getVar('cat_id');
} else {
    $article_cats = $article_obj->getCategories();
    if (!in_array($category_id, $article_cats)) {
        $category_id = 0;
    }
}
$categoryHandler = $helper->getHandler('Category', $GLOBALS['artdirname']);
$category_obj    = $categoryHandler->get($category_id);

if (empty($category_id) || !$categoryHandler->getPermission($category_obj, 'view')) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php', 2, art_constant('MD_NOACCESS'));
}

// Display option form
if (empty($op)) {
    $module_variables = "<input type=\"hidden\" name=\"category\" id=\"category\" value=\"{$category_id}\">";
    $module_variables .= "<input type=\"hidden\" name=\"article\" id=\"article\" value=\"{$article_id}\">";
    $module_variables = "<input type=\"hidden\" name=\"page\" id=\"page\" value=\"{$page}\">";
    require_once XOOPS_ROOT_PATH . '/Frameworks/transfer/option.transfer.php';
    exit();
}
$data             = [];
$data['id']       = $article_id;
$data['uid']      = $article_obj->getVar('uid');
$data['title']    = $article_obj->getVar('art_title');
$data['summary']  = $article_obj->getSummary();
$data['date']     = $article_obj->getTime('s');
$data['time']     = $article_obj->getTime('l');
$data['image']    = $article_obj->getImage();
$data['source']   = $article_obj->getVar('art_source');
$data['keywords'] = $article_obj->getVar('art_keywords');
$data['url']      = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . URL_DELIMITER . 'c' . $category_id . '/' . $article_obj->getVar('art_id');

if (!$_author = $article_obj->getWriter()) {
    $_author = $article_obj->getAuthor(true);
}
$data['author'] = $_author['name'];

$data['page'] = $page;

switch ($op) {
    // Use raw content
    case 'pdf':
    case 'print':

        $data['subtitle']     = $category_obj->getVar('cat_title');
        $article_data['text'] = $article_obj->getText($page, 'raw');
        if (!empty($article_data['text']['title'])) {
            $data['subsubtitle'] = '#' . $page . ' ' . $article_data['text']['title'];
        }
        $tmp = [];
        if ($data['author']) {
            $tmp[] = $data['author'];
        }
        if ($data['source']) {
            $tmp[] = $data['source'];
        }
        $data['author']  = implode(' ', $tmp);
        $data['content'] = '';
        if ($data['keywords']) {
            $data['content'] .= art_constant('MD_KEYWORDS') . ': ' . $data['keywords'] . '<br><br>';
        }
        if ($data['summary']) {
            $data['content'] .= art_constant('MD_SUMMARY') . ': ' . $data['summary'] . '<br><br>';
        }
        $data['content'] .= $article_data['text']['body'] . '<br>';

        ${"{$op}_data"} = &$data;

        break;
    // Use title
    case 'bookmark':
        break;
    // Use if already linked to a forum topic, redirect to it
    case 'newbb':
        $data['content'] = $data['summary'];
        if ($article_obj->getVar('art_forum')) {
            $data['entry'] = $article_obj->getVar('art_forum');
        } else {
            $data['content']  = $article_obj->getSummary(true);
            $data['forum_id'] = $helper->getConfig('forum');
        }
        break;
    // Use regular content
    default:
        $art_text        = $article_obj->getText($page);
        $data['content'] = $art_text['body'];
        break;
}
require_once XOOPS_ROOT_PATH . '/Frameworks/transfer/action.transfer.php';
exit();
