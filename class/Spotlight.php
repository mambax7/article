<?php

namespace XoopsModules\Article;

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

// defined('XOOPS_ROOT_PATH') || die('Restricted access');
require_once dirname(__DIR__) . '/include/vars.php';
mod_loadFunctions('parse', $GLOBALS['artdirname']);

if (!class_exists('Spotlight')) {
    class Spotlight extends \XoopsObject
    {
        public function __construct($id = null)
        {
            //$this->ArtObject();
            //$this->table = art_DB_prefix("spotlight");
            $this->initVar('sp_id', XOBJ_DTYPE_INT, 0);
            $this->initVar('art_id', XOBJ_DTYPE_INT, 0);
            $this->initVar('uid', XOBJ_DTYPE_INT, 0);
            $this->initVar('sp_time', XOBJ_DTYPE_INT);
            $this->initVar('sp_image', XOBJ_DTYPE_ARRAY, []);
            $this->initVar('sp_categories', XOBJ_DTYPE_ARRAY, []);
            $this->initVar('sp_note', XOBJ_DTYPE_TXTAREA, '');

            $this->initVar('dohtml', XOBJ_DTYPE_INT, 1);
            $this->initVar('dosmiley', XOBJ_DTYPE_INT, 1);
            $this->initVar('doxcode', XOBJ_DTYPE_INT, 1);
            $this->initVar('doimage', XOBJ_DTYPE_INT, 1);
            $this->initVar('dobr', XOBJ_DTYPE_INT, 1);
        }

        public function getImage()
        {
            $image = $this->getVar('sp_image');
            if (!empty($image['file'])) {
                mod_loadFunctions('url', $GLOBALS['artdirname']);
                $image['url'] = art_getImageUrl($image['file']);
            } else {
                $image = [];
            }

            return $image;
        }

        public function getTime($format = '')
        {
            if (empty($format)) {
                if (!is_object($GLOBALS['xoopsModule'])
                    || $GLOBALS['xoopsModule']->getVar('dirname') != $GLOBALS['artdirname']) {
                    /** @var \XoopsModuleHandler $moduleHandler */
                    $moduleHandler = xoops_getHandler('module');
                    $artModule     = $moduleHandler->getByDirname($GLOBALS['artdirname']);
                    $configHandler = xoops_getHandler('config');
                    $artConfig     = $configHandler->getConfigsByCat(0, $artModule->getVar('mid'));
                    $format        = $artConfig['timeformat'];
                } else {
                    $format = $GLOBALS['xoopsModuleConfig']['timeformat'];
                }
            }
            mod_loadFunctions('time', $GLOBALS['artdirname']);
            $time = art_formatTimestamp($this->getVar('sp_time'), $format);

            return $time;
        }
    }
}
