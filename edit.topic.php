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

$topic_id    = empty($_GET['topic']) ? 0 : (int)$_GET['topic'];
$category_id = empty($_GET['category']) ? 0 : (int)$_GET['category'];
$from        = empty($_GET['from']) ? 0 : (int)$_GET['from'];

$topicHandler = xoops_getModuleHandler('topic', $GLOBALS['artdirname']);
$topic_obj    = $topicHandler->get($topic_id);

$category_id = empty($category_id) ? $topic_obj->getVar('cat_id') : $category_id;
/*
 $categoryHandler = xoops_getModuleHandler("category", $GLOBALS["artdirname"]);
 $category_obj = $categoryHandler->get($category_id);

 if( !$categoryHandler->getPermission($category_obj, "moderate")
 || !art_isAdministrator())
 {
 redirect_header(XOOPS_URL."/modules/".$GLOBALS["artdirname"]."/index.php", 2, art_constant("MD_NOACCESS"));
 }
 */
// Disable cache
$xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
include XOOPS_ROOT_PATH . '/header.php';
include XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';
include XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/include/form.topic.php';
include XOOPS_ROOT_PATH . '/footer.php';
