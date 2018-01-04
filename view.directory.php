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

// Set groups, template, header for cache purposes
if (!empty($xoopsUser)) {
    $xoopsOption['cache_group'] = implode(',', $xoopsUser->groups());
}
$GLOBALS['xoopsOption']['template_main'] = art_getTemplate('directory', $xoopsModuleConfig['template']);
$xoops_module_header                     = art_getModuleHeader($xoopsModuleConfig['template']) . '
    <link rel="alternate" type="application/rss+xml" title="' . $xoopsModule->getVar('name') . ' rss" href="' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/xml.php' . URL_DELIMITER . 'rss">
    <link rel="alternate" type="application/rss+xml" title="' . $xoopsModule->getVar('name') . ' rdf" href="' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/xml.php' . URL_DELIMITER . 'rdf">
    <link rel="alternate" type="application/atom+xml" title="' . $xoopsModule->getVar('name') . ' atom" href="' . XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/xml.php' . URL_DELIMITER . 'atom">
    ';

$xoopsOption['xoops_module_header'] = $xoops_module_header;
require_once XOOPS_ROOT_PATH . '/header.php';
include XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';

// Following part will not be executed if cache enabled

if (art_parse_args($args_num, $args, $args_str)) {
    $args['category'] = !empty($args['category']) ? $args['category'] : @$args_num[0];
}
$category_id = (int)(empty($_GET['category']) ? @$args['category'] : $_GET['category']);

$categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
$category_obj    = $categoryHandler->get($category_id);

$category_depth = 2;
$data           = $categoryHandler->getArrayTree($category_id, 'access', null, $category_depth);
$counts_article = $categoryHandler->getArticleCounts();

$category_data = [];
$tracks        = [];
if (!$category_obj->isNew()) {
    $category_data               = [
        'id'          => $category_obj->getVar('cat_id'),
        'title'       => $category_obj->getVar('cat_title'),
        'description' => $category_obj->getVar('cat_description'),
        'image'       => $category_obj->getImage(),
        'articles'    => (int)$counts_article[$category_id]
    ];
    $topicHandler                = xoops_getModuleHandler('topic', $GLOBALS['artdirname']);
    $category_data['topics']     = $topicHandler->getCount(new Criteria('cat_id', $category_id));
    $category_data['categories'] = count(@$data['child']);
    $tracks                      = $categoryHandler->getTrack($category_obj, true);
}

if (!empty($data['child'])):
    foreach (array_keys($data['child']) as $key) {
        if (empty($data['child'][$key])) {
            continue;
        }
        $data['child'][$key]['count'] = @(int)$counts_article[$key];
        if (empty($data['child'][$key]['child'])) {
            continue;
        }
        foreach (array_keys($data['child'][$key]['child']) as $skey) {
            $data['child'][$key]['child'][$skey]['count'] = @(int)$counts_article[$skey];
            if ($subcats = art_getSubCategory($skey)):
                foreach (@$subcats as $subcat) {
                    $data['child'][$key]['child'][$skey]['count'] += @(int)$counts_article[$subcat];
                }
            endif;
        }
    }
endif;
unset($counts_article);

$xoopsTpl->assign('modulename', $xoopsModule->getVar('name'));
$xoopsTpl->assign_by_ref('tracks', $tracks);
$xoopsTpl->assign_by_ref('categories', $data);
$xoopsTpl->assign_by_ref('category', $category_data);

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$xoopsTpl->assign('xoops_module_header', $xoopsOption['xoops_module_header']);

require_once __DIR__ . '/footer.php';
