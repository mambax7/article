<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->
<{* $xoTheme->addStylesheet("modules/`$xoops_dirname`/assets/css/style.css") *}>

<!-- Breadcrumbs and header -->
<{if $header}>
    <div class="article-breadcrumbs head"><{$header}></div>
<{/if}>

<!-- Spotlight -->
<{if $spotlight}>
    <{assign var="article" value=$spotlight}>
    <div class="article-section article-spotlight">

        <h2 class="article-title">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.article.php<{$smarty.const.URL_DELIMITER}><{$article.id}>"><{$article.title}></a>
        </h2>

        <{if $article.image}>
            <div class="article-header-image"><img src="<{$article.image.url}>"
                                                   alt="<{$article.image.caption}>"><br><{$article.image.caption}>
            </div>
        <{/if}>

        <div class="article-meta">
            <{$article.writer|default:$article.author}>
            | <{$article.time}>
            | <{php}>echo art_constant("MD_VIEWS");<{/php}>: <{$article.counter|default:1}>
        </div>

        <{if $article.note}>
            <div style="padding: 10px;">
                <strong><{php}>echo art_constant("MD_EDNOTE");<{/php}>:</strong>
                <{$article.note}>
            </div>
        <{/if}>

        <div style="padding-top: 10px;"><{$article.summary}></div>
    </div>
    <br style="clear:both;">
<{/if}>

<{assign var="default_image" value="`$xoops_url`/modules/`$xoops_dirname`/assets/images/xoops.png"}>

<!-- Featured articles -->
<{if $features|is_array && count($features) > 0 }>
    <div class="article-section article-feature">
        <div class="article-section-title">
            <span class="subject"><{php}>echo art_constant("MD_FEATURED");<{/php}></span>
            <span class="navigation"><a
                        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.list.php<{$smarty.const.URL_DELIMITER}>f"><{$smarty.const._MORE}></a></span>
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
        </div>
    </div>
<{/if}>

<!-- Categories -->
<div class="article-section article-category">
    <div class="article-section-title">
        <span class="subject">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.directory.php"><{php}>echo art_constant("MD_CATEGORIES");<{/php}></a>
        </span>
        <span class="navigation">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.list.php"><{$smarty.const._MORE}></a>
        </span>
    </div>

    <div class="article-section-container" style="width: 100%;">
        <{include file="db:`$xoops_dirname`_inc_category.tpl"}>
    </div>

</div>
<br style="clear:both;">

<!-- Topic -->
<{if $topics|is_array && count($topics) > 0}>
    <div class="article-section article-topic">
        <div class="article-section-title">
            <span class="subject"><{php}>echo art_constant("MD_TOPICS");<{/php}></span>
            <span class="navigation">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.topics.php"><{$smarty.const._MORE}></a>
        </span>
        </div>

        <div class="article-section-container">
            <{include file="db:`$xoops_dirname`_inc_topic.tpl"}>
        </div>

    </div>
    <br style="clear:both;">
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
        <br style="clear:both;">
    </div>
<{/if}>

<!-- API -->
<div id="article-api" class="article-section article-api">
    API: <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/xml.php<{$smarty.const.URL_DELIMITER}>rss"
            target="api"><{php}>echo art_constant("MD_RSS");<{/php}></a>
    | <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/xml.php<{$smarty.const.URL_DELIMITER}>rdf"
         target="api"><{php}>echo art_constant("MD_RDF");<{/php}></a>
    | <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/xml.php<{$smarty.const.URL_DELIMITER}>atom"
         target="api"><{php}>echo art_constant("MD_ATOM");<{/php}></a>
    <{if $xoops_isadmin}>
        || <a
        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/admin/" title="<{php}>echo art_constant("MD_CPANEL");<{/php}>"><{php}>echo art_constant("MD_CPANEL");<{/php}></a>
        | <a
        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.category.php" title="<{php}>echo art_constant("MD_CPCATEGORY");<{/php}>"><{php}>echo art_constant("MD_CATEGORY");<{/php}></a>
        | <a
        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.topic.php" title="<{php}>echo art_constant("MD_CPTOPIC");<{/php}>"><{php}>echo art_constant("MD_TOPIC");<{/php}></a>
        | <a
        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php" title="<{php}>echo art_constant("MD_CPARTICLE");<{/php}>"><{php}>echo art_constant("MD_ARTICLE");<{/php}></a>
    <{/if}>
    <{if $xoops_isuser}>
        | <a
        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.author.php" title="<{php}>echo art_constant("MD_MYARTICLES");<{/php}>"><{php}>echo art_constant("MD_MYARTICLES");<{/php}></a>
        ||
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/edit.article.php"><{php}>echo art_constant("MD_ADDARTICLE");<{/php}></a>
    <{/if}>
    <div class="clear"></div>
</div>

<{if $xoops_notification}>
    <{include file='db:system_notification_select.tpl'}>
<{/if}>
