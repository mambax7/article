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

if (!class_exists('File')) {
    class File extends \XoopsObject
    {
        //var $db;
        //var $table;

        public function __construct($id = null)
        {
            //$this->ArtObject();
            //$this->db = \XoopsDatabaseFactory::getDatabaseConnection();
            $this->table = art_DB_prefix('file');
            $this->initVar('file_id', XOBJ_DTYPE_INT, null);
            $this->initVar('art_id', XOBJ_DTYPE_INT, 0, true);
            //$this->initVar("file_uid", XOBJ_DTYPE_INT, 0);
            $this->initVar('file_name', XOBJ_DTYPE_TXTBOX, '', true);
        }
    }
}
