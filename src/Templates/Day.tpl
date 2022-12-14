{include 'static/header.tpl'}
{include 'static/nav.tpl'}
{include 'static/inputs.tpl'}
{include 'static/title.tpl'}

{if $param_day && (isset($puzzle1) || isset($puzzle2))}
    <hr/>
    <h3>Puzzle 1 solution:
        {if isset($puzzle1)}
            {if is_numeric($puzzle1)}
                <br>
                <pre class="solution">{$puzzle1}</pre>
            {elseif is_array($puzzle1)}
                <br>
                <pre class="solution">{$puzzle1.solution}</pre>
                <pre class="solution">{$puzzle1.draw}</pre>
            {/if}

        {else}
            <b class="nosolution">No solution yet</b>
        {/if}
    </h3>
    <hr/>
    <hr/>
    <h3>Puzzle 2 solution:
        {if isset($puzzle2)}
            {if is_numeric($puzzle2)}
                <br>
                <pre class="solution">{$puzzle2}</pre>
            {elseif is_array($puzzle2)}
                <br>
                <pre class="solution">{$puzzle2.solution}</pre>
                <pre class="solution">{$puzzle2.draw}</pre>
            {/if}
        {else}
            <b class="nosolution">No solution yet</b>
        {/if}
    </h3>
    <hr/>
{/if}

{include 'static/footer.tpl'}