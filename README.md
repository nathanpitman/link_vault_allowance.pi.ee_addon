# EE2.x Link Vault Allowance Plug-in

An ExpressionEngine 2 plug-in which can be used with the Link Vault module to enforce a download allowance.

<pre>{exp:link_vault_allowance member_id="1"}
{if allowed}You have sufficient allowance to download this item{/if}
{allowance_lifetime_used}
{allowance_remaining}
{allowance_period}
{allowance_period_cap}
{allowance_period_remaining}
{/exp:link_vault_allowance}</pre>
