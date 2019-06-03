<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->

<{foreach item=topic from=$block.topics}>
<div style="padding: 5px 0;">
    <span>
        <a href="<{$xoops_url}>/modules/<{$block.dirname}>/view.topic.php<{$smarty.const.URL_DELIMITER}><{$topic.top_id}>"><{$topic.top_title}></a>
    </span>
    <span>
        (<a href="<{$xoops_url}>/modules/<{$block.dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$topic.cat_id}>"><{$topic.category}></a> <{$topic.time}>
        )
    </span>
</div>
<{/foreach}>
