<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->

<div class="article-breadcrumbs head">
    <{if $tracks|is_array && count($tracks) > 0 }>
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?from=<{$from}>"><{$modulename}> <{php}>echo art_constant("MD_CPARTICLE");<{/php}></a>
        </a>
        <{foreach item=track from=$tracks}>
        ::
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?category=<{$track.id}>&amp;from=<{$from}>"><{$track.title}></a>
    <{/foreach}>
    <{else}>
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$modulename}></a>
    <{/if}>
    :: <{php}>echo art_constant("MD_CPARTICLE");<{/php}>
</div>

<!-- article header data -->
<div class="article-section article-header">
    <{if $topic}>
        <h2 class="article-title">
            <span><strong><a
                            href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.topic.php<{$smarty.const.URL_DELIMITER}><{$topic.id}>"><{$topic.title}></a></strong></span>
            <span>(<a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/edit.topic.php?topic=<{$topic.id}>&amp;from=<{$from}>"><{$smarty.const._EDIT}></a>)</span>
        </h2>
        <div class="article-list">
            <{php}>echo art_constant("MD_DESCRIPTION");<{/php}>: <{$topic.desctiption}><br>
            <{php}>echo art_constant("MD_ARTICLES");<{/php}>: <{$topic.articles}>
        </div>
    <{elseif $category}>
        <h2 class="article-title">
            <span><strong><a
                            href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$category.id}>"><{$category.title}></a></strong></span>
            <span>(<a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/edit.category.php?category=<{$category.id}>"><{$smarty.const._EDIT}></a>)</span>
        </h2>
        <div class="article-list">
            <{php}>echo art_constant("MD_DESCRIPTION");<{/php}>: <{$category.desctiption}><br>
            <{php}>echo art_constant("MD_ARTICLES");<{/php}> (<{$type_name}>): <{$category.articles}>
        </div>
    <{/if}>

    <div class="article-list">
        <{if $category}>
        <span class="article-button"><a
                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?category=<{$category.id}>&amp;topic=<{$topic.id}>&amp;type=registered&amp;from=<{$from}>"><{php}>echo art_constant("MD_REGISTERED");<{/php}></a></span>
        <span class="article-button"><a
                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?category=<{$category.id}>&amp;topic=<{$topic.id}>&amp;type=published&amp;from=<{$from}>"><{php}>echo art_constant("MD_PUBLISHED");<{/php}></a></span>
        <span class="article-button"><a
                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?category=<{$category.id}>&amp;topic=<{$topic.id}>&amp;type=featured&amp;from=<{$from}>"><{php}>echo art_constant("MD_FEATURED");<{/php}></a></span>
        <span class="article-button"><a
                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?category=<{$category.id}>&amp;topic=<{$topic.id}>&amp;type=all&amp;from=<{$from}>"><{$smarty.const._ALL}></a>
            <{elseif $topic==NULL}>
            <span class="article-button"><a
                        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?type=submitted&amp;from=<{$from}>"><{php}>echo art_constant("MD_SUBMITTED");<{/php}></a></span>
            <span class="article-button"><a
                        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?type=registered&amp;from=<{$from}>"><{php}>echo art_constant("MD_REGISTERED");<{/php}></a></span>
            <span class="article-button"><a
                        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?type=published&amp;from=<{$from}>"><{php}>echo art_constant("MD_PUBLISHED");<{/php}></a></span>
            <span class="article-button"><a
                        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?type=featured&amp;from=<{$from}>"><{php}>echo art_constant("MD_FEATURED");<{/php}></a></span>
            <span class="article-button"><a
                        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?type=all&amp;from=<{$from}>"><{$smarty.const._ALL}></a>
                <{/if}>
    </div>
</div>

<{if $articles|is_array && count($articles) > 0 }>
    <div>
        <form name="form_article_cpanel" action="am.article.php" method="POST">
            <{securityToken}><{*//mb*}>
            <div class="article-section article-article">
                <div class="article-title">
                    <{php}>echo art_constant("MD_ARTICLE");<{/php}> (<{$type_name}>)
                </div>

                <div class="article-section-container">

                    <ol>
                        <{foreach item=article from=$articles}>
                        <li style="padding-top: 10px;">
                            <div class="article-list">
                                <strong><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.article.php<{$smarty.const.URL_DELIMITER}>c<{$article.category.id}>/<{$article.id}>"
                                           title=""
                                           target="<{$article.id}>"><{$article.title}></a></strong>
                                <br>
                                <{if $article.category.title}>
                                    <{php}>echo art_constant("MD_CATEGORY");<{/php}>: <a
                                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$article.category.id}>"
                                    target="<{$article.id}>"><{$article.category.title}></a><{if $article.cat_id eq $article.category.id}>
                                    <strong>(*)</strong>
                                <{/if}>;
                                <{/if}>
                                <{php}>echo art_constant("MD_AUTHOR");<{/php}>: <{$article.author}>
                            </div>
                            <div class="article-list">
                                <{if $topic}>
                                    <{php}>echo art_constant("MD_PUBLISH");<{/php}>: <{$article.time_topic}>
                                <{else}>
                                    <{php}>echo art_constant("MD_SUBMISSION");<{/php}>: <{$article.submit}>;
                                    <{php}>echo art_constant("MD_REGISTER");<{/php}>: <{$article.register_category}>
                                    <{if $article.publish_category}>
                                        ; <{php}>echo art_constant("MD_PUBLISH");<{/php}>: <{$article.publish_category}>
                                    <{/if}>
                                    <{if $article.feature_category}>
                                        ; <{php}>echo art_constant("MD_FEATURE");<{/php}>: <{$article.feature_category}>
                                    <{/if}>
                                <{/if}>
                            </div>

                            <div class="article-summary">
                                <{$article.summary}>
                            </div>

                            <div class="article-list"
                                 style="text-align: right; padding-top: 5px; padding-bottom: 10px;">
                                <span class="article-button"><a
                                            href="<{$xoops_url}>/modules/<{$xoops_dirname}>/am.article.php?op=terminate&amp;article=<{$article.id}>&amp;category=<{$article.category.id}>&amp;topic=<{$topic.id}>&amp;from=<{$from}>"><{php}>echo art_constant("MD_TERMINATE");<{/php}></a></span>
                                <{if $topic.id==0}>
                                    <{if $article.admin}>
                                        <span class="article-button"><a
                                                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/edit.article.php?article=<{$article.id}>"><{$smarty.const._EDIT}></a></span>
                                    <{/if}>
                                    <{if $article.publish_category eq "" OR $article.publish eq ""}>
                                        <span class="article-button"><a
                                                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/am.article.php?op=approve&amp;article=<{$article.id}>&amp;category=<{$article.category.id}>&amp;from=<{$from}>"><{php}>echo art_constant("MD_APPROVE");<{/php}></a></span>
                                    <{/if}>
                                    <{if $article.feature_category}>
                                        <span class="article-button"><a
                                                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/am.article.php?op=unfeature&amp;article=<{$article.id}>&amp;category=<{$article.category.id}>&amp;from=<{$from}>"><{php}>echo art_constant("MD_UNFEATUREIT");<{/php}></a></span>
                                    <{elseif $article.publish_category}>
                                        <span class="article-button"><a
                                                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/am.article.php?op=feature&amp;article=<{$article.id}>&amp;category=<{$article.category.id}>&amp;from=<{$from}>"><{php}>echo art_constant("MD_FEATUREIT");<{/php}></a></span>
                                    <{/if}>
                                    <{if $type eq "featured"}>
                                        <span class="article-button"><a
                                                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/am.article.php?op=update_time&type=featured&amp;article=<{$article.id}>&amp;category=<{$article.category.id}>&amp;from=<{$from}>"><{php}>echo art_constant("MD_UPDATE_TIME");<{/php}></a></span>
                                    <{elseif $type eq "published"}>
                                        <span class="article-button"><a
                                                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/am.article.php?op=update_time&amp;article=<{$article.id}>&amp;category=<{$article.category.id}>&amp;from=<{$from}>"><{php}>echo art_constant("MD_UPDATE_TIME");<{/php}></a></span>
                                    <{/if}>
                                    <{if $article.admin AND $article.publish_category}>
                                        <span class="article-button"><a
                                                    href="<{$xoops_url}>/modules/<{$xoops_dirname}>/am.article.php?op=rate&amp;article=<{$article.id}>&amp;from=<{$from}>"><{php}>echo art_constant("MD_RESETRATE");<{/php}></a></span>
                                        <!-- reset rating data -->
                                    <{/if}>
                                <{/if}>
                                <span class="article-button"><{$smarty.const._SELECT}><input type="checkbox"
                                                                                             name="art_id[<{$article.id}>]"
                                                                                             id="art_id[]"
                                                                                             value="1"></span>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <{/foreach}>
                    </ol>

                </div>
            </div>

            <div class="article-section article-actions" style="float: right; text-align: right; padding-top: 10px;">
                <div class="article-list">
                    <{$smarty.const._ALL}>: <input type="checkbox" name="art_check" id="art_check" value="1"
                                                   onclick="xoopsCheckAll('form_article_cpanel', 'art_check', 'art_id[]');">
                    <{php}>echo art_constant("MD_ACTIONS");<{/php}>:
                    <select name="op"
                            <{if $category.id gt 0 AND count($topics)>0}>
                                onChange="if(this.options[this.selectedIndex].value=='registertopic'){setVisible('div_topic');}else{setHidden('div_topic');}"
                            <{/if}>
                    >
                        <option value=""><{$smarty.const._SELECT}></option>

                        <{if $category.id gt 0}>
                            <{if $type eq "submitted" OR $type eq "registered"}>
                                <option value="approve"><{php}>echo art_constant("MD_APPROVE");<{/php}></option>
                            <{/if}>
                            <{if $type eq "published"}>
                                <option value="feature"><{php}>echo art_constant("MD_FEATUREIT");<{/php}></option>
                                <option value="update_time"><{php}>echo art_constant("MD_UPDATE_TIME");<{/php}></option>
                            <{/if}>
                            <{if $type eq "featured"}>
                                <option value="unfeature"><{php}>echo art_constant("MD_UNFEATUREIT");<{/php}></option>
                                <option value="update_time"><{php}>echo art_constant("MD_UPDATE_TIME");<{/php}></option>
                            <{/if}>
                            <{if $topics|is_array && count($topics) > 0}>
                                <option value="registertopic"><{php}>echo art_constant("MD_REGISTERTOPIC");<{/php}></option>
                            <{/if}>
                        <{/if}>

                        <{if $category.id gt 0 OR $topic.id gt 0}>
                            <option value="terminate"><{php}>echo art_constant("MD_TERMINATE");<{/php}></option>
                        <{/if}>

                        <{if $topic eq NULL}>
                            <option value="rate"><{php}>echo art_constant("MD_RESETRATE");<{/php}></option>
                        <{/if}>

                    </select>

                    <{if $category.id gt 0 AND count($topics)>0}>
                        <div id="div_topic" style="visibility:hidden; display:inline;">
                            <select name="top_id">
                                <{foreach item=top from=$topics}>
                                <option value="<{$top.id}>"><{$top.title}></option>
                                <{/foreach}>
                            </select>
                        </div>
                    <{/if}>

                </div>
            </div>


            <div class="article-list" style="float: right; text-align: right; padding-top: 10px;">
                <input type="hidden" name="category" value="<{$category.id}>">
                <input type="hidden" name="topic" value="<{$topic.id}>">
                <input type="hidden" name="start" value="<{$start}>">
                <input type="hidden" name="type" value="<{$type}>">
                <input type="hidden" name="from" value="<{$from}>">
                <span><input type="submit" name="submit" value="<{$smarty.const._SUBMIT}>"></span>
                <span><input type="reset" value="<{$smarty.const._CANCEL}>"></span>
            </div>

        </form>
    </div>
<{/if}>

<div id="pagenav" class="article-section pagenav">
    <{$pagenav}>
</div>

<{if $categories}>
    <div class="article-section article-categories">
        <div class="article-title">
            <{php}>echo art_constant("MD_CATEGORY");<{/php}>
        </div>

        <div class="article-list">
            <{foreach item=cat from=$categories}>
            <span class="article-button"><a
                        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php?category=<{$cat.id}>&amp;from=<{$from}>"><{$cat.title}></a></span>
            <{/foreach}>
        </div>
    </div>
<{/if}>

<{if $topics}>
    <div class="article-section article-topics">
        <div class="article-title">
            <{php}>echo art_constant("MD_TOPIC");<{/php}>
        </div>
        <div class="article-list">
            <{foreach item=top from=$topics}>
            <span class="article-button><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>
            /cp.article.php?topic=<{$top.id}>&amp;from=<{$from}>"><{$top.title}></a></span>
            <{/foreach}>
        </div>
    </div>
<{/if}>
