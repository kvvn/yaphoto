<div id="titleExt"><h5>{widget('path')}<span class="ext">Галерея</span></h5></div>

{if is_array($albums)}
<table width="100%">
	<tr>
		{$counter = 1}
		{foreach $albums as $album}     
			{if $album.title!='Неразобранное'}
			<td width="50%" align="middle">
				<a href="{site_url('yandexphoto/index/' . $album.id)}" class="image"><img src="{$album.cover}" border="0" /></a>
				<br /><b>{$album.title}</b>
			</td>
			{if $counter == 2}
				</tr><tr>
				{$counter = 0}
			{/if}
			{$counter++}
			{/if}
		{/foreach}
	</tr>
</table>

{else:}
    Альбомов не найдено.
{/if}


