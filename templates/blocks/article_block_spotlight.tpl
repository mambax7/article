<h3><{$block.title}></h3>

<div style="clear: both;">
    <{if $block.image.url}>
        <div style="float: left; padding: 5px;">
            <img src="<{$block.image.url}>" alt="<{$block.image.caption}>">
        </div>
    <{/if}>
    <div style="padding: 5px 0px;">
        <span><{$block.lang_author}>: <{$block.writer|default:$block.author}></span>
        <span><{$block.lang_time}>: <{$block.time}></span>
        <br>
        <{if $block.sp_note}>
            <span style="font-weight: bold;"><{$block.sp_note}></span>
            <br>
        <{/if}>
        <{if $block.summary}>
            <span><{$block.summary}></span>
        <{/if}>
    </div>

    <div>
        <a href="<{$block.url}>" title="<{$smarty.const._MORE}>"><{$smarty.const._MORE}></a>
    </div>
    <br style="clear: both;">
</div>
