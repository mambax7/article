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

// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

use XoopsModules\Article;

require_once __DIR__ . '/../class/Helper.php';
//require_once __DIR__ . '/../include/common.php';
$helper = Article\Helper::getInstance();

$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$adminmenu[] = [
    'title' => art_constant('MI_ADMENU_HOME'),
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png',
];

$adminmenu[] = [
    'title' => art_constant('MI_ADMENU_INDEX'),
    'link'  => 'admin/main.php',
    'icon'  => $pathIcon32 . '/home.png',
];

$adminmenu[] = [
    'title' => art_constant('MI_ADMENU_CATEGORY'),
    'link'  => 'admin/admin.category.php',
    'icon'  => $pathIcon32 . '/category.png',
];

$adminmenu[] = [
    'title' => art_constant('MI_ADMENU_TOPIC'), //_MI_ADMENU_TOPIC,
    'link'  => 'admin/admin.topic.php',
    'icon'  => $pathIcon32 . '/category.png',
];

$adminmenu[] = [
    'title' => art_constant('MI_ADMENU_ARTICLE'), //_MI_ADMENU_ARTICLE,
    'link'  => 'admin/admin.article.php',
    'icon'  => $pathIcon32 . '/index.png',
];

$adminmenu[] = [
    'title' => art_constant('MI_ADMENU_PERMISSION'),
    'link'  => 'admin/admin.permission.php',
    'icon'  => $pathIcon32 . '/permissions.png',
];

$adminmenu[] = [
    'title' => art_constant('MI_ADMENU_SPOTLIGHT'),
    'link'  => 'admin/admin.spotlight.php',
    'icon'  => $pathIcon32 . '/highlight.png',
];

$adminmenu[] = [
    'title' => art_constant('MI_ADMENU_TRACKBACK'),
    'link'  => 'admin/admin.trackback.php',
    'icon'  => $pathIcon32 . '/category.png',
];

$adminmenu[] = [
    'title' => art_constant('MI_ADMENU_FILE'),
    'link'  => 'admin/admin.file.php',
    'icon'  => $pathIcon32 . '/home.png',
];

$adminmenu[] = [
    'title' => art_constant('MI_ADMENU_UTILITY'),
    'link'  => 'admin/admin.synchronization.php',
    'icon'  => $pathIcon32 . '/home.png',
];

$adminmenu[] = [
    'title' => art_constant('MI_ADMENU_ABOUT'),
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png',
];
