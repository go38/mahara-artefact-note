{include file="header.tpl"}
<div><a style="float:right;"class="btn" href="{$WWWROOT}artefact/note/ui/edit.php?id=0">+</a><span style="clear:both;"></span></div>
<div class="vflow">
    {foreach from=$notes item=note}
    <div class="note">
        <div class ="header">
            <a class="action" href="{$WWWROOT}artefact/note/ui/delete.php?id={$note->id}">
                <img src="{$WWWROOT}artefact/note/theme/raw/static/images/delete.gif" alt="edit" />
            </a>
            <a class="action" href="{$WWWROOT}artefact/note/ui/edit.php?id={$note->id}">
                <img src="{$WWWROOT}artefact/note/theme/raw/static/images/edit.gif" alt="edit" />
            </a>
            <a href="{$WWWROOT}artefact/note/ui/view.php?id={$note->id}">
                {$note->title|safe}
            </a>
        </div>
        <div class="content">{$note->description|safe}</div>
        <div class="footer"></div>
    </div>
    {/foreach}
</div>
<div style="clear:both;"></div>
{$pagination|safe}
{include file="footer.tpl"}