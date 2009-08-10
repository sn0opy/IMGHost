<table>
	<tr>
		<td colspan="2" rowspan="2"><img src="<?=$thumbausgabe?>" alt="" /></td>
	</tr>
	<tr>
		<td>Gr&ouml;&szlig;e: <br/>Bildname: <br/>Dateityp: </td>
		<td><?=$size?> kb<br/><?=$thename?><br/><?=$typeausgabe?></td>
	</tr>
</table>


<table>
	<tr>
		<td colspan="2"><h3>Direktlinks</h3></td>
	</tr>
	<tr>
		<td>Voll: </td>
		<td><input type='text' value='<?=$fullausgabe?>' size='70' onClick="this.select();" /></td>
	</tr>
	<tr>
		<td>Thumb: </td>
		<td><input type='text' value='<?=$thumbausgabe?>' size='70' onClick="this.select();" /></td>
	</tr>
	<tr>
		<td colspan="2"><h3>Vollansicht</h3></td>
	</tr>
	<tr>
		<td>Forum: </td>
		<td><input type='text' value='<?=$bbcodeausgabe?>' size='70' onClick="this.select();" /></td>
	</tr>
	<tr>
		<td>HTML: </td>
		<td><input type='text' value='<?=$htmlcodeausgabe?>' size='70' onClick="this.select();" /></td>
	</tr>
	<tr>
		<td colspan="2"><h3>Thumbnails</h3></td>
	</tr>
	<tr>
		<td>Forum: </td>
		<td><input type='text' value='<?=$bbcodeausgabethumb?>' size='70' onClick="this.select();" /></td>
	</tr>
	<tr>
		<td>HTML: </td>
		<td><input type='text' value='<?=$htmlcodeausgabethumb?>' size='70' onClick="this.select();" /></td>
	</tr>	
	<tr>
		<td colspan="2"><h3>Sonstiges</h3></td>
	</tr>
	<tr>
		<td>Clickcounter: </td>
		<td><input type='text' value='<?=$fullausgabeclick?>' size='70' onClick="this.select();" /></td>
	</tr>
	<tr>
		<td>L&ouml;schlink: </td>
		<td><input type='text' value='<?=$deletelink?>' size='70' onClick="this.select();" /></td>
	</tr>
<?if($globvar['twitter']):?>
	<tr>
		<td>Twittern: </td>
		<td><a href="<?=$twitterausgabe?>">Hier klicken (twitter.com)</a></td>
	</tr>
<?endif;?>
</table>

