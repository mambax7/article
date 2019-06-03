<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->

<!-- page breadcrumbs -->
<div class="article-breadcrumbs head">
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/"><{$modulename}></a>
    <{foreach item=track from=$tracks}>
    ::
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$track.id}>"><{$track.title}></a>
    <{/foreach}>
    <{foreach item=track from=$tracks_extra}>
    :: <a href="<{$track.link}>"><{$track.title}></a>
    <{/foreach}>
</div>

<!-- list header data -->
<div class="article-section list-header">
    <h2 class="article-title">
        <{foreach item=track from=$page_meta}>
        <a href="<{$track.link}>"><{$track.title}></a>
        <{/foreach}>
    </h2>

    <{if $author}>
        <div class="article-section-container" style="padding: 20px; margin-bottom: 20px;">

            <{if $author.avatar}>
                <div class="article-header-image"><img src="<{$author.avatar}>" alt="<{$author.uname}>"></div>
            <{/if}>

            <div class="article-list">
                <{foreach item=profile from=$author.profiles}>
                <div>
                    <span class="article-label"><{$profile.title}>:</span><span
                            class="article-content"><{$profile.content}></span>
                </div>
                <{/foreach}>
                <{if $author.mods|is_array && count($author.mods) > 0 }>
                    <div>
                        <span class="article-label"><{php}>echo art_constant("MD_MODERATOR");<{/php}>:</span>
                        <{foreach item=mod from=$author.mods}>
                        <span class="article-content"><a href="<{$mod.url}>"><{$mod.title}></a></span>
                        <{/foreach}>
                    </div>
                <{/if}>
                <{if $author.stats}>
                    <div>
                        <span class="article-subject"><{php}>echo art_constant("MD_ARTICLES");<{/php}>:</span><span
                                class="article-term"><{$author.stats.articles}></span>
                        <span class="article-subject"><{php}>echo art_constant("MD_FEATURED");<{/php}>:</span><span
                                class="article-term"><{$author.stats.featured}></span>
                        <span class="article-subject"><{php}>echo art_constant("MD_TOPICS");<{/php}>:</span><span
                                class="article-term"><{$author.stats.topics}></span>
                    </div>
                <{/if}>
            </div>

        </div>
        <br style="clear:both;">
    <{/if}>

    <div class="article-list">
        <div class="article-title">
            <span class="article-label">
                <{php}>echo art_constant("MD_TYPES");<{/php}>:
            </span>
            <{foreach item=item from=$options.type}>
            <span class="article-content"><{$item}></span>
            <{/foreach}>
        </div>
        <div class="article-title">
            <span class="article-label">
                <{php}>echo art_constant("MD_SORTBY");<{/php}>:
            </span>
            <{foreach item=item from=$options.sort}>
            <span class="article-content"><{$item}></span>
            <{/foreach}>
            |
            <{foreach item=item from=$options.order}>
            <span class="article-content"><{$item}></span>
            <{/foreach}>
        </div>
    </div>
</div>


<!-- Recent articles -->
<{if $articles|is_array && count($articles) > 0}>
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
            <ol start=<{math equation="( pg / 10 ) * 10 + 1 " pg=$smarty.request.start|default:0}>>
                <{foreach item=article from=$articles}>
                <li>
                    <div class="article-title">
                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.article.php<{$smarty.const.URL_DELIMITER}><{$article.id}>"><{$article.title}></a>
                        <{if $article.image}><img
                            src="<{$xoops_url}>/modules/<{$xoops_dirname}>/assets/images/image.gif" width="12px"
                            alt=""><{/if}>
                    </div>
                    <!--
            <{if $article.image}>
                <div class="article-header-image">
                    <img src="<{$article.image.url}>" alt="<{$article.image.caption}>">
                </div>
            <{/if}>
            -->
                    <div class="article-meta">
                        <{$article.writer|default:$article.author}>
                        <{$article.time}>
                        | <{php}>echo art_constant("MD_VIEWS");<{/php}>: <{$article.counter|default:1}>
                        <{if $article.comments}>
                            | <{php}>echo art_constant("MD_COMMENTS");<{/php}>: <{$article.comments}>
                        <{/if}>
                        <{if $article.trackbacks}>
                            | <{php}>echo art_constant("MD_TRACKBACKS");<{/php}>: <{$article.trackbacks}>
                        <{/if}>
                        <{if $article.rating}>
                            | <{php}>echo art_constant("MD_RATE");<{/php}>: <{$article.rating}>
                        <{/if}>
                    </div>
                    <{if $article.categories|is_array && count($article.categories) > 0 }>
                        <div class="article-list">
                            <span><{php}>echo art_constant("MD_CATEGORIES");<{/php}>:</span>
                            <{foreach item=category key=catid from=$article.categories}>
                            <span>
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

<div id="pagenav" class="article-section pagenav">
    <{$pagenav}>
</div>
