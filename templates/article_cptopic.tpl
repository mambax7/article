<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->

<div class="article-breadcrumbs head">
    <{if $tracks|is_array && count($tracks) > 0 }>
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.topic.php?from=<{$from}>"><{$modulename}> <{php}>echo art_constant("MD_CPTOPIC");<{/php}></a>
        </a>
        <{foreach item=track from=$tracks}>
        ::
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.topic.php?category=<{$track.id}>&amp;from=<{$from}>"><{$track.title}></a>
    <{/foreach}>
    <{else}>
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$modulename}></a>
    <{/if}>
</div>


<!-- topic header data -->
<div class="article-section topic-header">
    <h2 class="article-title">
        <{if $category}>
            <{php}>echo art_constant("MD_CPTOPIC");<{/php}>: <{$category.title}>
        <{else}>
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.topic.php"><{php}>echo art_constant("MD_CPTOPIC");<{/php}></a>
        <{/if}>
    </h2>

    <div class="article-list">
        <span class="article-button"><a
                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.topic.php?category=<{$category.id}>&amp;type=active&amp;from=<{$from}>"><{php}>echo art_constant("MD_ACTIVE");<{/php}></a></span>
        <span class="article-button"><a
                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.topic.php?category=<{$category.id}>&amp;type=expired&amp;from=<{$from}>"><{php}>echo art_constant("MD_EXPIRED");<{/php}></a></span>
        <span class="article-button"><a
                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.topic.php?category=<{$category.id}>&amp;type=all&amp;from=<{$from}>"><{$smarty.const._ALL}></a></span>
    </div>
</div>


<{if $topics|is_array && count($topics) > 0}>
    <form action="am.topic.php" method="POST">
        <{securityToken}><{*//mb*}>
        <div class="article-section article-topics">
            <div class="article-title">
                <{php}>echo art_constant("MD_TOPIC");<{/php}> (<{$type_name}>)
            </div>

            <div class="article-section-container">

                <{foreach key=id item=topic from=$topics}>
                <div class="article-list" style="padding-top: 10px;">
                    <strong><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.topic.php<{$smarty.const.URL_DELIMITER}><{$topic.id}>"><{$topic.title}></a></strong>
                </div>
                <div class="article-list" style="padding-bottom: 10px;">
                    <{php}>echo art_constant("MD_CREATION");<{/php}>: <{$topic.time}>
                    ; <{php}>echo art_constant("MD_EXPIRATION");<{/php}>: <{$topic.expire}>
                </div>
                <div class="article-list" style="padding-bottom: 10px;">
                    <span class="article-button"><a
                                href="<{$xoops_url}>/modules/<{$xoops_dirname}>/edit.topic.php?topic=<{$topic.id}>&amp;from=<{$from}>"><{$smarty.const._EDIT}></a></span>
                    <span class="article-button"><a
                                href="<{$xoops_url}>/modules/<{$xoops_dirname}>/am.topic.php?op=delete&amp;topic=<{$topic.id}>&amp;from=<{$from}>"><{$smarty.const._DELETE}></a></span>
                    <span><{php}>echo art_constant("MD_ORDER");<{/php}><input type="text" name="top_order[]"
                                                                              value="<{$topic.order}>" size="5"><input
                                type="hidden" name="top_id[]"
                                value="<{$topic.id}>"></span>
                </div>
                <{/foreach}>

                <div class="clear"></div>

                <div class="article-list">
                    <input type="hidden" name="op" value="order">
                    <input type="hidden" name="from" value="<{$from}>">
                    <input type="hidden" name="category" value="<{$category.id}>">
                    <input type="hidden" name="start" value="<{$start}>">
                    <input type="hidden" name="type" value="<{$type}>">
                    <span><input type="submit" name="submit" value="<{$smarty.const._SUBMIT}>"></span>
                    <span><input type="reset" value="<{$smarty.const._CANCEL}>"></span>
                </div>
            </div>
        </div>

    </form>
<{/if}>

<div id="pagenav" class="article-section pagenav">
    <{$pagenav}>
</div>

<{if $category}>
    <div class="article-section article-categories">
        <span class="article-button"><a
                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/edit.topic.php?category=<{$category.id}>&amp;from=<{$from}>"><{$smarty.const._ADD}></a></span>
    </div>
<{/if}>
