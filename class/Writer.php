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

if (!class_exists('Writer')) {
    class Writer extends \XoopsObject
    {
        public function __construct($id = null)
        {
            //$this->ArtObject();
            //$this->table = art_DB_prefix("writer");
            $this->initVar('writer_id', XOBJ_DTYPE_INT, null);
            $this->initVar('uid', XOBJ_DTYPE_INT); // submitter for the author
            //$this->initVar("writer_uid", XOBJ_DTYPE_INT, 0, true); // uid of the author if registered
            $this->initVar('writer_name', XOBJ_DTYPE_TXTBOX);
            $this->initVar('writer_avatar', XOBJ_DTYPE_TXTBOX);
            $this->initVar('writer_profile', XOBJ_DTYPE_TXTAREA);

            $this->initVar('dohtml', XOBJ_DTYPE_INT, 1);
            $this->initVar('dosmiley', XOBJ_DTYPE_INT, 1);
            $this->initVar('doxcode', XOBJ_DTYPE_INT, 1);
            $this->initVar('doimage', XOBJ_DTYPE_INT, 1);
            $this->initVar('dobr', XOBJ_DTYPE_INT, 1);
        }
    }
}
