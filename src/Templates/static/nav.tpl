{if isset($days)}
    <nav>
        {foreach $days as $day => $data}
            <a href="{$data.url}"
               {if isset($param_day) && $param_day == $day}class="selected"{/if}>Day {$day}
            </a>
        {/foreach}
    </nav>
    {if isset($param_day) && isset($days[$param_day].puzzles)}
        <nav>
            {foreach $days[$param_day].puzzles as $puzzleNumber => $url}
                <a href="{$url}"
                   {if isset($param_puzzle) && $param_puzzle == $puzzleNumber}class="selected"{/if}>Puzzle {$puzzleNumber}
                </a>
            {/foreach}
        </nav>
    {/if}
{/if}