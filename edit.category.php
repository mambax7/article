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

$category_id = \Xmf\Request::getInt('category', 0, 'GET');
$from        = empty($_GET['from']) ? 0 : 1;

$categoryHandler = xoops_getModuleHandler('category', $GLOBALS['artdirname']);
$category_obj    = $categoryHandler->get($category_id);
if ((!empty($category_id) && !$categoryHandler->getPermission($category_obj, 'moderate'))
    || (empty($category_id)
        && !art_isAdministrator())) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php', 2, art_constant('MD_NOACCESS'));
}
// Disable cache
$xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
include XOOPS_ROOT_PATH . '/header.php';
include XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';
include XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['artdirname'] . '/include/form.category.php';
include XOOPS_ROOT_PATH . '/footer.php';
