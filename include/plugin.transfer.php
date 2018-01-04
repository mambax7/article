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

if (!@require_once XOOPS_ROOT_PATH . '/Frameworks/transfer/transfer.php') {
    return null;
}

// Specify the addons to skip for the module
$GLOBALS['addons_skip_module'] = [];
// Maximum items to show on page
$GLOBALS['addons_limit_module'] = 5;

class ModuleTransferHandler extends TransferHandler
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get valid addon list
     *
     * @param array   $skip Addons to skip
     * @param boolean $sort To sort the list upon 'level'
     *                      return    array    $list
     */
    public function &getList($skip = [], $sort = true)
    {
        $list = parent::getList($skip, $sort);

        return $list;
    }

    /**
     * If need change config of an item
     * 1 parent::load_item
     * 2 $this->config
     * 3 $this->do_transfer
     * @param $item
     * @param $data
     * @return
     */
    public function do_transfer($item, &$data)
    {
        $ret = parent::do_transfer($item, $data);

        if ('newbb' == $item && !empty($ret['data']['topic_id'])) {
            $articleHandler = xoops_getModuleHandler('article', $GLOBALS['xoopsModule']->getVar('dirname'));
            $article_obj    = $articleHandler->get($data['id']);
            $article_obj->setVar('art_forum', $ret['data']['topic_id']);
            $articleHandler->insert($article_obj, true);
        }

        return $ret;
    }
}
