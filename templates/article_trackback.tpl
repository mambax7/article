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

<div class="article-list">
    <ul>
        <li>
            <{php}>echo art_constant("MD_URL");<{/php}>:
            <input name="a<{$article.id}>" id="a<{$article.id}>"
                   value="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.article.php<{$smarty.const.URL_DELIMITER}>c<{$article.category}>/<{$article.id}>"
                   type="hidden">
            <span class="article-copytext" onclick="copytext('a<{$article.id}>')"
                  title="URI - <{php}>echo art_constant(" MD_CLICKTOCOPY");<{/php}>" ><{$xoops_url}>
            /modules/<{$xoops_dirname}>
            /view.article.php<{$smarty.const.URL_DELIMITER}>c<{$article.category}>/<{$article.id}></span>

        </li>
        <li>
            <{php}>echo art_constant("MD_TRACKBACK");<{/php}>:
            <input name="t<{$article.id}>" id="t<{$article.id}>"
                   value="<{$xoops_url}>/modules/<{$xoops_dirname}>/trackback.php<{$smarty.const.URL_DELIMITER}><{$article.id}>"
                   type="hidden">
            <span class="article-copytext" onclick="copytext('t<{$article.id}>')"
                  title="Trackback - <{php}>echo art_constant(" MD_CLICKTOCOPY");<{/php}>" ><{$xoops_url}>
            /modules/<{$xoops_dirname}>
            /trackback.php<{$smarty.const.URL_DELIMITER}><{$article.id}></span>
    </ul>
</div>

<{if $trackbacks|is_array && count($trackbacks) > 0 }>
    <div class="article-title"><{php}>echo art_constant("MD_TRACKBACKS");<{/php}></div>
    <div class="article-list">
        <ol>
            <{foreach item=trackback from=$trackbacks}>
            <li><a id="tb<{$trackback.id}>"></a>
                <{php}>echo art_constant("MD_FROM");<{/php}>: <a href="<{$trackback.url}>"
                                                                 target="_blank"><{$trackback.name}></a>
                | <{$trackback.time}>
                <{if $isadmin}>
                    | IP: <{$trackback.ip}> |
                    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/am.trackback.php?op=delete&amp;trackback=<{$trackback.id}>"><{$smarty.const._DELETE}></a>
                <{/if}>
                <br>
                <{$trackback.title}>
                <br>
                <{$trackback.excerpt}>
            </li>
            <{/foreach}>
        </ol>
    </div>
<{/if}>
