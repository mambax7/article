<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->

<h2 class="article-title">
    <img src="<{$xoops_url}>/assets/images/pointer.gif" alt=""> <a
            href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.article.php<{$smarty.const.URL_DELIMITER}><{$article.id}>"><{$article.title}></a>
</h2>

<{if $article.image}>
    <div class="article-header-image"><img src="<{$article.image.url}>"
                                           alt="<{$article.image.caption}>"><br><{$article.image.caption}></div>
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

<div class="article-meta">
    <{php}>echo art_constant("MD_CATEGORIES");<{/php}>:
    <{foreachq item=category key=catid from=$article.categories}>
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$category.id}>/"><{$category.title}></a>
    <{/foreach}>
</div>

<div class="article-meta">
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.article.php<{$smarty.const.URL_DELIMITER}><{$article.id}>"
       title="<{php}>echo art_constant(" MD_VIEWALL");<{/php}>"><{php}>echo
    art_constant("MD_VIEWALL");<{/php}></a>
    |
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.article.php<{$smarty.const.URL_DELIMITER}><{$article.id}>"
       title="<{php}>echo art_constant(" MD_COMMENTS");<{/php}>"><{php}>echo
    art_constant("MD_COMMENTS");<{/php}> (<{$article.comments|default:0}>)</a>
    |
    <input name="a<{$article.id}>" id="a<{$article.id}>"
           value="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.article.php<{$smarty.const.URL_DELIMITER}><{$article.id}>"
           type="hidden">
    <span class="copytext" onclick="copytext('a<{$article.id}>')" title="URI - <{php}>echo art_constant(" MD_CLICKTOCOPY");<{/php}>
    " ><{php}>echo art_constant("MD_URL");<{/php}></span>
    |
    <input name="t<{$article.id}>" id="t<{$article.id}>"
           value="<{$xoops_url}>/modules/<{$xoops_dirname}>/trackback.php<{$smarty.const.URL_DELIMITER}><{$article.id}>"
           type="hidden">
    <span class="copytext" onclick="copytext('t<{$article.id}>')" title="Trackback - <{php}>echo art_constant("
          MD_CLICKTOCOPY");<{/php}>" ><{php}>echo art_constant("MD_TRACKBACK");<{/php}>
    (<{$article.trackbacks|default:0}>)</span>
</div>
