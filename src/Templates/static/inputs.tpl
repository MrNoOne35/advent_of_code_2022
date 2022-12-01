{if isset($inputs)}
    <ul class="puzzle_inputs">
        {foreach $inputs as $input}
            <li>
                <a href="{$input.url}"
                   {if isset($param_input) && $param_input == $input.name}class="selected"{/if}>{$input.name}</a>
            </li>
        {/foreach}
    </ul>
{/if}
