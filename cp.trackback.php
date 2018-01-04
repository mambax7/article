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

include __DIR__ . '/header.php';

$category_id = (int)(empty($_GET['category']) ? @$_POST['category'] : $_GET['category']);
$article_id  = (int)(empty($_GET['article']) ? @$_POST['article'] : $_GET['article']);
$start       = (int)(empty($_GET['start']) ? @$_POST['start'] : $_GET['start']);
$type        = empty($_GET['type']) ? @$_POST['type'] : $_GET['type'];
$from        = (!empty($_GET['from']) || !empty($_POST['from'])) ? 1 : 0;

$categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
$category_obj    = $categoryHandler->get($category_id);
if ((!empty($category_id) && !$categoryHandler->getPermission($category_obj, 'moderate'))
    || (empty($category_id)
        && !art_isAdministrator())) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.category.php' . URL_DELIMITER . $category_id, 2, art_constant('MD_NOACCESS'));
}

$xoopsOption['xoops_pagetitle']     = $xoopsModule->getVar('name') . ' - ' . art_constant('MD_CPTRACKBACK');
$template                           = (empty($category_obj) ? $xoopsModuleConfig['template'] : $category_obj->getVar('cat_template'));
$xoopsOption['template_main']       = art_getTemplate('cptrackback', $template);
$xoopsOption['xoops_module_header'] = art_getModuleHeader($template);

// Disable cache
$xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;

require_once XOOPS_ROOT_PATH . '/header.php';
include XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

$criteria = new CriteriaCompo();
if (!empty($category_id)) {
    $criteria->add(new Criteria('cat_id', $category_id));
}
if (!empty($article_id)) {
    $criteria->add(new Criteria('art_id', $article_id));
}

if ('pending' === $type) {
    $criteria->add(new Criteria('tb_status', 0));
    $type_name = art_constant('MD_PENDING');
} elseif ('approved' === $type) {
    $criteria->add(new Criteria('tb_status', 0, '>'));
    $type_name = art_constant('MD_APPROVED');
} else {
    $type_name = _ALL;
}

$trackbackHandler = xoops_getModuleHandler('trackback', $GLOBALS['artdirname']);
$tb_count         = $trackbackHandler->getCount($criteria);
$criteria->setStart($start);
$criteria->setLimit($xoopsModuleConfig['articles_perpage']);
$trackbacks_obj = $trackbackHandler->getAll($criteria);

$articleIds   = [];
$trackbacks   = [];
$article_list = [];
if (!empty($article_id)) {
    $articleIds[$article_id] = 1;
} elseif (count($trackbacks_obj) > 0) {
    foreach ($trackbacks_obj as $id => $trackback) {
        $trackbacks[]                             = [
            'id'      => $id,
            'art_id'  => $trackback->getVar('art_id'),
            'title'   => $trackback->getVar('tb_title'),
            'url'     => $trackback->getVar('tb_url'),
            'excerpt' => $trackback->getVar('tb_excerpt'),
            'time'    => $trackback->getTime($xoopsModuleConfig['timeformat']),
            'ip'      => $trackback->getIp(),
            'name'    => $trackback->getVar('tb_blog_name'),
        ];
        $articleIds[$trackback->getVar('art_id')] = 1;
    }
}
$article_list = [];
if (!empty($articleIds)) {
    $articleHandler = xoops_getModuleHandler('article', $GLOBALS['artdirname']);
    $criteria       = new CriteriaCompo(new Criteria('art_id', '(' . implode(',', array_keys($articleIds)) . ')', 'IN'));
    $article_list   = $articleHandler->getList($criteria);
}
foreach (array_keys($trackbacks) as $i) {
    if (empty($article_list[$trackbacks[$i]['art_id']])) {
        continue;
    }
    $trackbacks[$i]['article'] = $article_list[$trackbacks[$i]['art_id']]['title'];
}

if ($tb_count > $xoopsModuleConfig['articles_perpage']) {
    include XOOPS_ROOT_PATH . '/class/pagenav.php';
    $nav     = new XoopsPageNav($tb_count, $xoopsModuleConfig['articles_perpage'], $start, 'start', "category={$category_id}&amp;type={$type}&amp;from={$from}");
    $pagenav = $nav->renderNav(4);
} else {
    $pagenav = '';
}

$category_data = [];
if (!empty($category_id)) {
    $categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
    $category_obj    = $categoryHandler->get($category_id);
    $category_data   = [
        'id'          => $category_obj->getVar('cat_id'),
        'title'       => $category_obj->getVar('cat_title'),
        'description' => $category_obj->getVar('cat_description'),
        'trackbacks'  => $tb_count
    ];
}

$article_data = [];
if (!empty($article_id)) {
    $articleHandler = xoops_getModuleHandler('article', $GLOBALS['artdirname']);
    $article_obj    = $articleHandler->get($article_id);
    $article_data   = [
        'id'          => $article_obj->getVar('cat_id'),
        'title'       => $article_obj->getVar('art_title'),
        'description' => $article_obj->getSummary(),
        'trackbacks'  => $tb_count
    ];
}

$xoopsTpl->assign('modulename', $xoopsModule->getVar('name'));
$xoopsTpl->assign('from', $from);
$xoopsTpl->assign('start', $start);
$xoopsTpl->assign('type', $type);
$xoopsTpl->assign('type_name', $type_name);
$xoopsTpl->assign_by_ref('category', $category_data);
$xoopsTpl->assign_by_ref('article', $article_data);
$xoopsTpl->assign_by_ref('trackbacks', $trackbacks);
$xoopsTpl->assign_by_ref('pagenav', $pagenav);

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$xoopsTpl->assign('xoops_module_header', $xoopsOption['xoops_module_header']);
$xoopsTpl->assign('xoops_pagetitle', $xoopsOption['xoops_pagetitle']);

require_once __DIR__ . '/footer.php';
