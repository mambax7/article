<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->

<div class="article-breadcrumbs head">
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$modulename}></a>
</div>

<{if $categories|is_array && count($categories) > 0 }>
    <form action="am.category.php" method="POST">
        <{securityToken}><{*//mb*}>
        <div class="article-section article-categories">
            <div class="article-title">
                <{php}>echo art_constant("MD_CATEGORY")<{/php}>
            </div>

            <div class="article-section-container">

                <{foreach key=id item=category from=$categories}>
                <div class="article-list" style="padding-top: 10px;">
                    <span><{$category.prefix}></span><span><strong><a
                                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php?category=<{$id}>"
                                    title="<{$category.cat_title}>"><{$category.cat_title}></a></strong></span>
                </div>
                <div class="article-list" style="padding-bottom: 10px;">
                    <span><{$category.prefix}></span>
                    <span class="article-button"><a
                                href="<{$xoops_url}>/modules/<{$xoops_dirname}>/edit.category.php?category=<{$id}>&amp;from=<{$from}>"
                                target="_blank"><{$smarty.const._EDIT}></a></span>
                    <span class="article-button"><a
                                href="<{$xoops_url}>/modules/<{$xoops_dirname}>/am.category.php?op=delete&amp;category=<{$id}>&amp;from=<{$from}>"
                                target="_blank"><{$smarty.const._DELETE}></a></span>
                    <span class="article-button"><a
                                href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?category=<{$id}>&amp;from=<{$from}>"
                                target="_blank"><{php}>echo art_constant("MD_CPARTICLE")<{/php}></a></span>
                    <span class="article-button"><a
                                href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.topic.php?category=<{$id}>&amp;from=<{$from}>"
                                target="_blank"><{php}>echo art_constant("MD_CPTOPIC")<{/php}></a></span>
                    <span><{php}>echo art_constant("MD_ORDER")<{/php}>: <input type="text" name="cat_order[]"
                                                                               value="<{$category.cat_order}>"
                                                                               size="5"><input type="hidden"
                                                                                                name="cat_id[]"
                                                                                                value="<{$id}>"></span>
                </div>
                <{/foreach}>

                <div class="article-list">
                    <input type="hidden" name="op" value="order">
                    <input type="hidden" name="from" value="<{$from}>">
                    <span><input type="submit" name="submit" value="<{$smarty.const._SUBMIT}>"></span>
                    <span><input type="reset" value="<{$smarty.const._CANCEL}>"></span>
                </div>

            </div>
        </div>
        <div class="clear"></div>


    </form>
<{/if}>
