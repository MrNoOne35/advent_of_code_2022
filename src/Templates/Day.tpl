{include 'static/header.tpl'}
{include 'static/nav.tpl'}
{include 'static/inputs.tpl'}
{include 'static/title.tpl'}

{if $param_day && (isset($puzzle1) || isset($puzzle2))}
    <hr />
    <h3>Puzzle 1 solution:
        {if isset($puzzle1)}
            <b class="solution">{$puzzle1}</b>
        {else}
            <b class="nosolution">No solution yet</b>
        {/if}
    </h3>
    <hr />
    <hr />
    <h3>Puzzle 2 solution:
        {if isset($puzzle2)}
            <b class="solution">{$puzzle2}</b>
        {else}
            <b class="nosolution">No solution yet</b>
        {/if}
    </h3>
    <hr />
{/if}

{include 'static/footer.tpl'}