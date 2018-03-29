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

use XoopsModules\Article;
/** @var Article\Helper $helper */
$helper = Article\Helper::getInstance();

include __DIR__ . '/header.php';

//if(!empty($xoopsModuleConfig["do_urw"])):

/**
 * The comment detection scripts should be removed once absolute url is used in comment_view.php
 * The notification detection scripts should be removed once absolute url is used in notification_select.php
 *
 */
//if (preg_match("/(\/comment_[^\.]*\.php\?com_[a-z]*=.*)/i", $_SERVER["REQUEST_URI"], $matches)) {
if (preg_match("/(\/comment_[^\.]*\.php\?.*=.*)/i", $_SERVER['REQUEST_URI'], $matches)) {
    header('location: ' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . $matches[1]);
    exit();
}
if (!empty($_POST['not_submit']) && preg_match("/\/notification_update\.php/i", $_SERVER['REQUEST_URI'], $matches)) {
    include XOOPS_ROOT_PATH . '/include/notification_update.php';
    exit();
}

//endif;

if ($REQUEST_URI_parsed = art_parse_args($args_num, $args, $args_str)) {
    $args['article'] = !empty($args['article']) ? $args['article'] : @$args_num[0];
}

$article_id  = (int)(empty($_GET['article']) ? @$args['article'] : $_GET['article']);
$category_id = (int)(empty($_GET['category']) ? @$args['category'] : $_GET['category']);
$page        = (int)(!isset($_GET['page']) ? @$args['page'] : $_GET['page']);

$idCategorized = $category_id;

$articleHandler = xoops_getModuleHandler('article', $GLOBALS['artdirname']);
$article_obj    = $articleHandler->get($article_id);
/*
 * Global Xoops Entity could be used by blocks or other add-ons
 * Designed by Skalpa for Xoops 2.3+
 */
$xoopsEntity =& $article_obj;

$categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
$criteria        = new \CriteriaCompo(new \Criteria('ac.ac_publish', 0, '>'));
$categories_obj  = $categoryHandler->getByArticle($article_id, $criteria);
if (0 == count($categories_obj) || !in_array($category_id, array_keys($categories_obj))) {
    $category_id = 0;
}
$category_id = empty($category_id) ? $article_obj->getVar('cat_id') : $category_id;

$categories = [];
foreach ($categories_obj as $id => $category) {
    if ($id == $category_id) {
        continue;
    }
    $categories[] = [
        'id'    => $id,
        'title' => $category->getVar('cat_title')
    ];
}
unset($categories_obj);
$category_obj = $categoryHandler->get($category_id);

$uid      = empty($xoopsUser) ? 0 : $xoopsUser->getVar('uid');
$isAuthor = ($uid > 0 && $uid == $article_obj->getVar('uid'));
$isAdmin  = $categoryHandler->getPermission($category_obj, 'moderate');

if (!$isAuthor && !$article_obj->getVar('art_time_submit')) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php', 2, art_constant('MD_NOACCESS'));
}

if ($isAuthor || ($isAdmin && null !== $articleHandler->getCategoryStatus($category_id, $article_id))) {
} elseif (empty($category_id) || !$categoryHandler->getPermission($category_obj, 'view')
          || !$article_obj->getVar('art_time_publish')) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php', 2, art_constant('MD_NOACCESS'));
}

// restore $_SERVER["REQUEST_URI"]
if (!empty($REQUEST_URI_parsed)) {
    $args_REQUEST_URI = [];
    if (!empty($article_id)) {
        $args_REQUEST_URI[] = 'article=' . $article_id;
    }
    if (!empty($page)) {
        $args_REQUEST_URI[] = 'page=' . $page;
    }
    if (!empty($category_id)) {
        $args_REQUEST_URI[] = 'category=' . $category_id;
    }
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '/modules/' . $GLOBALS['artdirname'] . '/view.article.php')) . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . (empty($args_REQUEST_URI) ? '' : '?' . implode('&', $args_REQUEST_URI));
}

$xoopsOption['xoops_pagetitle'] = $xoopsModule->getVar('name') . ' - ' . $article_obj->getVar('art_title');
$template                       = $article_obj->getVar('art_template');
$xoopsOption['template_main']   = art_getTemplate('article', $template);
// Disable cache for author and category moderator since we don't have proper cache handling way for them
if ($isAuthor || art_isModerator($category_obj)) {
    $xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
}
$xoopsOption['xoops_module_header'] = art_getModuleHeader($template) . '
    <link rel="alternate" type="application/rss+xml" title="' . $xoopsModule->getVar('name') . ' article rss" href="' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/xml.php' . URL_DELIMITER . 'rss/' . $article_id . '/c' . $category_id . '">
    <link rel="alternate" type="application/rss+xml" title="' . $xoopsModule->getVar('name') . ' article rdf" href="' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/xml.php' . URL_DELIMITER . 'rdf/' . $article_id . '/c' . $category_id . '">
    <link rel="alternate" type="application/atom+xml" title="' . $xoopsModule->getVar('name') . ' article atom" href="' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/xml.php' . URL_DELIMITER . 'atom/' . $article_id . '/c' . $category_id . '">
    ';
// To enable image auto-resize by js
//$xoopsOption["xoops_module_header"] .= '<script src="' . XOOPS_URL . '/Frameworks/textsanitizer/xoops.js" type="text/javascript"></script>';
include XOOPS_ROOT_PATH . '/header.php';
include XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

// Topics
$topicHandler = xoops_getModuleHandler('topic', $GLOBALS['artdirname']);
$criteria     = new \CriteriaCompo(new \Criteria('t.top_expire', time(), '>'));
$topics_obj   = $topicHandler->getByArticle($article_id, $criteria);
$topics       = [];
foreach ($topics_obj as $id => $topic) {
    $topics[] = [
        'id'    => $id,
        'title' => $topic->getVar('top_title')
    ];
}

$article_data = [];

$article_data['id']     = $article_obj->getVar('art_id');
$article_data['cat_id'] = $category_id;
if ($article_obj->getVar('art_forum')) {
    $article_data['forum'] = XOOPS_URL . '/modules/' . sprintf($helper->getConfig('url_forum'), $article_obj->getVar('art_forum'), $helper->getConfig('forum'));
}

// title
$article_data['title'] = $article_obj->getVar('art_title');

// image
$article_data['image'] = $article_obj->getImage();

// Author
/*
 * name
 * uid
 */
//$article_data["author"] = $article_obj->getAuthor(true);
mod_loadFunctions('author');
$authors                = art_getAuthorNameFromId($article_obj->getVar('uid'), false, true);
$article_data['author'] = $authors[$article_obj->getVar('uid')];

// Writer
/*
 * name
 * profile
 * avatar
 */
$article_data['writer'] = $article_obj->getWriter();

// source
$article_data['source'] = $article_obj->getVar('art_source');

// publish time
$article_data['time'] = $article_obj->getTime($helper->getConfig('timeformat'));

// counter
$article_data['counter'] = $article_obj->getVar('art_counter');

// rating data
/* rating: average
 * votes: total rates
 */
$article_data['rates']  = $article_obj->getVar('art_rates');
$article_data['rating'] = $article_obj->getRatingAverage();

if (0 == $page) {
    // summary
    $article_data['summary'] = $article_obj->getSummary();
}

// current category
$article_data['category'] = $category_id;

// text of page
$text                 = $article_obj->getText($page);
$article_data['text'] = $text;
if (!empty($helper->getConfig('do_keywords'))) {
    $keywordsHandler = xoops_getModuleHandler('keywords', $GLOBALS['artdirname'], true);
    if ($keywordsHandler->init()) {
        $article_data['text']['body'] = $keywordsHandler->process($article_data['text']['body']);
    }
}

$article_data['headings'] =& $article_obj->headings;
$article_data['notes']    =& $article_obj->notes;

// pages
$count_page = count($article_obj->getPages(false));
if ($count_page > 1) {
    $pages = $article_obj->getPages(true);
    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
    $nav = new \XoopsPageNav($count_page, 1, $page, 'page', 'category=' . $category_id . '&amp;article=' . $article_id);
    //$nav = new \XoopsPageNav($count_page, 1, $page, "page");
    $article_data['pages'] = $nav->renderNav(5);
    if ($helper->getConfig('do_subtitle')) {
        for ($ipage = 0; $ipage < $count_page; ++$ipage) {
            if (empty($pages[$ipage]['title'])) {
                continue;
            }
            $title = $pages[$ipage]['title'];
            if ($page == $ipage) {
                $title = '<strong>' . $title . '</strong>';
            }
            $article_data['subtitles'][] = [
                'url'   => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . URL_DELIMITER . 'c' . $category_id . '/' . $article_id . '/p' . $ipage,
                'title' => $title
            ];
        }
    }
}

// trackbacks
/* trackback.title
 * trackback.url
 */
if (2 != $helper->getConfig('trackback_option')) { // trackback open
    $trackbackHandler = xoops_getModuleHandler('trackback', $GLOBALS['artdirname']);
    $trackback_array  =& $trackbackHandler->getByArticle($article_obj->getVar('art_id'));
    $trackbacks       = [];
    foreach ($trackback_array as $id => $trackback) {
        $trackbacks[] = [
            'id'      => $id,
            'title'   => $trackback->getVar('tb_title'),
            'url'     => $trackback->getVar('tb_url'),
            'excerpt' => $trackback->getVar('tb_excerpt'),
            'time'    => $trackback->getTime($helper->getConfig('timeformat')),
            'ip'      => $trackback->getIp(),
            'name'    => $trackback->getVar('tb_blog_name'),
        ];
    }
}

if (!empty($helper->getConfig('do_sibling'))) {
    if (empty($idCategorized)) {
        $cats = array_keys($categoryHandler->getAllByPermission($permission = 'access', ['cat_id']));
    } else {
        $cats = [$idCategorized];
    }
    $articles_sibling =& $articleHandler->getSibling($article_obj, $cats);
    if (!empty($articles_sibling['previous'])) {
        $articles_sibling['previous']['url']   = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . URL_DELIMITER . $articles_sibling['previous']['id'] . '/c' . $idCategorized;
        $articles_sibling['previous']['title'] = $myts->htmlSpecialChars($articles_sibling['previous']['title']);
        if (!empty($helper->getConfig('sibling_length'))) {
            $articles_sibling['previous']['title'] = xoops_substr($articles_sibling['previous']['title'], 0, $helper->getConfig('sibling_length'));
        }
    }
    if (!empty($articles_sibling['next'])) {
        $articles_sibling['next']['url']   = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.article.php' . URL_DELIMITER . $articles_sibling['next']['id'] . '/c' . $idCategorized;
        $articles_sibling['next']['title'] = $myts->htmlSpecialChars($articles_sibling['next']['title']);
        if (!empty($helper->getConfig('sibling_length'))) {
            $articles_sibling['next']['title'] = xoops_substr($articles_sibling['next']['title'], 0, $helper->getConfig('sibling_length'));
        }
    }
}

$xoopsTpl->assign('modulename', $xoopsModule->getVar('name'));
$xoopsTpl->assign('copyright', sprintf($helper->getConfig('copyright'), !empty($article_data['writer']['name']) ? $article_data['writer']['name'] : $article_data['author']));

$xoopsTpl->assign('tracks', $categoryHandler->getTrack($category_obj, true));
$xoopsTpl->assign('links', art_parseLinks($article_obj->getVar('art_elinks', 'E'))); // related external links

$xoopsTpl->assign_by_ref('article', $article_data);
$xoopsTpl->assign_by_ref('categories', $categories);
$xoopsTpl->assign_by_ref('topics', $topics);
$xoopsTpl->assign_by_ref('trackbacks', $trackbacks);
$xoopsTpl->assign_by_ref('sibling', $articles_sibling);

$xoopsTpl->assign('isadmin', $isAdmin);
$xoopsTpl->assign('isauthor', $isAuthor);

$xoopsTpl->assign('do_counter', $helper->getConfig('do_counter'));
$xoopsTpl->assign('do_trackback', $helper->getConfig('do_trackback'));

$xoopsTpl->assign('canRate', !empty($helper->getConfig('do_rate')) && $categoryHandler->getPermission($category_obj, 'rate'));
$xoopsTpl->assign('page', $page);

$xoopsTpl->assign('sponsors', $category_obj->getSponsor());

if (@require_once XOOPS_ROOT_PATH . '/modules/tag/include/tagbar.php') {
    $xoopsTpl->assign('tagbar', tagBar($article_obj->getVar('art_keywords', 'n')));
}

if ($transferbar = @include XOOPS_ROOT_PATH . '/Frameworks/transfer/bar.transfer.php') {
    $xoopsTpl->assign('transfer', $transferbar);
}

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$xoopsTpl->assign('xoops_module_header', $xoopsOption['xoops_module_header']);
$xoopsTpl->assign('xoops_pagetitle', $xoopsOption['xoops_pagetitle']);

// for comment and notification
//$_SERVER["REQUEST_URI"] = XOOPS_URL."/modules/".$GLOBALS["artdirname"]."/view.article.php";
$_GET['article'] = $article_obj->getVar('art_id');

// for comment
$category = $category_id; // The $comment_config["extraParams"]
include XOOPS_ROOT_PATH . '/include/comment_view.php';

require_once __DIR__ . '/footer.php';
