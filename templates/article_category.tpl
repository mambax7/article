<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->

<!-- page breadcrumbs -->
<div class="article-breadcrumbs head">
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/"><{$modulename}></a>
    <{foreach item=track from=$tracks}>
        ::
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$track.id}>"><{$track.title}></a>
        <{if $featured}>
            (<{php}>echo art_constant("MD_FEATURED");<{/php}>)
        <{/if}>
    <{/foreach}>
</div>


<!-- category meta data -->
<div class="article-section category-header">

    <{if $category.image}>
        <div class="article-header-image"><img src="<{$category.image}>" alt="<{$category.title}>"></div>
    <{/if}>

    <h2 class="article-title"><{$category.title}></h2>

    <div style="padding: 10px 0;"><{$category.description}></div>

    <div class="article-meta">

        <{if $featured}>
            <a href="#feature"><{php}>echo art_constant("MD_FEATURED");<{/php}></a>
        <{else}>
            <a href="#article"><{php}>echo art_constant("MD_ARTICLES");<{/php}></a>
        <{/if}>
        : <{$category.articles}>
        <{if $count_featured}>
            (
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$category.id}>/featured/"><{php}>echo art_constant("MD_FEATURED");<{/php}></a>
            : <{$count_featured}>)
        <{/if}>

        <{if $categories|is_array && count($categories) > 0 }>
            |
            <a href="#category"><{php}>echo art_constant("MD_CATEGORIES");<{/php}></a>
            : <{$categories|@count}>
        <{/if}>

        <{if $topics|is_array && count($topics) > 0}>
            |
            <a href="#topic"><{php}>echo art_constant("MD_TOPICS");<{/php}></a>
            : <{$topics|@count}>
        <{/if}>

        <{if $category.moderators|is_array && count($category.moderators) > 0 }>
            <br>
            <{php}>echo art_constant("MD_MODERATOR");<{/php}>:
            <{foreach item=moderator key=muid from=$category.moderators}>
                <span><{$moderator}></span>
            <{/foreach}>
        <{/if}>

    </div>

    <div class="clear"></div>
</div>


<{assign var="default_image" value="`$xoops_url`/modules/`$xoops_dirname`/assets/images/xoops.png"}>

<!-- Featured articles -->
<{if $features|is_array && count($features) > 0 }>
<div class="article-section article-feature">
    <div class="article-section-title">
        <span class="subject"><{php}>echo art_constant("MD_FEATURED");<{/php}></span>
        <span class="navigation"><a
                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.list.php<{$smarty.const.URL_DELIMITER}>c<{$category.id}>/f"><{$smarty.const._MORE}></a></span>
    </div>

    <div class="article-section-container">
        <{foreach item=article from=$features}>
            <div class="article-list">
                <div class="article-header-image"><img src="<{$article.image.url|default:$default_image}>"
                                                       alt="<{$article.title}>"><br><{$article.image.caption}></div>

                <div class="article-title">
                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.article.php<{$smarty.const.URL_DELIMITER}><{$article.id}>"><{$article.title}></a>
                </div>
                <div class="article-meta">
                    <{$article.writer|default:$article.author}>
                    <{$article.time}>
                </div>
                <div class="article-summary"><{$article.summary}></div>
            </div>
        <{/foreach}>
        <div class="article-section-container">
        </div>
        <{/if}>

        <!-- Recent articles -->
        <{if $articles|is_array && count($articles) > 0}>
            <div id="article" class="article-section article-article">
                <div class="article-section-title">
        <span class="subject">
            <{php}>echo art_constant("MD_ARTICLES");<{/php}>
            <{if $featured}>
                (<{php}>echo art_constant("MD_FEATURED");<{/php}>)
            <{/if}>
        </span>
                    <span class="navigation">
            <{if $featured}>
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.list.php<{$smarty.const.URL_DELIMITER}>c<{$category.id}>/f"><{$smarty.const._MORE}></a>
            <{else}>
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.list.php<{$smarty.const.URL_DELIMITER}>c<{$category.id}>"><{$smarty.const._MORE}></a>
            <{/if}>
        </span>
                </div>

                <div class="article-section-container">
                    <ol>
                        <{foreach item=article from=$articles}>
                            <li>
                                <div class="article-title">
                                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.article.php<{$smarty.const.URL_DELIMITER}><{$article.id}>"><{$article.title}></a>
                                    <{if $article.image}><img
                                        src="<{$xoops_url}>/modules/<{$xoops_dirname}>/assets/images/image.gif" width="12px"
                                        alt=""><{/if}>
                                </div>
                                <div class="article-meta">
                                    <{$article.writer|default:$article.author}>
                                    <{$article.time}>
                                </div>
                                <div class="article-summary"><{$article.summary}></div>
                            </li>
                        <{/foreach}>
                        <ol>
                </div>
            </div>
        <{/if}>


        <div id="pagenav" class="article-section pagenav">
            <{$pagenav}>
        </div>


        <{if $categories|is_array && count($categories) > 0}>
            <div id="category" class="article-section article-category">
                <div class="article-section-title">
                    <span class="subject"><{php}>echo art_constant("MD_CATEGORIES");<{/php}></span>
                    <span class="navigation">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.directory.php<{$smarty.const.URL_DELIMITER}><{$category.id}>"><{$smarty.const._MORE}></a>
        </span>
                </div>

                <div class="article-section-container">
                    <{foreach item=cat name=cat from=$categories}>
                        <span class="article-term">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$cat.id}>"><{$cat.title}></a>
            (<acronym title='<{php}>echo art_constant("MD_SUBCATEGORIES");<{/php}>'><{$cat.categories}></acronym>|<acronym
                                    title='<{php}>echo art_constant("MD_ARTICLES");<{/php}>'><{$cat.articles}></acronym>)
        </span>
                    <{/foreach}>
                </div>
            </div>
        <{/if}>

        <{if $topics|is_array && count($topics) > 0}>
            <div class="article-section article-topic">
                <div class="article-section-title">
                    <span class="subject"><{php}>echo art_constant("MD_TOPICS");<{/php}></span>
                    <span class="navigation">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.topics.php<{$smarty.const.URL_DELIMITER}><{$category.id}>"><{$smarty.const._MORE}></a>
        </span>
                </div>

                <div class="article-section-container">
                    <{include file="db:`$xoops_dirname`_inc_topic.tpl"}>
                </div>

            </div>
        <{/if}>

        <!-- Sponsors -->
        <{if $sponsors|is_array && count($sponsors) > 0 }>
            <div class="article-section article-sponsor">
                <div class="article-section-title">
                    <span class="subject"><{php}>echo art_constant("MD_SPONSORS");<{/php}></span>
                    <span class="navigation"></span>
                </div>
                <div class="article-section-container">
                    <{include file="db:`$xoops_dirname`_inc_sponsor.tpl"}>
                </div>
            </div>
        <{/if}>

        <div id="article-api" class="article-section article-api">
            API:
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/xml.php<{$smarty.const.URL_DELIMITER}>rss/c<{$category.id}>"
               target="api"><{php}>echo art_constant("MD_RSS");<{/php}></a>
            |
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/xml.php<{$smarty.const.URL_DELIMITER}>rdf/c<{$category.id}>"
               target="api"><{php}>echo art_constant("MD_RDF");<{/php}></a>
            |
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/xml.php<{$smarty.const.URL_DELIMITER}>atom/c<{$category.id}>"
               target="api"><{php}>echo art_constant("MD_ATOM");<{/php}></a>
            |
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/xml.php<{$smarty.const.URL_DELIMITER}>opml/c<{$category.id}>"
               target="api"><{php}>echo art_constant("MD_OPML");<{/php}></a>
            <{if $isadmin}>
                || <{php}>echo art_constant("MD_CPANEL");<{/php}>:
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/edit.category.php?category=<{$category.id}>"><{php}>echo art_constant("MD_CATEGORY");<{/php}></a>
                |
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.topic.php?category=<{$category.id}>"><{php}>echo art_constant("MD_TOPIC");<{/php}></a>
            <{/if}>
            <{if $xoops_isuser}>
                |
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?category=<{$category.id}>"><{php}>echo art_constant("MD_ARTICLE");<{/php}></a>
                ||
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/edit.article.php?category=<{$category.id}>"><{php}>echo art_constant("MD_ADDARTICLE");<{/php}></a>
            <{/if}>
        </div>


        <{if $xoops_notification}>
            <{include file='db:system_notification_select.tpl'}>
        <{/if}>
