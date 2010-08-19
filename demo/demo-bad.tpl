{if $test}
  <h3>Testing smarty-lint</h3>
{/if}

{foreach from=$foo item=bar}
  {$bar|print_r}<br />
  {continue}
