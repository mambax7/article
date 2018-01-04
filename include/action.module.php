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

include __DIR__ . '/vars.php';
mod_loadFunctions('parse', $GLOBALS['artdirname']);

art_parse_function('
function xoops_module_install_[DIRNAME](XoopsModule $module)
{
    [DIRNAME]_updateXoopsConfig();

    $data_file = XOOPS_ROOT_PATH . "/modules/" . $GLOBALS["artdirname"] . "/sql/mysql." . $GLOBALS["xoopsConfig"]["language"] . ".sql";
    if (!file_exists($data_file)) {
        $data_file = XOOPS_ROOT_PATH . "/modules/" . $GLOBALS["artdirname"] . "/sql/mysql.english.sql";
    }
    // The mysql data was exported with UTF-8, so restore it
    $GLOBALS["xoopsDB"]->queryF("SET NAMES utf8");
    if (!$GLOBALS["xoopsDB"]->queryFromFile($data_file)) {
        $module->setErrors("Pre-set data were not installed");

        return true;
    }

    /* Set tags */
    mod_loadFunctions("recon", $GLOBALS["artdirname"]);
    art_updateTag($module->getVar("mid"));

    require_once(XOOPS_ROOT_PATH . "/modules/" . $GLOBALS["artdirname"] . "/class/permission.php");

    /* Set corresponding permissions for categories and articles */
    $module_id = $module->getVar("mid") ;
    $gpermHandler = xoops_getHandler("groupperm");
    $groups_view = array(XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS);
    $groups_post = array(XOOPS_GROUP_USERS);
    $groups_admin = array(XOOPS_GROUP_ADMIN);

    $view_items = array("access", "view");
    $post_items = array("submit", "rate");
    $admin_items = array("publish", "moderate");

    $categoryHandler = xoops_getModuleHandler("category", $GLOBALS["artdirname"]);
    $cat_ids = $categoryHandler->getIds();

    foreach ($groups_view as $group_id) {
        $gpermHandler->addRight("global", $GLOBALS["perms_global"]["search"]["id"], $group_id, $module_id);
        foreach ($view_items as $item) {
            foreach ($cat_ids as $id) {
                $gpermHandler->addRight($item, $id, $group_id, $module_id);
            }
        }
    }
    foreach ($groups_post as $group_id) {
        $gpermHandler->addRight("global", $GLOBALS["perms_global"]["html"]["id"], $group_id, $module_id);
        foreach ($post_items as $item) {
            foreach ($cat_ids as $id) {
                $gpermHandler->addRight($item, $id, $group_id, $module_id);
            }
        }
    }
    foreach ($groups_admin as $group_id) {
        $gpermHandler->addRight("global", $GLOBALS["perms_global"]["upload"]["id"], $group_id, $module_id);
        foreach ($admin_items as $item) {
            foreach ($cat_ids as $id) {
                $gpermHandler->addRight($item, $id, $group_id, $module_id);
            }
        }
    }

    return true;
}

function xoops_module_pre_install_[DIRNAME](XoopsModule $module)
{
    $mod_tables = $module->getInfo("tables");
    foreach ($mod_tables as $table) {
        $GLOBALS["xoopsDB"]->queryF("DROP TABLE IF EXISTS " . $GLOBALS["xoopsDB"]->prefix($table) . ";");
    }

    return [DIRNAME]_setModuleConfig($module);
}

function xoops_module_pre_update_[DIRNAME](XoopsModule $module)
{
    return [DIRNAME]_setModuleConfig($module);
}

function xoops_module_update_[DIRNAME](XoopsModule $module, $prev_version = null)
{
    //if ($prev_version < 100) {
        [DIRNAME]_updateXoopsConfig();
    //}

    load_functions("config");
    mod_clearConfg($module->getVar("dirname", "n"));

    if ($prev_version < 96) {
        $GLOBALS["xoopsDB"]->queryFromFile(XOOPS_ROOT_PATH . "/modules/" . $GLOBALS["artdirname"] . "/sql/mysql.096.sql");
    }
    if ($prev_version < 97) {
        require_once __DIR__ . "/module.v097.php";
        xoops_module_update_art_v097($module);
    }
    if ($prev_version < 100) {
        $GLOBALS["xoopsDB"]->queryFromFile(XOOPS_ROOT_PATH . "/modules/" . $GLOBALS["artdirname"] . "/sql/mysql.100.sql");
    }

    mod_loadFunctions("recon", $GLOBALS["artdirname"]);
    art_synchronization();

    // Update permissions
    $permissionHandler = xoops_getModuleHandler("permission", $GLOBALS["artdirname"]);
    $permissionHandler->createPermData();

    // Clear caches
    load_functions("cache");
    $dirname = $GLOBALS["artdirname"];
    mod_clearSmartyCache("/(^{$dirname}\^.*\.html$|blk_.*{$dirname}[^\.]*\.html$)/");

    // Update templates
    mod_loadFunctions("render", $GLOBALS["artdirname"]);
    art_template_lookup();
    art_template_lookup(true);

    // remove old html template files
    $template_directory = XOOPS_ROOT_PATH . "/modules/" . $module->getVar("dirname", "n") . "/templates/";
    $template_list      = array_diff(scandir($template_directory, SCANDIR_SORT_NONE), array("..", "."));
    foreach ($template_list as $k => $v) {
        $fileinfo = new SplFileInfo($template_directory . $v);
        if ($fileinfo->getExtension() == "html" && $fileinfo->getFilename() != "index.html") {
            @unlink($template_directory . $v);
        }
    }

    //delete .html entries from the tpl table
    $sql = "DELETE FROM " . $xoopsDB->prefix("tplfile") . " WHERE `tpl_module` = \'" .$module->getVar(\'dirname\', \'n\') . "\' AND `tpl_file` LIKE \'%.html%\'";
    $xoopsDB->queryF($sql);

    return true;
}

function [DIRNAME]_updateXoopsConfig()
{
    $GLOBALS["xoopsDB"]->queryF("ALTER TABLE " . $GLOBALS["xoopsDB"]->prefix("config") . " CHANGE `conf_name` `conf_name` varchar(64) NOT NULL default " . $GLOBALS["xoopsDB"]->quoteString(""));
    $GLOBALS["xoopsDB"]->queryF("ALTER TABLE " . $GLOBALS["xoopsDB"]->prefix("config") . " CHANGE `conf_title` `conf_title` varchar(64) NOT NULL default " . $GLOBALS["xoopsDB"]->quoteString(""));
    $GLOBALS["xoopsDB"]->queryF("ALTER TABLE " . $GLOBALS["xoopsDB"]->prefix("config") . " CHANGE `conf_desc` `conf_desc` varchar(64) NOT NULL default " . $GLOBALS["xoopsDB"]->quoteString(""));

    $GLOBALS["xoopsDB"]->queryF("ALTER TABLE " . $GLOBALS["xoopsDB"]->prefix("configcategory") . " CHANGE `confcat_name` `confcat_name` varchar(64) NOT NULL default " . $GLOBALS["xoopsDB"]->quoteString(""));

    $GLOBALS["xoopsDB"]->queryF("ALTER TABLE " . $GLOBALS["xoopsDB"]->prefix("xoopsnotifications") . " CHANGE `not_category` `not_category` varchar(64) NOT NULL default " . $GLOBALS["xoopsDB"]->quoteString(""));
    $GLOBALS["xoopsDB"]->queryF("ALTER TABLE " . $GLOBALS["xoopsDB"]->prefix("xoopsnotifications") . " CHANGE `not_event` `not_event` varchar(64) NOT NULL default " . $GLOBALS["xoopsDB"]->quoteString(""));

    return true;
}

function [DIRNAME]_setModuleConfig(XoopsModule $module)
{
    return true;
}
');
