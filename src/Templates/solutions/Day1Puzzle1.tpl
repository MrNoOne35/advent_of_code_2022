<hr />
<h3>Solution: <b class="solution">{$result.solution}</b></h3>
<hr />

<h4>Winner</h4>
{foreach $result.winner as $number => $calories}
    <ul>
        <li>{$number}: {$calories}</li>
    </ul>
{/foreach}
<h4>List of elves</h4>
{foreach $result.elves as $number => $calories}
    <ul>
        <li>{$number}: {$calories}</li>
    </ul>
{/foreach}