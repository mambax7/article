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

$xoopsOption['pagetype'] = 'search';
require_once __DIR__ . '/header.php';

/** @var \XoopsModules\Article\Helper $helper */
$helper = \XoopsModules\Article\Helper::getInstance();

//$xoopsModule->loadLanguage("main");
art_load_lang_file('main');
$configHandler     = xoops_getHandler('config');
$xoopsConfigSearch = $configHandler->getConfigsByCat(XOOPS_CONF_SEARCH);
if (empty($xoopsConfigSearch['enable_search'])) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php', 2, art_constant('MD_NOACCESS'));
}

$xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
$xoopsOption['template_main']                             = art_getTemplate('search', $helper->getConfig('template'));
$xoopsOption['xoops_module_header']                       = art_getModuleHeader($helper->getConfig('template'));
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

$module_info_search = $xoopsModule->getInfo('search');
require_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/' . $module_info_search['file'];
$limit = $helper->getConfig('articles_perpage');

$queries  = [];
$andor    = isset($_POST['andor']) ? $_POST['andor'] : (isset($_GET['andor']) ? $_GET['andor'] : '');
$start    = isset($_GET['start']) ? $_GET['start'] : 0;
$uid      = 0;
$category = isset($_POST['category']) ? $_POST['category'] : (isset($_GET['category']) ? $_GET['category'] : null);
$username = isset($_POST['uname']) ? $_POST['uname'] : (isset($_GET['uname']) ? $_GET['uname'] : null);
$searchin = isset($_POST['searchin']) ? $_POST['searchin'] : (isset($_GET['searchin']) ? explode('|', $_GET['searchin']) : []);
$sortby   = isset($_POST['sortby']) ? $_POST['sortby'] : (isset($_GET['sortby']) ? $_GET['sortby'] : null);
$term     = isset($_POST['term']) ? $_POST['term'] : (isset($_GET['term']) ? $_GET['term'] : '');

if (empty($category) || (is_array($category) && in_array('all', $category))) {
    $category = [];
} else {
    $category = (!is_array($category)) ? explode(',', $category) : $category;
    $category = array_map('intval', $category);
}

$andor  = in_array(mb_strtoupper($andor), ['OR', 'AND', 'EXACT']) ? mb_strtoupper($andor) : 'OR';
$sortby = in_array(mb_strtolower($sortby), [
    'a.art_id desc',
    'a.art_time_publish desc',
    'a.art_title',
    'ac.cat_id',
], true) ? mb_strtolower($sortby) : 'a.art_id DESC';

if (!(empty($_POST['submit']) && empty($_GET['term']))) {
    $next_search['category'] = implode(',', $category);
    $next_search['andor']    = $andor;

    $next_search['term'] = $term;
    $query               = trim($term);

    if ('EXACT' !== $andor) {
        $ignored_queries = []; // holds kewords that are shorter than allowed minmum length
        $temp_queries    = preg_split("/[\s,]+/", $query);
        foreach ($temp_queries as $q) {
            $q = trim($q);
            if (mb_strlen($q) >= $xoopsConfigSearch['keyword_min']) {
                $queries[] = $myts->addSlashes($q);
            } else {
                $ignored_queries[] = $myts->addSlashes($q);
            }
        }
        if (0 == count($queries)) {
            redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/search.php', 2, sprintf(_SR_KEYTOOSHORT, $xoopsConfigSearch['keyword_min']));
        }
    } else {
        if (mb_strlen($query) < $xoopsConfigSearch['keyword_min']) {
            redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/search.php', 2, sprintf(_SR_KEYTOOSHORT, $xoopsConfigSearch['keyword_min']));
        }
        $queries = [$myts->addSlashes($query)];
    }

    $uname_required       = false;
    $search_username      = trim($username);
    $next_search['uname'] = $search_username;
    if (!empty($search_username)) {
        $uname_required  = true;
        $search_username = $myts->addSlashes($search_username);
        if (!$result = $xoopsDB->query('SELECT uid FROM ' . $xoopsDB->prefix('users') . ' WHERE uname LIKE ' . $xoopsDB->quoteString("%$search_username%"))) {
            redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/search.php', 1, art_constant('MD_ERROROCCURED'));
        }
        $uid = [];
        while (false !== ($row = $xoopsDB->fetchArray($result))) {
            $uid[] = $row['uid'];
        }
    } else {
        $uid = 0;
    }

    $next_search['sortby']   = $sortby;
    $next_search['searchin'] = implode('|', $searchin);

    /* To be added: year-month
     * see view.archive.php
     */
    if (!empty($time)) {
        $extra = '';
    } else {
        $extra = '';
    }

    if ($uname_required && (!$uid || count($uid) < 1)) {
        $results = [];
    } else {
        $results = $module_info_search['func']($queries, $andor, $limit, $start, $uid, $category, $sortby, $searchin, $extra);
    }

    if (count($results) < 1) {
        $results[] = ['text' => _SR_NOMATCH];
    }
    /*
     if ( count($results) < 1 ) {
     redirect_header("javascript:history.go(-1);", 2, _SR_NOMATCH);
     } else
     */
    {
        $xoopsTpl->assign('results', $results);

        if (count($next_search) > 0) {
            $items = [];
            foreach ($next_search as $para => $val) {
                if (!empty($val)) {
                    $items[] = "$para=$val";
                }
            }
            if (count($items) > 0) {
                $paras = implode('&', $items);
            }
            unset($next_search);
            unset($items);
        }
        $search_url = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/search.php?' . $paras;

        if (count($results)) {
            $next            = $start + $limit;
            $queries         = implode(',', $queries);
            $search_url_next = $search_url . "&start=$next";
            $search_next     = '<a href="' . htmlspecialchars($search_url_next, ENT_QUOTES | ENT_HTML5) . '">' . _SR_NEXT . '</a>';
            $xoopsTpl->assign('search_next', $search_next);
        }
        if ($start > 0) {
            $prev            = $start - $limit;
            $search_url_prev = $search_url . "&start=$prev";
            $search_prev     = '<a href="' . htmlspecialchars($search_url_prev, ENT_QUOTES | ENT_HTML5) . '">' . _SR_PREVIOUS . '</a>';
            $xoopsTpl->assign('search_prev', $search_prev);
        }
    }

    unset($results);
    $search_info = _SR_KEYWORDS . ': ' . $myts->htmlSpecialChars($term);
    if ($uname_required) {
        if ($search_info) {
            $search_info .= '<br>';
        }
        $search_info .= art_constant('MD_USERNAME') . ': ' . $myts->htmlSpecialChars($search_username);
    }
    $xoopsTpl->assign('search_info', $search_info);
}

/*
 $permissionHandler = \XoopsModules\Article\Helper::getInstance()->getHandler("Permission", $GLOBALS["artdirname"]);
 $allowed_ids = $permissionHandler->getCategories();
 */

/* type */
$type_select = '<select name="andor">';
$type_select .= '<option value="OR"';
if ('OR' === $andor) {
    $type_select .= ' selected="selected"';
}
$type_select .= '>' . _SR_ANY . '</option>';
$type_select .= '<option value="AND"';
if ('AND' === $andor) {
    $type_select .= ' selected="selected"';
}
$type_select .= '>' . _SR_ALL . '</option>';
$type_select .= '<option value="EXACT"';
if ('exact' === $andor) {
    $type_select .= ' selected="selected"';
}
$type_select .= '>' . _SR_EXACT . '</option>';
$type_select .= '</select>';

/* category */
$categoryHandler = $helper->getHandler('Category', $GLOBALS['artdirname']);
$categories      = &$categoryHandler->getTree(0, 'access', '----');
$select_category = '<select name="category[]" size="5" multiple="multiple">';
$select_category .= '<option value="all"';
if (empty($category) || 0 == count($category)) {
    $select_category .= 'selected="selected"';
}
$select_category .= '>' . _ALL . '</option>';
foreach ($categories as $id => $cat) {
    $select_category .= '<option value="' . $id . '"';
    if (in_array($id, $category)) {
        $select_category .= 'selected="selected"';
    }
    $select_category .= '>' . $cat['prefix'] . $cat['cat_title'] . '</option>';
}
$select_category .= '</select>';

/* scope */
$searchin_select = '';
$searchin_select .= '<input type="checkbox" name="searchin[]" value="title"';
if (in_array('title', $searchin)) {
    $searchin_select .= ' checked';
}
$searchin_select .= '>' . art_constant('MD_TITLE') . '&nbsp;&nbsp;';
$searchin_select .= '<input type="checkbox" name="searchin[]" value="author"';
if (in_array('author', $searchin)) {
    $searchin_select .= ' checked';
}
$searchin_select .= '>' . art_constant('MD_AUTHOR') . '&nbsp;&nbsp;';
$searchin_select .= '<input type="checkbox" name="searchin[]" value="keywords"';
if (in_array('keywords', $searchin)) {
    $searchin_select .= ' checked';
}
$searchin_select .= '>' . art_constant('MD_KEYWORDS') . '&nbsp;&nbsp;';
$searchin_select .= '<input type="checkbox" name="searchin[]" value="summary"';
if (in_array('summary', $searchin)) {
    $searchin_select .= ' checked';
}
$searchin_select .= '>' . art_constant('MD_SUMMARY') . '&nbsp;&nbsp;';
$searchin_select .= '<input type="checkbox" name="searchin[]" value="subtitle"';
if (in_array('subtitle', $searchin)) {
    $searchin_select .= ' checked';
}
$searchin_select .= '>' . art_constant('MD_SUBTITLE') . '&nbsp;&nbsp;';
$searchin_select .= '<input type="checkbox" name="searchin[]" value="text"';
if (in_array('text', $searchin)) {
    $searchin_select .= ' checked';
}
$searchin_select .= '>' . art_constant('MD_BODY') . '&nbsp;&nbsp;';
$searchin_select .= '<input type="checkbox" name="searchin[]" value="all"';
if (in_array('all', $searchin) || empty($searchin)) {
    $searchin_select .= ' checked';
}
$searchin_select .= '>' . _ALL . '&nbsp;&nbsp;';

/* sortby */
$sortby_select = '<select name="sortby">';
$sortby_select .= '<option value="a.art_id desc"';
if ('a.art_id desc' === $sortby || empty($sortby)) {
    $sortby_select .= ' selected="selected"';
}
$sortby_select .= '>' . _NONE . '</option>';
$sortby_select .= '<option value="a.art_time_publish desc"';
if ('a.art_time_publish desc' === $sortby) {
    $sortby_select .= ' selected="selected"';
}
$sortby_select .= '>' . art_constant('MD_TIME') . '</option>';
$sortby_select .= '<option value="a.art_title"';
if ('a.art_title' === $sortby) {
    $sortby_select .= ' selected="selected"';
}
$sortby_select .= '>' . art_constant('MD_TITLE') . '</option>';
$sortby_select .= '<option value="ac.cat_id"';
if ('ac.cat_id' === $sortby) {
    $sortby_select .= ' selected="selected"';
}
$sortby_select .= '>' . art_constant('MD_CATEGORY') . '</option>';
$sortby_select .= '</select>';

$xoopsTpl->assign('type_select', $type_select);
$xoopsTpl->assign('searchin_select', $searchin_select);
$xoopsTpl->assign('category_select', $select_category);
$xoopsTpl->assign('sortby_select', $sortby_select);
$xoopsTpl->assign('search_term', $term);
$xoopsTpl->assign('search_user', $username);

$xoopsTpl->assign('modulename', $xoopsModule->getVar('name'));

if ($xoopsConfigSearch['keyword_min'] > 0) {
    $xoopsTpl->assign('search_rule', sprintf(_SR_KEYIGNORE, $xoopsConfigSearch['keyword_min']));
}

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$xoopsTpl->assign('xoops_module_header', $xoopsOption['xoops_module_header']);

require_once XOOPS_ROOT_PATH . '/footer.php';
