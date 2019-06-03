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
require_once __DIR__ . '/header.php';

$helper      = \XoopsModules\Article\Helper::getInstance();
$topic_id    = \Xmf\Request::getInt('topic', 0, 'GET');
$category_id = \Xmf\Request::getInt('category', 0, 'GET');
$from        = \Xmf\Request::getInt('from', 0, 'GET');

$topicHandler = $helper->getHandler('Topic', $GLOBALS['artdirname']);
$topic_obj    = $topicHandler->get($topic_id);

$category_id = empty($category_id) ? $topic_obj->getVar('cat_id') : $category_id;
/*
 $categoryHandler = \XoopsModules\Article\Helper::getInstance()->getHandler("Category", $GLOBALS["artdirname"]);
 $category_obj = $categoryHandler->get($category_id);

 if( !$categoryHandler->getPermission($category_obj, "moderate")
 || !art_isAdministrator())
 {
 redirect_header(XOOPS_URL."/modules/".$GLOBALS["artdirname"]."/index.php", 2, art_constant("MD_NOACCESS"));
 }
 */
// Disable cache
$xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/include/form.topic.php';
require_once XOOPS_ROOT_PATH . '/footer.php';
