<{assign var="num_column" value=3}> <{* Set the column number *}>
<{assign var="ful_width" value=95}>  <{* Set the full width for multiple columns *}>
<{assign var="col_width" value=$ful_width/$num_column|@floor}>  <{* calculate column width *}>

<div>
    <div>

        <{foreach item=sponsor name=sponsor from=$sponsors}>
        <div style="width: <{$col_width}>%; display: inline; float: left; margin: 5px;">
            <a href="<{$sponsor.url}>" target="_blank"><{$sponsor.title}></a>
        </div>

        <{if $num_column != 0 && $smarty.foreach.sponsor.iteration % $num_column eq 0}>
    </div>
    <div class="clear"></div>
    <div>
        <{/if}>

        <{/foreach}>

    </div>
</div>
