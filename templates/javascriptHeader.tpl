{if !empty($javascriptHeaderFiles)}
{foreach $javascriptHeaderFiles AS $file}
	{if !empty($file.url)}
		{if !empty($file.comment)}
			<!--
				{$file.comment}
			-->
		{/if}
	<script {if !empty($file.id)}id="{$file.id}"{/if} type="{if !empty($file.type)}{$file.type}{else}application/javascript{/if}" src="{$file.url}"></script>
	{/if}
{/foreach}
{/if}

{if !empty($javascriptHeaderCodes)}

	<script type="application/javascript">
		jQuery(document).ready(function(){
			{foreach $javascriptHeaderCodes AS $code}
				{$code}
			{/foreach}
		});
	</script>
{/if}