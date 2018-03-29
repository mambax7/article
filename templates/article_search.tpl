<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->

<div class="article-breadcrumbs">
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$modulename}></a>: <a
            href="search.php"><{$smarty.const._SR_SEARCH}></a>
</div>

<{if $search_info}>
    <div class="resultMsg"> <{$search_info}> </div>
    <{if $results}>
        <{foreach item=result from=$results}>
        <div class="item">
            <strong><a href="<{$result.link}>"><{$result.art_title}></a></strong><br>
            <{$result.author}> <{$result.art_time}>
            <{if $result.text}>
                <br>
                <{$result.text}>
            <{/if}>
        </div>
        <div class="clear"></div>
    <{/foreach}>
    <{/if}>
<{/if}>

<form name="search" action="search.php" method="post">
    <{securityToken}><{*//mb*}>
    <table class="outer" border="0" cellpadding="1" cellspacing="0" align="center" width="95%">
        <tr>
            <td>
                <table border="0" cellpadding="1" cellspacing="1" width="100%" class="head">
                    <tr>
                        <td class="head" width="10%" align="right"><strong><{$smarty.const._SR_KEYWORDS}></strong></td>
                        <td class="even"><input type="text" name="term" value="<{$search_term}>" size="50"></td>
                    </tr>
                    <tr>
                        <td class="head" align="right"><strong><{$smarty.const._SR_TYPE}></strong></td>
                        <td class="even"><{$type_select}></td>
                    </tr>
                    <tr>
                        <td class="head" align="right"><strong><{php}>echo art_constant("MD_CATEGORY");<{/php}></strong>
                        </td>
                        <td class="even"><{$category_select}></td>
                    </tr>
                    <tr>
                        <td class="head" align="right"><strong><{$smarty.const._SR_SEARCHIN}></strong></td>
                        <td class="even"><{$searchin_select}></td>
                    </tr>
                    <tr>
                        <td class="head" align="right">
                            <strong><{php}>echo art_constant("MD_SUBMITTER");<{/php}></strong>&nbsp;</td>
                        <td class="even"><input type="text" name="uname" value="<{$search_user}>"></td>
                    </tr>
                    <tr>
                        <td class="head" align="right"><strong><{php}>echo art_constant("MD_SORTBY");<{/php}></strong>&nbsp;
                        </td>
                        <td class="even"><{$sortby_select}></td>
                    </tr>
                    <{if $search_rule}>
                        <tr>
                            <td class="head" align="right"><strong><{$smarty.const._SR_SEARCHRULE}></strong>&nbsp;</td>
                            <td class="even"><{$search_rule}></td>
                        </tr>
                    <{/if}>
                    <tr>
                        <td class="head" align="right">&nbsp;</td>
                        <td class="even"><input type="submit" name="submit" value="<{$smarty.const._SUBMIT}>">&nbsp;
                            <input type="reset" name="cancel" value="<{$smarty.const._CANCEL}>"></td>
                </table>
            </td>
        </tr>
    </table>
</form>
<!-- end module contents -->
