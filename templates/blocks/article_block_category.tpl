<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->

<{foreach item=cat from=$block.categories}>
<div>
    <a href="<{$xoops_url}>/modules/<{$block.dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$cat.cat_id}>"><{$cat.cat_title}></a>
    (<{$cat.articles|default:0}>)
</div>
<{/foreach}>
