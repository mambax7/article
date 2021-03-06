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

require __DIR__ . '/preloads/autoloader.php';

require_once dirname(dirname(__DIR__)) . '/mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/include/vars.php';
mod_loadFunctions('', $xoopsModule->getVar('dirname'));

art_define_url_delimiter();
$myts = \MyTextSanitizer::getInstance();

/** @var \XoopsModules\Article\Helper $helper */
$helper = \XoopsModules\Article\Helper::getInstance();
