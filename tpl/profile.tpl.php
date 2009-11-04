<div class="pageRow"><span class="floatleft">Seite: &nbsp;</span> 
<?php for($i = 1; $i <= $pageCount; $i++) { ?>
	<a href="./?profile&p=<?php echo $i; ?>"><?php echo $i; ?></a>
<?php } ?>
</div>

<?php foreach($output as $row) { ?>
<div class="outRow">
	<div class="outPicture">
		<a href="./i/<?php echo $row[0]; ?>"><img src="./i/t/<?php echo $row[0]; ?>" alt="" /></a>
	</div>
	<div class="outLinks">
		<div class="formRow">
			<div class="formValue"><p>Klicks: <?php echo $row[1]; ?> - Hochgeladen: <?php echo $row[2]; ?> Uhr <a href="./?d=<?php echo $row[0]; ?>&amp;c=<?php echo $row[3]; ?>" /><img src="inc/img/delete.png" alt="L&ouml;schen" /></a></p></div>
		</div>
		<div class="formRow">
			<div class="formLabel"><span>Direktlink</span></div>
			<div class="formValue"><p><input type="text" onClick="this.select();" value="http://<?php echo $serverurl ?>i/<?php echo $row[0]; ?>" /></p></div>
		</div>
		<div class="formRow">
			<div class="formLabel"><span>BBcode</span></div>
			<div class="formValue"><p><input type="text" onClick="this.select();" value="[url=http://<?php echo $serverurl ?>i/<?php echo $row[0]; ?>][img]http://<?php echo $serverurl ?>i/t/<?php echo $row[0]; ?>[/img][/url]" /></p></div>
		</div>
		<div class="formRow">
			<div class="formLabel"><span>Klickcount</span></div>
			<div class="formValue"><p><input type="text" onClick="this.select();" value="http://<?php echo $serverurl ?>?s=<?php echo $row[0]; ?>" /></p></div>
		</div>
	</div>
</div>
<?php } ?>

<div class="pageRowB clearfix"><span class="floatleft">Seite: &nbsp;</span> 
<?php for($i = 1; $i <= $pageCount; $i++) { ?>
	<a href="./?profile&p=<?php echo $i; ?>"><?php echo $i; ?></a>
<?php } ?>
</div>
