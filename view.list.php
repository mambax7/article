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

require_once __DIR__ . '/header.php';
/** @var Article\Helper $helper */
$helper = Article\Helper::getInstance();

// Valid types of articles for regular applications, except author view
$valid_types = [
    'p' => art_constant('MD_REGULAR'),
    'f' => art_constant('MD_FEATURED'),
    's' => art_constant('MD_SPOTLIGHT'),
    'a' => _ALL,
];

// Valid sort criteria for articles for regular applications, except spotlight
$valid_sorts = [
    'id'           => [
        'key'   => 'art_id',
        'title' => art_constant('MD_DEFAULT'),
    ],
    'time_publish' => [
        'key'   => 'art_time_publish',
        'title' => art_constant('MD_TIME'),
    ],
    'title'        => [
        'key'   => 'art_title',
        'title' => art_constant('MD_TITLE'),
    ],
    'rating'       => [
        'key'   => 'art_rating/art_rates',
        'tag'   => 'art_rating, art_rates',
        'title' => art_constant('MD_RATE'),
    ],
    'counter'      => [
        'key'   => 'art_counter',
        'title' => art_constant('MD_VIEWS'),
    ],
    'comments'     => [
        'key'   => 'art_comments',
        'title' => art_constant('MD_COMMENTS'),
    ],
    'trackbacks'   => [
        'key'   => 'art_trackbacks',
        'title' => art_constant('MD_TRACKBACKS'),
    ],
];
// Sort order
$valid_orders = [
    'DESC' => art_constant('MD_DESC'),
    'ASC'  => art_constant('MD_ASC'),
];

/*
 * Parse the variables
 */
if ($REQUEST_URI_parsed = art_parse_args($args_num, $args, $args_str)) {
    $args['start'] = !empty($args['start']) ? $args['start'] : @$args_num[0];
    $args['type']  = @$args_str[0];
    $args['sort']  = @$args_str[1];
    $args['order'] = @$args_str[2];
}

$category_id = \Xmf\Request::getInt('category', @$args['category'], 'GET');
$topic_id    = \Xmf\Request::getInt('topic', @$args['topic'], 'GET');
$uid         = \Xmf\Request::getInt('uid', @$args['uid'], 'GET');
$start       = \Xmf\Request::getInt('start', @$args['start'], 'GET');
$type        = \Xmf\Request::getString('type', @$args['type'], 'GET');
$sort        = \Xmf\Request::getString('sort', @$args['sort'], 'GET');
$order       = array_key_exists($order = mb_strtoupper(\Xmf\Request::getString('order', @$args['order'], 'GET')), $valid_orders) ? $order : 'DESC';

/*
 * Instantiate category object and check access permissions
 */
$tracks_extra    = [];
$category_obj    = null;
$categoryHandler = $helper->getHandler('Category', $GLOBALS['artdirname']);
if (!empty($topic_id)) {
    $topicHandler   = $helper->getHandler('Topic', $GLOBALS['artdirname']);
    $topic_obj      = $topicHandler->get($topic_id);
    $category_id    = $topic_obj->getVar('cat_id');
    $tracks_extra[] = [
        'title' => $topic_obj->getVar('top_title'),
        'link'  => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.topic.php' . URL_DELIMITER . $topic_id,
    ];
}
if (!empty($category_id)) {
    $category_obj = $categoryHandler->get($category_id);
    if (!$categoryHandler->getPermission($category_obj, 'access')) {
        redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php', 2, art_constant('MD_NOACCESS'));
    }
    $categories_id = [$category_id];
}

$tags = ['cat_title'];
if (!empty($uid)) {
    $tags[] = 'cat_moderator';
}
if (!$categories_obj = $categoryHandler->getAllByPermission('access', $tags)) {
    redirect_header('index.php', 2, art_constant('MD_NOACCESS'));
}
$categories_id = empty($categories_id) ? array_keys($categories_obj) : $categories_id;

/*
 * Instantiate user object
 */
if (null !== $category_obj) {
    $xoopsuser_is_admin = art_isAdministrator() || art_isModerator($category_obj);
} else {
    $xoopsuser_is_admin = art_isAdministrator();
}
$xoopsuser_is_author = false;
if (!empty($uid)) {
    if (!empty($xoopsUser) && $uid == $xoopsUser->getVar('uid')) {
        $author_obj          = $xoopsUser;
        $xoopsuser_is_author = true;
    } else {
        $memberHandler = xoops_getHandler('member');
        $author_obj    = $memberHandler->getUser($uid);
    }
    if (empty($author_obj) || !$author_obj->isActive()) {
        redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/index.php', 2, art_constant('MD_INVALID'));
    }
    $tracks_extra[] = [
        'title' => $author_obj->getVar('uname'),
        'link'  => XOOPS_URL . "/userinfo.php?uid={$uid}",
    ];

    unset($valid_types['s']);
    $valid_types_author = [];
    if ($xoopsuser_is_author) {
        $valid_types_author = [
            'c' => art_constant('MD_CREATED'),
            'm' => art_constant('MD_SUBMITTED'),
            'r' => art_constant('MD_REGISTERED'),
        ];
    } elseif ($xoopsuser_is_admin) {
        $valid_types_author = [
            'm' => art_constant('MD_SUBMITTED'),
            'r' => art_constant('MD_REGISTERED'),
        ];
    }
    $valid_types = array_merge($valid_types_author, $valid_types);
}

$type       = array_key_exists($type, $valid_types) ? $type : 'a';
$byCategory = true;
switch (mb_strtolower($type)) {
    case 'created':
    case 'c':
        $art_criteria = new \CriteriaCompo(new \Criteria('art_time_submit', 0));
        $art_criteria->add(new \Criteria('cat_id', 0), 'OR');
        $byCategory = false;
        break;
    case 'submitted':
    case 'm':
        $art_criteria = new \CriteriaCompo(new \Criteria('art_time_publish', 0));
        $art_criteria->add(new \Criteria('art_time_submit', 0, '>'));
        $art_criteria->add(new \Criteria('cat_id', 0, '>'));
        $byCategory = false;
        break;
    case 'registered':
    case 'r':
        $art_criteria = new \CriteriaCompo(new \Criteria('ac.ac_publish', 0));
        break;
    case 'published':
    case 'p':
        $art_criteria = new \CriteriaCompo(new \Criteria('ac.ac_publish', 0, '>'));
        $art_criteria->add(new \Criteria('ac.ac_feature', 0));
        break;
    case 'featured':
    case 'f':
        $art_criteria = new \CriteriaCompo(new \Criteria('ac.ac_feature', 0, '>'));
        break;
    case 'spotlight':
    case 's':
        $art_criteria = new \CriteriaCompo(new \Criteria('art_id', 0, '>'));
        $type         = 's';
        $_sort        = $valid_sorts['id'];
        unset($valid_sorts);
        $valid_sorts = ['id' => $_sort];
        $byCategory  = false;
        break;
    default:
        $art_criteria = new \CriteriaCompo(new \Criteria('ac.ac_publish', 0, '>'));
        $type         = 'a';
        break;
}
$type_title = $valid_types[$type];

// Disable cache for author since we don't have proper cache handling way for them
if ($xoopsuser_is_author) {
    $xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;
}
$xoopsOption['xoops_pagetitle'] = $xoopsModule->getVar('name') . (null === $category_obj ? '' : ' - ' . $category_obj->getVar('cat_title')) . ' - ' . (empty($author_obj) ? '' : ' - ' . $author_obj->getVar('uname')) . ' - ' . art_constant('MD_LIST') . ' - ' . $type_title;

$xoopsOption['template_main']       = art_getTemplate('list', $helper->getConfig('template'));
$xoopsOption['xoops_module_header'] = art_getModuleHeader($helper->getConfig('template'));
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $helper->getDirname() . '/include/vars.php';

$articleHandler = $helper->getHandler('Article', $GLOBALS['artdirname']);

if (!empty($uid)) {
    $art_criteria->add(new \Criteria(($byCategory ? 'a.' : '') . 'uid', $uid));
}
$sort = array_key_exists($sort, $valid_sorts) ? $sort : 'id';

$sp_data     = [];
$articles_id = [];
if ('s' === $type) {
    $spotlightHandler = $helper->getHandler('Spotlight', $GLOBALS['artdirname']);
    $articles_count   = $spotlightHandler->getCount($art_criteria);
    $art_criteria->setSort($valid_sorts[$sort]['key']);
    $art_criteria->setOrder($order);
    $art_criteria->setStart($start);
    $art_criteria->setLimit($helper->getConfig('articles_perpage'));
    $spotlights_obj = $spotlightHandler->getAll($art_criteria, ['art_id', 'sp_image', 'sp_note', 'sp_time']);
    foreach (array_keys($spotlights_obj) as $sid) {
        $articles_id[$spotlights_obj[$sid]->getVar('art_id')] = 1;
        $sp_data[$spotlights_obj[$sid]->getVar('art_id')]     = [
            'id'      => $spotlights_obj[$sid]->getVar('art_id'),
            //"image"        => $spotlights_obj[$sid]->getImage(),
            'time'    => $spotlights_obj[$sid]->getTime($helper->getConfig('timeformat')),
            'sp_note' => $spotlights_obj[$sid]->getVar('sp_note'),
        ];
    }
    $articles_id = array_keys($articles_id);
} elseif ($byCategory) {
    $articles_count = $articleHandler->getCountByCategory($categories_id, $art_criteria);
    $art_criteria->setSort($valid_sorts[$sort]['key']);
    $art_criteria->setOrder($order);
    $articles_id = $articleHandler->getIdsByCategory($categories_id, $helper->getConfig('articles_perpage'), $start, $art_criteria);
} else {
    $art_criteria->add(new \Criteria('cat_id', '(' . implode(', ', $categories_id) . ')', 'IN'));
    $articles_count = $articleHandler->getCount($art_criteria);
    $art_criteria->setSort($valid_sorts[$sort]['key']);
    $art_criteria->setOrder($order);
    $art_criteria->setLimit($helper->getConfig('articles_perpage'));
    $art_criteria->setStart($start);
    $articles_id = $articleHandler->getIds($art_criteria);
}
unset($art_criteria);

if (count($articles_id) > 0) {
    $art_criteria = new \Criteria('art_id', '(' . implode(', ', $articles_id) . ')', 'IN');
    $tags         = [
        'uid',
        'writer_id',
        'art_title',
        'art_image',
        'art_pages',
        'art_categories',
        'art_time_publish',
        'art_counter',
    ];
    if (!empty($helper->getConfig('display_summary'))) {
        $tags[] = 'art_summary';
    }
    if (!empty($valid_sorts[$sort]['tag']) && !in_array($valid_sorts[$sort]['tag'], $tags)) {
        $tags[] = $valid_sorts[$sort]['tag'];
    } elseif (!in_array($valid_sorts[$sort]['key'], $tags)) {
        $tags[] = $valid_sorts[$sort]['key'];
    }
    $articles_obj = $articleHandler->getAll($art_criteria, $tags);
} else {
    $articles_obj = [];
}

$author_array = [];
$writer_array = [];
$users        = [];
$writers      = [];
foreach (array_keys($articles_obj) as $id) {
    $author_array[$articles_obj[$id]->getVar('uid')]       = 1;
    $writer_array[$articles_obj[$id]->getVar('writer_id')] = 1;
}

if (!empty($author_array)) {
    require_once XOOPS_ROOT_PATH . '/modules/' . $helper->getDirname() . '/include/functions.author.php';
    $users = art_getAuthorNameFromId(array_keys($author_array), true, true);
}

if (!empty($writer_array)) {
    require_once XOOPS_ROOT_PATH . '/modules/' . $helper->getDirname() . '/include/functions.author.php';
    $writers = art_getWriterNameFromIds(array_keys($writer_array));
}

$articles = [];
foreach ($articles_id as $id) {
    $article  = &$articles_obj[$id];
    $_article = [
        'id'      => $id,
        'title'   => $articles_obj[$id]->getVar('art_title'),
        'author'  => @$users[$articles_obj[$id]->getVar('uid')],
        'writer'  => @$writers[$articles_obj[$id]->getVar('writer_id')],
        'time'    => $articles_obj[$id]->getTime($helper->getConfig('timeformat')),
        'counter' => $articles_obj[$id]->getVar('art_counter'),
        'image'   => $articles_obj[$id]->getVar('art_image') ? 1 : 0,
        'summary' => $article->getSummary(!empty($helper->getConfig('display_summary'))),
    ];
    if (in_array($sort, ['comments', 'trackbacks'])) {
        $_article[$sort] = $articles_obj[$id]->getVar($valid_sorts[$sort]['key']);
    }
    if ('rating' === $sort) {
        $_article[$sort] = $articles_obj[$id]->getRatingAverage() . '/' . (int)$articles_obj[$id]->getVar('art_rates');
    }
    if (empty($category_id)) {
        $cats = $article->getCategories();
        if (count($cats) > 0) {
            foreach ($cats as $catid) {
                if (0 == $catid || !isset($categories_obj[$catid])) {
                    continue;
                }
                $_article['categories'][$catid] = [
                    'id'    => $catid,
                    'title' => $categories_obj[$catid]->getVar('cat_title'),
                ];
            }
        }
    }
    $articles[$id] = $_article;
    unset($_article);
}

if (count($sp_data) > 0) {
    foreach (array_keys($articles) as $i) {
        $articles[$i]['note'] = $sp_data[$i]['sp_note'];
        $articles[$i]['time'] = $sp_data[$i]['time'];
    }
}

// The author's profile
if (!empty($author_obj)):

    if ('blank.gif' !== $author_obj->getVar('user_avatar')
        && file_exists(XOOPS_ROOT_PATH . '/uploads/' . $author_obj->getVar('user_avatar'))) {
        $avatar = XOOPS_URL . '/uploads/' . $author_obj->getVar('user_avatar');
    } else {
        $avatar = '';
    }
    $author = [
        'uid'    => $uid,
        'avatar' => $avatar,
        'uname'  => $author_obj->getVar('uname'),
    ];

    xoops_loadLanguage('user');
    if ($author_obj->getVar('name')) {
        $author['profiles'][] = [
            'title'   => _US_REALNAME,
            'content' => $author_obj->getVar('name'),
        ];
    }
    if ($author_obj->getVar('url')) {
        $author['profiles'][] = [
            'title'   => _US_WEBSITE,
            'content' => $author_obj->getVar('url'),
        ];
    }
    if ($author_obj->getVar('user_viewemail') || (!empty($xoopsUser) && $xoopsUser->isAdmin())) {
        $author['profiles'][] = [
            'title'   => _US_EMAIL,
            'content' => checkEmail($author_obj->getVar('email'), true),
        ];
    }
    if ($author_obj->getVar('bio')) {
        $author['profiles'][] = [
            'title'   => _US_EXTRAINFO,
            'content' => $author_obj->getVar('bio'),
        ];
    }

    if (empty($start)) {
        $criteria = new \CriteriaCompo(new \Criteria('art_time_publish', 0, '>'));
        $criteria->add(new \Criteria('uid', $uid));
        $count_articles = $articleHandler->getCount($criteria);
        unset($criteria);

        $criteria = new \CriteriaCompo(new \Criteria('a.uid', $uid));
        $criteria->add(new \Criteria('ac.ac_feature', 0, '>'));
        $count_featured = $articleHandler->getCountByCategory($categories_id, $criteria);
        unset($criteria);

        $criteria    = new \CriteriaCompo(new \Criteria('uid', $uid));
        $count_topic = $articleHandler->getCountByTopic($uid, $criteria);
        unset($criteria);

        $mods = [];
        if ($xoopsuser_is_admin) {
            $mods[] = [
                'url'   => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/',
                'title' => $xoopsModule->getVar('name'),
            ];
        }
        foreach ($categories_obj as $id => $cat_obj) {
            if (!@in_array($uid, $cat_obj->getVar('cat_moderator'))) {
                continue;
            }
            $mods[] = [
                'url'   => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.category.php' . URL_DELIMITER . $id,
                'title' => $cat_obj->getVar('cat_title'),
            ];
        }
        $author['mods']  = $mods;
        $author['stats'] = [
            'articles' => $count_articles,
            'featured' => $count_featured,
            'topics'   => $count_topic,
        ];
    }
endif;
// End of author profile

$pagequery = (!empty($topic_id) ? "t{$topic_id}/" : (!empty($category_id) ? "c{$category_id}/" : '')) . (empty($uid) ? '' : "u{$uid}/");

if ($articles_count > $helper->getConfig('articles_perpage')) {
    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
    $_query = $pagequery . (empty($uid) ? '' : "u{$uid}/") . (empty($type) ? 'a/' : "{$type}/") . (empty($sort) ? 'id' : "{$sort}/") . (empty($order) ? '' : "{$order}/");

    $nav     = new \XoopsPageNav($articles_count, $helper->getConfig('articles_perpage'), $start, 'start', $pagequery);
    $pagenav = $nav->renderNav(4);
} else {
    $pagenav = '';
}

$link_options = [];
$basic_link   = XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.list.php' . URL_DELIMITER . $pagequery;
foreach ($valid_types as $val => $name) {
    if ($val == $type) {
        $link_options['type'][] = $name;
    } else {
        $link_options['type'][] = "<a href=\"{$basic_link}{$val}\">{$name}</a>";
    }
}

$basic_link .= (empty($type) ? 'a/' : "{$type}/");
foreach ($valid_sorts as $val => $_sort) {
    if ($val == $sort) {
        $link_options['sort'][] = $_sort['title'];
    } else {
        $link_options['sort'][] = "<a href=\"{$basic_link}{$val}\">{$_sort['title']}</a>";
    }
}

$basic_link .= (empty($sort) ? 'id/' : "{$sort}/");
foreach ($valid_orders as $val => $name) {
    if ($val == $order) {
        $link_options['order'][] = $name;
    } else {
        $link_options['order'][] = "<a href=\"{$basic_link}{$val}\">{$name}</a>";
    }
}

$i         = 0;
$page_meta = [];
if (!empty($author_obj)):
    ++$i;
    $page_meta[$i] = [
        'title' => $author_obj->getVar('uname'),
        'link'  => XOOPS_URL . "/userinfo.php?uid={$uid}",
    ];
endif;
++$i;
$page_meta[$i] = [
    'title' => art_constant('MD_LIST'),
    'link'  => XOOPS_URL . '/modules/' . $GLOBALS['artdirname'] . '/view.list.php' . URL_DELIMITER . $pagequery,
];
++$i;
$page_meta[$i] = [
    'title' => $type_title,
    'link'  => $page_meta[$i - 1]['link'] . (empty($type) ? 'a/' : "{$type}/"),
];
if ('id' !== $sort):
    ++$i;
    $page_meta[$i] = [
        'title' => sprintf(art_constant('MD_SORTORDER'), $valid_sorts[$sort]['title'], $valid_orders[$order]),
        'link'  => $page_meta[$i - 1]['link'] . "{$sort}/{$order}/",
    ];
endif;

$xoopsTpl->assign('modulename', $xoopsModule->getVar('name'));
$xoopsTpl->assign_by_ref('articles', $articles);
$xoopsTpl->assign_by_ref('author', $author);

if (null !== $category_obj) {
    $xoopsTpl->assign('tracks', $categoryHandler->getTrack($category_obj, true));
}
$xoopsTpl->assign_by_ref('tracks_extra', $tracks_extra);
$xoopsTpl->assign_by_ref('page_meta', $page_meta);
$xoopsTpl->assign_by_ref('options', $link_options);
$xoopsTpl->assign_by_ref('pagenav', $pagenav);

// Loading module meta data, NOT THE RIGHT WAY DOING IT
$xoopsTpl->assign('xoops_module_header', $xoopsOption['xoops_module_header']);
$xoopsTpl->assign('xoops_pagetitle', $xoopsOption['xoops_pagetitle']);

require_once __DIR__ . '/footer.php';
