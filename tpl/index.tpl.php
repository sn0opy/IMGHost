<form enctype="multipart/form-data" action="index.php" method="post"> 
	<p><?php if($globvar['needpassword']) : ?>Passwort: <input type="text" name="password" size="10" /> <?php endif; ?><input type="file" name="nfile" size="30" /> <input type="submit" name="nsubmit" value="Upload" /></p>
</form>

