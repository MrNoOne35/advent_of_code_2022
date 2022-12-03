{if isset($days)}
    {if !empty($days)}
        <nav>
            {foreach $days as $day => $data}
                <a href="{$data.url}"
                   {if isset($param_day) && $param_day == $day}class="selected"{/if}>Day {$day}
                </a>
            {/foreach}
        </nav>
    {else}
        <div>You do not have any puzzle inpits set. Puzzle inputs should have been in directories with name of day number and their type of file should bee txt. For example /public/puzzles/1/test1.txt, /public/puzzles/23/input.txt</div>
    {/if}
{/if}