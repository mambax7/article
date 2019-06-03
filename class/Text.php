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

if (!class_exists('Text')) {
    class Text extends \XoopsObject
    {
        public function __construct($id = null)
        {
            //$this->ArtObject();
            //$this->table = art_DB_prefix("text");
            $this->initVar('text_id', XOBJ_DTYPE_INT, null, false);
            $this->initVar('art_id', XOBJ_DTYPE_INT, 0, true);
            $this->initVar('text_title', XOBJ_DTYPE_TXTBOX, '');
            $this->initVar('text_body', XOBJ_DTYPE_TXTAREA, '', true);

            $this->initVar('dohtml', XOBJ_DTYPE_INT, 1);
            $this->initVar('dosmiley', XOBJ_DTYPE_INT, 1);
            $this->initVar('doxcode', XOBJ_DTYPE_INT, 1);
            $this->initVar('doimage', XOBJ_DTYPE_INT, 1);
            $this->initVar('dobr', XOBJ_DTYPE_INT, 0);        // Concerning html tags, the dobr is set to 0 by default
        }
    }
}
