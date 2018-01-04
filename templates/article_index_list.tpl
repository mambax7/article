<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->
<{* $xoTheme->addStylesheet("modules/`$xoops_dirname`/assets/css/style_list.css") *}>

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
            | <{php}>echo art_constant("MD_VIEWS");<{/php}>: <{$article.counter}>
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
<{if count($features) gt 0}>
    <div class="article-section article-feature">
        <div class="article-section-title">
            <span class="subject"><{php}>echo art_constant("MD_FEATURED");<{/php}></span>
            <span class="navigation"><a
                        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.list.php<{$smarty.const.URL_DELIMITER}>f"><{$smarty.const._MORE}></a></span>
        </div>

        <div class="article-section-container">
            <{foreachq item=article from=$features}>
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

<!-- Recent articles -->
<{if count($articles) gt 0}>
    <div id="list-article" class="article-section list-article">
        <div class="article-section-title">
        <span class="subject">
            <{php}>echo art_constant("MD_ARTICLES");<{/php}>
        </span>
            <span class="navigation">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.list.php"><{$smarty.const._MORE}></a>
        </span>
        </div>

        <div class="article-section-container">
            <ol>
                <{foreachq item=article from=$articles}>
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
                    <{if count($article.categories)>0}>
                        <div class="article-list">
                            <span class="article-subject"><{php}>echo art_constant("MD_CATEGORIES");<{/php}>:</span>
                            <{foreachq item=category key=catid from=$article.categories}>
                            <span class="article-term">
                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$category.id}>/"><{$category.title}></a>
                    </span>
                            <{/foreach}>
                        </div>
                    <{/if}>
                    <{if $article.summary}>
                        <div class="article-summary"><{$article.summary}></div>
                    <{/if}>
                </li>
                <{/foreach}>
                <ol>
        </div>
    </div>
<{/if}>


<{if count($categories) gt 0}>
    <div id="category" class="article-section article-category">
        <div class="article-section-title">
            <span class="subject"><{php}>echo art_constant("MD_CATEGORIES");<{/php}></span>
            <span class="navigation">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.directory.php"><{$smarty.const._MORE}></a>
        </span>
        </div>

        <div class="article-section-container">
            <{foreachq item=cat name=cat from=$categories}>
            <span class="article-term">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$cat.id}>"><{$cat.title}></a>
            (<acronym title='<{php}>echo art_constant("MD_SUBCATEGORIES");<{/php}>'><{$cat.categories}></acronym>|<acronym
                        title='<{php}>echo art_constant("MD_ARTICLES");<{/php}>'><{$cat.articles}></acronym>)
        </span>
            <{/foreach}>
        </div>
    </div>
<{/if}>

<{if count($topics) gt 0}>
    <div class="article-section article-topic">
        <div class="article-section-title">
            <span class="subject"><{php}>echo art_constant("MD_TOPICS");<{/php}></span>
            <span class="navigation">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.topics.php"><{$smarty.const._MORE}></a>
        </span>
        </div>

        <div class="article-section-container">
            <{foreachq item=topic name=topic from=$topics}>
            <span class="article-term">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.topic.php<{$smarty.const.URL_DELIMITER}><{$topic.id}>"><{$topic.title}></a>
        </span>
            <{/foreach}>
        </div>

    </div>
<{/if}>

<!-- Sponsors -->
<{if count($sponsors) gt 0}>
    <div class="article-section article-sponsor">
        <div class="article-section-title">
            <span class="subject"><{php}>echo art_constant("MD_SPONSORS");<{/php}></span>
            <span class="navigation"></span>
        </div>
        <div class="article-section-container">
            <{includeq file="db:`$xoops_dirname`_inc_sponsor.tpl"}>
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
    <{includeq file='db:system_notification_select.tpl'}>
<{/if}>
