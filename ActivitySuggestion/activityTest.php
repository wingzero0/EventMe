<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  
  <meta content="text/html; charset=utf-8" http-equiv="content-type"><title>Simple Activity Insert</title>
  
  
  </head><body>
<p>
<form method="post" action="activityHandler.php" name="defaultTest" target="_blank">
Search recent activity (in one month)<br>
<input type="hidden" name="op" value="default">

limit<input name="limit"><br>
startOffset<input name="startOffset"><br>

<button name="submit">送出</button>
</form>
</p>

<p>
<form method="post" action="activityHandler.php" name="categoryTest" target="_blank">
<input type="hidden" name="op" value="categoryDefault">
Search recent activity with category filter(in one month)<br>
limit<input name="limit"><br>
startOffset<input name="startOffset"><br>
categoryID<input name="categoryID"><br>
<button name="submit">送出</button>
</form>
</p>

<p>
<form method="post" action="activityHandler.php" name="freeTest" target="_blank">
<input type="hidden" name="op" value="freeDefault">
Search recent activity with free filter(in one month)<br>
limit<input name="limit"><br>
startOffset<input name="startOffset"><br>
<button name="submit">送出</button>
</form>
</p>

<p>
<form method="post" action="activityHandler.php" name="recommendTest" target="_blank">
<input type="hidden" name="op" value="recommendedDefault">
Search recent activity with recommend filter(in one month)<br>
userID<input name="userID"><br>
limit<input name="limit"><br>
startOffset<input name="startOffset"><br>
<button name="submit">送出</button>
</form>
</p>

</body></html>
