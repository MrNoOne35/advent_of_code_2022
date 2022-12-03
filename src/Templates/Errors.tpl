{include 'static/header.tpl'}
{if isset($param_errors)}
    <div>You have some errors. Fix them before start</div>
    <ul>
        {foreach $param_errors as $error}
            <li>{$error}</li>
        {/foreach}
    </ul>
{/if}
{include 'static/footer.tpl'}