<{foreach item=article from=$block.articles}>
<div style="margin:10px 0; clear: both;">
    <div>
        <span><a href="<{$xoops_url}>/modules/<{$block.dirname}>/view.article.php<{$smarty.const.URL_DELIMITER}><{$article.art_id}>/c<{$article.cat_id}>"><strong><{$article.art_title}></strong></a></span>
        <{if $article.disp}> (<{$article.disp}>)<{/if}>
    </div>
    <{if $article.image}>
        <div style="float: left; font-size: small; margin: 0px 10px 10px 0px;">
            <img src="<{$article.image.url}>" alt="<{$article.image.caption}>" width="50px;">
        </div>
    <{/if}>
    <div>
        <span><a href="<{$xoops_url}>/modules/<{$block.dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$article.cat_id}>"><{$article.category}></a></span>
        |
        <span><{$article.writer|default:$article.author}></span> @
        <span><{$article.time}></span>
    </div>
    <{if $article.summary}>
        <div style="margin-top:5px;"><{$article.summary}></div>
    <{/if}>
</div>
<{/foreach}>
