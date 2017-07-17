<div id="titleExt"><h5>{widget('path')}<span class="ext">Альбом</span></h5></div>

{if is_array($photos)}
<table>
	<tr>
		{$counter = 1}
		{foreach $photos as $photo}     
			<td>
				<a href="{$photo.image}" rel="prettyPhoto[pp_gal]" title="{$photo.title}"><img src="{$photo.preview}" /></a>
			</td>
			{if $counter == 4}
				</tr><tr>
				{$counter = 0}
			{/if}
			{$counter++}
		{/foreach}
	</tr>
</table>

{else:}
    Фото не найдено.
{/if}

{literal}
<script type="text/javascript" charset="utf-8">
  $(document).ready(function(){
    $("a[rel^='prettyPhoto']").prettyPhoto({social_tools:''});
  });
</script>
{/literal}