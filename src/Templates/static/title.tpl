{if isset($param_day)}
    <h1>
        Day: {$param_day}

        {if isset($param_puzzle)}
            , Puzzle: {$param_puzzle}

            {if isset($param_input)}
                , Input: {$param_input}
            {/if}
        {/if}
    </h1>
{/if}