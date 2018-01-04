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
include __DIR__ . '/../../mainfile.php';
$xoopsLogger->activated = false;

// Set to 1 if upload is disabled
define('FCKUPLOAD_DISABLED', 0);

// Set the upload directory
define('XOOPS_FCK_FOLDER', $xoopsModule->getVar('dirname', 'n'));

// Usually no need to change this
chdir(XOOPS_ROOT_PATH . '/class/xoopseditor/fckeditor/fckeditor/editor/filemanager/connectors/php/');
require_once __DIR__ . '/upload.php';
