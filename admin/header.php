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

include dirname(__DIR__) . '/preloads/autoloader.php';

require dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
require dirname(__DIR__) . '/include/common.php';

require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/functions.php';
require_once XOOPS_ROOT_PATH . '/Frameworks/art/functions.admin.php';

$moduleDirName = basename(dirname(__DIR__));

/** @var \XoopsModules\Article\Helper $helper */
$helper = \XoopsModules\Article\Helper::getInstance();

/** @var \Xmf\Module\Admin $adminObject */
$adminObject = \Xmf\Module\Admin::getInstance();

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('common');

art_define_url_delimiter();
$myts = \MyTextSanitizer::getInstance();
