<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->

<!-- page breadcrumbs -->
<div class="article-breadcrumbs head">
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/"><{$modulename}></a>
    <{foreach item=track from=$tracks}>
    ::
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$track.id}>"><{$track.title}></a>
    <{/foreach}>
</div>


<!-- category meta data -->
<{if $category}>
    <div class="article-section category-header">

        <{if $category.image}>
            <div class="article-header-image"><img src="<{$category.image}>" alt="<{$category.title}>"></div>
        <{/if}>

        <h2 class="article-title"><{$category.title}></h2>

        <div style="padding: 10px 0;"><{$category.description}></div>

        <div class="article-meta">

            <{php}>echo art_constant("MD_ARTICLES");<{/php}>: <{$category.articles}>

            <{if $category.categories}>
                | <{php}>echo art_constant("MD_CATEGORIES");<{/php}>: <{$category.categories}>
            <{/if}>

            <{if $category.topics}>
                | <{php}>echo art_constant("MD_TOPICS");<{/php}>: <{$category.topics}>
            <{/if}>

        </div>

        <div class="clear"></div>
    </div>
<{/if}>

<!-- Categories -->
<{if $categories}>
    <div class="article-section article-category">

        <div class="article-section-container">
            <div>

                <{assign var="num_column" value=3}> <{* Set the column number *}>
                <{assign var="ful_width" value=95}>  <{* Set the full width for multiple columns *}>
                <{assign var="col_width" value=$ful_width/$num_column}>  <{* calculate column width *}>

                <{foreach item=cat1 name=cat1 from=$categories.child}>
                <div class="article-list-column" style="width: <{$col_width}>%; float: left; margin: 5px;">
                    <div class="article-title">
                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.directory.php<{$smarty.const.URL_DELIMITER}><{$cat1.cat_id}>"><{$cat1.cat_title}></a>
                        <{if $cat1.count}> (
                            <acronym title='<{php}>echo art_constant("MD_ARTICLES");<{/php}>'><{$cat1.count}></acronym>
                            )<{/if}>
                        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$cat1.cat_id}>"><img
                                    src="<{$xoops_url}>/assets/images/pointer.gif" alt=""></a>
                    </div>

                    <div class="category-container article-title">
                        <ul>
                            <{foreach item=cat2 from=$cat1.child}>
                            <li>
                                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.directory.php<{$smarty.const.URL_DELIMITER}><{$cat2.cat_id}>"><{$cat2.cat_title}></a>
                                <{if $cat2.count}> (
                                    <acronym
                                            title='<{php}>echo art_constant("MD_ARTICLES");<{/php}>'><{$cat2.count}></acronym>
                                    )<{/if}>
                                <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$cat2.cat_id}>"><img
                                            src="<{$xoops_url}>/assets/images/pointer.gif"
                                            alt=""></a>
                            </li>
                            <{/foreach}>
                        </ul>
                    </div>
                </div>

                <{if $num_column != 0 && $smarty.foreach.cat1.iteration % $num_column eq 0}>
            </div>
            <div class="clear"></div>
            <br style="clear: both;">
            <div>
                <{/if}>
                <{/foreach}>
            </div>
        </div>

    </div>
<{/if}>
