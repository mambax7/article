<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->

<script type="text/javascript">
    <!--
    function copytext(element) {
        var copyText = document.getElementById(element).value;
        if (window.clipboardData) { // IE send-to-clipboard method.
            window.clipboardData.setData('Text', copyText);

        } else if (window.netscape) {
            // You have to sign the code to enable this or allow the action in about:config by changing user_pref("signed.applets.codebase_principal_support", true);
            netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');

            // Store support string in an object.
            var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
            if (!str) return false;
            str.data = copyText;

            // Make transferable.
            var trans = Components.classes["@mozilla.org/widget/transferable;1"].createInstance(Components.interfaces.nsITransferable);
            if (!trans) return false;

            // Specify what datatypes we want to obtain, which is text in this case.
            trans.addDataFlavor("text/unicode");
            trans.setTransferData("text/unicode", str, copyText.length * 2);

            var clipid = Components.interfaces.nsIClipboard;
            var clip = Components.classes["@mozilla.org/widget/clipboard;1"].getService(clipid);
            if (!clip) return false;

            clip.setData(trans, null, clipid.kGlobalClipboard);
        }
    }
    //-->
</script>

<{* $xoTheme->addStylesheet("modules/$xoops_dirname/assets/css/style_blog.css") *}>

<{if $header}>
    <div class="article-breadcrumbs"><{$header}></div>
<{/if}>

<!-- Spotlight -->
<{if $spotlight}>
    <{assign var="article" value=$spotlight}>
    <div class="article-section article-spotlight">
        <{include file="db:`$xoops_dirname`_item_blog.tpl"}>
    </div>
    <br style="clear:both;">
<{/if}>

<{assign var="default_image" value="`$xoops_url`/modules/`$xoops_dirname`/assets/images/xoops.png"}>

<!-- Featured articles -->
<{if $features|is_array && count($features) > 0 }>
    <div class="article-section article-feature">
        <div class="article-section-title">
            <span class="subject"><{php}>echo art_constant("MD_FEATURED");<{/php}></span>
            <span class="navigation"><a
                        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.list.php<{$smarty.const.URL_DELIMITER}>f"><{$smarty.const._MORE}></a></span>
        </div>

        <div class="article-section-container">
            <{foreach item=article from=$features}>
            <div class="article-list">
                <{include file="db:`$xoops_dirname`_item_blog.tpl"}>
            </div>
            <{/foreach}>
        </div>
    </div>
<{/if}>

<!-- Recent articles -->
<{if $articles|is_array && count($articles) > 0}>
    <div id="list-article" class="article-section list-article">
        <div class="article-section-title">
        <span class="subject">
            <{php}>echo art_constant("MD_ARTICLES");<{/php}>
        </span>
            <span class="navigation">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.list.php"><{$smarty.const._MORE}></a>
        </span>
        </div>

        <div class="article-section-container">
            <{foreach item=article from=$articles}>
            <div class="article-list">
                <{include file="db:`$xoops_dirname`_item_blog.tpl"}>
            </div>
            <{/foreach}>
        </div>
    </div>
    </div>
<{/if}>

<div id="pagenav" class="article-section pagenav">
    <{$pagenav}>
</div>

<{if $categories|is_array && count($categories) > 0}>
    <div id="category" class="article-section article-category">
        <div class="article-section-title">
            <span class="subject"><{php}>echo art_constant("MD_CATEGORIES");<{/php}></span>
            <span class="navigation">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.directory.php"><{$smarty.const._MORE}></a>
        </span>
        </div>

        <div class="article-section-container">
            <{foreach item=cat name=cat from=$categories}>
            <span class="article-term">
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.category.php<{$smarty.const.URL_DELIMITER}><{$cat.id}>"><{$cat.title}></a>
            (<acronym title='<{php}>echo art_constant("MD_SUBCATEGORIES");<{/php}>'><{$cat.categories}></acronym>|<acronym
                        title='<{php}>echo art_constant("MD_ARTICLES");<{/php}>'><{$cat.articles}></acronym>)
        </span>
            <{/foreach}>
        </div>
    </div>
<{/if}>

<!-- API -->
<div id="article-api" class="article-section article-api">
    API: <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/xml.php<{$smarty.const.URL_DELIMITER}>rss"
            target="api"><{php}>echo art_constant("MD_RSS");<{/php}></a>
    | <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/xml.php<{$smarty.const.URL_DELIMITER}>rdf"
         target="api"><{php}>echo art_constant("MD_RDF");<{/php}></a>
    | <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/xml.php<{$smarty.const.URL_DELIMITER}>atom"
         target="api"><{php}>echo art_constant("MD_ATOM");<{/php}></a>
    <{if $xoops_isadmin}>
        || <a
        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/admin/" title="<{php}>echo art_constant("MD_CPANEL");<{/php}>"><{php}>echo art_constant("MD_CPANEL");<{/php}></a>
        | <a
        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.category.php" title="<{php}>echo art_constant("MD_CPCATEGORY");<{/php}>"><{php}>echo art_constant("MD_CATEGORY");<{/php}></a>
        | <a
        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.topic.php" title="<{php}>echo art_constant("MD_CPTOPIC");<{/php}>"><{php}>echo art_constant("MD_TOPIC");<{/php}></a>
        | <a
        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/cp.article.php" title="<{php}>echo art_constant("MD_CPARTICLE");<{/php}>"><{php}>echo art_constant("MD_ARTICLE");<{/php}></a>
    <{/if}>
    <{if $xoops_isuser}>
        | <a
        href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.author.php" title="<{php}>echo art_constant("MD_MYARTICLES");<{/php}>"><{php}>echo art_constant("MD_MYARTICLES");<{/php}></a>
        ||
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/edit.article.php"><{php}>echo art_constant("MD_ADDARTICLE");<{/php}></a>
    <{/if}>
    <div class="clear"></div>
</div>

<{if $xoops_notification}>
    <{include file='db:system_notification_select.tpl'}>
<{/if}>
