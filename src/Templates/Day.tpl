{include 'static/header.tpl'}
{include 'static/nav.tpl'}
{include 'static/inputs.tpl'}
{include 'static/title.tpl'}

{if $param_day && $param_puzzle && isset($result)}
    {assign var='resultFile' value="solutions/Day"|cat:$param_day|cat:'Puzzle'|cat:$param_puzzle|cat:'.tpl'}
    {include $resultFile}
{/if}

{include 'static/footer.tpl'}