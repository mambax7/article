<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->

<div class="article-breadcrumbs">
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/"><{$modulename}></a>
    <{foreach item=track from=$tracks}>
    >
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$track.id}>"><{$track.title}></a>
    <{/foreach}>
</div>

<div id="pagetitle">
    <div class="title"><{$topic.title}></a></div>
</div>

<div id="topic">
    <div class="description"><{$topic.description}></div>
    <div class="time">
        <span><{php}>echo art_constant("MD_CREATION");<{/php}>: <{$topic.time}></span>
        <span><{php}>echo art_constant("MD_EXPIRATION");<{/php}>: <{$topic.expire}></span>
        <span><{php}>echo art_constant("MD_ARTICLES");<{/php}>: <{$topic.articles}></span>
    </div>
</div>

<{if $articles|is_array && count($articles) > 0 }>
    <div id="article">
        <div class="title"><{php}>echo art_constant("MD_ARTICLES");<{/php}></div>
        <{foreach item=article from=$articles}>
        <div class="item">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.article.php<{$smarty.const.URL_DELIMITER}>c<{$topic.cat_id}>/<{$article.id}>"><{$article.title}></a>
        </div>
        <div class="item">
<span class="author">
<a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.author.php<{$smarty.const.URL_DELIMITER}><{$article.author.uid}>"><{$article.author.name}></a>
    <{if $article.author.author}>(<{$article.author.author}>)<{/if}>
</span>
            <span class="time">
<{$article.time}>
</span>
        </div>
        <div class="clear"></div>
        <{/foreach}>
    </div>
<{/if}>

<div id="pagenav">
    <{$pagenav}>
</div>

<{if $sponsors|is_array && count($sponsors) > 0 }>
    <div id="sponsor">
        <div class="title"><{php}>echo art_constant("MD_SPONSOR");<{/php}></div>
        <{foreach item=sponsor from=$sponsors}>
        <div class="item"><a href="<{$sponsor.url}>" target="_blank"><{$sponsor.title}></a></div>
        <{/foreach}>
    </div>
<{/if}>

<{if $isadmin}>
    <div id="api">
        <{php}>echo art_constant("MD_CPANEL");<{/php}>: <a
                href="<{$xoops_url}>/modules/<{$xoops_dirname}>/edit.topic.php?topic=<{$topic.id}>"><{php}>echo art_constant("MD_TOPIC");<{/php}></a>
        |
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?topic=<{$topic.id}>"><{php}>echo art_constant("MD_CPARTICLE");<{/php}></a>
    </div>
<{/if}>
