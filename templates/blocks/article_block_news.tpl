<style type="text/css">
    /*
     * From http://www.positioniseverything.net/easyclearing.html
     */
    .clear:after {
        content: ".";
        display: block;
        height: 0;
        clear: both;
        visibility: hidden;
    }

    .clear {
        display: inline-table;
    }

    /* Hides from IE-mac \*/
    * html .clear {
        height: 1%;
    }

    .clear {
        display: block;
    }

    /* End hide from IE-mac */

    acronym {
        cursor: help;
        border-bottom: 1px dotted #000;
    }
</style>

<h3><a href="<{$block.spotlight.url}>" title="<{$block.spotlight.title}>"><{$block.spotlight.title}></a></h3>

<div>
    <{if $block.spotlight.image.url}>
        <div style="float: left; padding: 10px;">
            <img src="<{$block.spotlight.image.url}>" alt="<{$block.image.spotlight.caption}>">
        </div>
    <{/if}>
    <div style="margin: 5px 0; border-bottom: solid 1px #ddd;">
        <div>
            <{$block.spotlight.writer|default:$block.spotlight.author}>
            <{$block.spotlight.time}>
            <{if $block.spotlight.views}>
                <{$block.lang.views}>: <{$block.spotlight.views}>
            <{/if}>
            <{if $block.spotlight.comments}>
                <{$block.lang.comments}>: <{$block.spotlight.comments}>
            <{/if}>
        </div>
        <{if $block.spotlight.sp_note}>
            <div style="padding: 5px 0; font-weight: bold;"><{$block.spotlight.sp_note}></div>
        <{/if}>
        <{if $block.spotlight.summary}>
            <div style="padding: 5px 0;"><{$block.spotlight.summary}></div>
        <{/if}>
        <div class="clear"></div>
        <br style="clear:both;">
    </div>

    <{if $block.mode == 0}>
        <div style="margin: 5px 0;">
            <!--
            <div><{$block.lang.categories}></div>
            -->
            <ul style="margin: 5px; padding-left: 5px;">
                <{foreach item=article from=$block.articles}>
                <li style="list-tyle-type: disc; list-style-position: outside; margin-left: 5px;">
                    <a href="<{$article.url}>" title="<{$article.title_full}>"><{$article.title}></a>
                    <{$article.time}> <{if $article.comments}> (
                        <acronym title="<{$block.lang.comments}>"><{$article.comments}></acronym>
                        )<{/if}>
                </li>
                <{/foreach}>
            </ul>
        </div>
        <div style="margin:5px 0;">
            <{$block.lang.categories}>:
            <{foreach item=category from=$block.categories}>
            <span>[<a href="<{$category.url}>" title="<{$category.title}>"><{$category.title}></a>]</span>
            <{/foreach}>
        </div>
    <{else}>
        <div style="margin: 5px 0;">
            <{assign var="num_column" value=$block.mode}>
            <{assign var="ful_width" value=95}>
            <{assign var="col_width" value=$ful_width/$num_column|@floor}>

            <div>

                <{foreach item=category name=category from=$block.categories}>
                <div style="float: left; width: <{$col_width}>%; padding: 0 5px; margin-top: 5px;">
                    <div style="font-weight: bold; border-bottom: solid 1px #ddd;">
                        <img src="<{$xoops_url}>/assets/images/pointer.gif" alt=""> <a
                                href="<{$category.url}>"><{$category.title}></a>
                    </div>
                    <div>
                        <ul style="margin: 5px; padding-left: 5px; list-style: disc outside;">
                            <{foreach item=article from=$category.articles}>
                            <li style="list-style: disc outside;">
                                <a href="<{$article.url}>" title="<{$article.title_full}>"><{$article.title}></a>
                                <{$article.time}> <{if $article.comments}> (
                                    <acronym title="<{$block.lang.comments}>"><{$article.comments}></acronym>
                                    )<{/if}>
                            </li>
                            <{/foreach}>
                        </ul>
                    </div>
                </div>

                <{if $num_column != 0 && $smarty.foreach.category.iteration % $num_column eq 0}>
            </div>
            <div class="clear"></div>
            <br style="clear:both;">
            <div>
                <{/if}>

                <{/foreach}>
            </div>
        </div>
    <{/if}>
    <div class="clear"></div>
</div>

<div class="clear"></div>
<br style="clear:both;">
