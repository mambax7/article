<?php
/**
 *  FCKeditor adapter for XOOPS
 *
 * @copyright      XOOPS Project (https://xoops.org)
 * @license        {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @package        core
 * @subpackage     xoopseditor
 * @since          2.3.0
 * @author         Taiwen Jiang <phppp@users.sourceforge.net>
 */
include  dirname(dirname(__DIR__)) . '/mainfile.php';
$xoopsLogger->activated = false;

define('XOOPS_FCK_FOLDER', $xoopsModule->getVar('dirname', 'n'));
chdir(XOOPS_ROOT_PATH . '/class/xoopseditor/fckeditor/fckeditor/editor/filemanager/connectors/php/');
require_once __DIR__ . '/connector.php';
