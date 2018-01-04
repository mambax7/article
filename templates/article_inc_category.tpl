<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->
<{assign var="num_column" value=2}> <{* Set the column number *}>
<{assign var="ful_width" value=95}>  <{* Set the full width for multiple columns *}>
<{assign var="col_width" value=$ful_width/$num_column|@floor}>  <{* calculate column width *}>

<{assign var="mode_list" value=0}> <{* Top:Left:Right mode*}>

<div>

    <{foreachq item=category name=category from=$categories}>
    <div class="article-list-column" style="width: <{$col_width}>%; float: left; padding: 5px;">


        <{if $mode_list}>
            <div class="article-category-navigation">

            <span class="subject">
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$category.id}>"><{$category.title}></a>
            </span>
                <span class="navigation">
                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$category.id}>"
                   title="<{php}>echo art_constant(" MD_SUBCATEGORIES");<{/php}>
                    : <{$category.count_category}>; <{php}>echo art_constant("MD_ARTICLES");<{/php}>
                    : <{$category.count_article}>" ><{$smarty.const._MORE}></a>
            </span>

            </div>
            <div class="category-container">

                <div class="article-header-image">
                    <img src="<{$category.image|default:$default_image}>" alt="<{$category.title}>">
                </div>

                <div class="article-list">
                    <ul>
                        <{foreachq item=article from=$category.articles}>
                        <li>
                            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.article.php<{$smarty.const.URL_DELIMITER}>c<{$category.id}>/<{$article.id}>"
                               title="<{$article.summary|strip_tags}>"><{$article.title}></a>
                            <{if $article.image}><img
                                src="<{$xoops_url}>/modules/<{$xoops_dirname}>/assets/images/image.gif" width="12px"
                                alt=""><{/if}>
                            <{$article.time}>
                        </li>
                        <{/foreach}>
                    </ul>
                </div>

            </div>
        <{else}>
            <div class="category-container">

                <div class="article-header-image">
                    <img src="<{$category.image|default:$default_image}>" alt="<{$category.title}>">
                </div>

                <div class="article-list">
                    <div class="article-title" style="border-bottom: solid 1px #ddd; font-size: 120%;">
                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$category.id}>">
                            <{$category.title}>
                        </a>
                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$category.id}>"
                           title="<{php}>echo art_constant("
                           MD_SUBCATEGORIES");<{/php}>: <{$category.count_category}>
                        ; <{php}>echo art_constant("MD_ARTICLES");<{/php}>: <{$category.count_article}>" >
                        <img src="<{$xoops_url}>/assets/images/pointer.gif" alt="">
                        </a>
                    </div>

                    <ul>
                        <{foreachq item=article from=$category.articles}>
                        <li>
                            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.article.php<{$smarty.const.URL_DELIMITER}>c<{$category.id}>/<{$article.id}>"
                               title="<{$article.summary|strip_tags}>"><{$article.title}></a>
                            <{if $article.image}><img
                                src="<{$xoops_url}>/modules/<{$xoops_dirname}>/assets/images/image.gif" width="12px"
                                alt=""><{/if}>
                            <{$article.time}>
                        </li>
                        <{/foreach}>
                    </ul>
                </div>

            </div>
        <{/if}>

    </div>

    <{if $num_column != 0 && $smarty.foreach.category.iteration % $num_column eq 0}>
</div>
<br style="clear:both;">
<div>
    <{/if}>

    <{/foreach}>

</div>
