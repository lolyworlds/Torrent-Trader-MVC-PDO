<?php
begin_frame("General");
	print ("<br /><center><font size='3' color='orange'>Intro into PDO in v3 these have sqli before them</font><br /><font size='2' color='orange'>This is a rough guide to assist</font><br /> ~ ");
	print ("<a href='#query_exec'> query_exec</a> ~ ");
	print ("<a href='#fetch_array'> fetch_array </a> ~ ");
	print ("<a href='#fetch_assoc'> fetch_assoc</a> ~ <br />");
	print (" ~ <a href='#fetch_row'> fetch_row </a> ~");
	print ("<a href='#num_rows'> num_rows </a> ~ ");

	print ("<a href='#real_escape_string'> real_escape_string</a> ~ ");
	print ("<a href='#insert_id'> insert_id </a> ~ ");
	print ("<a href='#errno'> errno </a> ~ <br />");
	print (" ~ <a href='#affected_row'> affected_row </a> ~");
	print ("<a href='#result'>result </a> ~ ");
	print ("<a href='#result'>both </a> ~ ");
          end_frame();

print ("<div id='query'></div>");
begin_frame("query");
echo '<div style="font-size:1.25em;color:orange">SQLI: </div>';
echo '<div style="font-size:1.25em;color:red">SQL_Query_exec("UPDATE users SET class=$class WHERE id=$userid"); </div></br>';
echo '<div style="font-size:1.25em;color:orange">PDO: </div>';
echo '<div style="font-size:1.25em;color:red">DB::run("UPDATE users SET class=$class WHERE id=$userid"); </div></br>';
echo '<div style="font-size:1.25em;color:orange">PREPARED STATEMENT: </div>';
echo '<div style="font-size:1.25em;color:red">DB::run("UPDATE users SET class=? WHERE id=?",[$class ,$userid]); </div>';
          end_frame();
  
  print ("<div id='fetch_array'></div>");
begin_frame("fetch_array");
echo '<div style="font-size:1.25em;color:orange">SQLI: </div>';
echo '<div style="font-size:1.25em;color:red">$res = SQL_Query_exec("SELECT username FROM users WHERE id=$user[invited_by]");
</br>$row = mysqli_fetch_array($res); </div></br>';
echo '<div style="font-size:1.25em;color:orange">PDO: </div>';
echo '<div style="font-size:1.25em;color:red">$row = DB::run("SELECT username FROM users WHERE id=$user[invited_by]")->fetch();  <font size=2 color=green>Maybe fetch(PDO::FETCH_LAZY) OR fetch(PDO::FETCH_ASSOC)</font></div></br>';
echo '<div style="font-size:1.25em;color:orange">PREPARED STATEMENT: </div>';
echo '<div style="font-size:1.25em;color:red">$invited = $user[invited_by];
</br>$row = DB::run("SELECT username FROM users WHERE id=?", [$invited])->fetch(); </div>';
          end_frame();
          
          print ("<div id='fetch_assoc'></div>");
begin_frame("fetch_assoc");
echo '<div style="font-size:1.25em;color:orange">SQLI: </div>';
echo '<div style="font-size:1.25em;color:red">$res = SQL_Query_exec("SELECT `password`, `secret`, `status` FROM `users` WHERE `id` = $id");
</br>$row = mysqli_fetch_assoc($res); </div></br>';
echo '<div style="font-size:1.25em;color:orange">PDO: </div>';
echo '<div style="font-size:1.25em;color:red">$row = DB::run("SELECT `password`, `secret`, `status` FROM `users` WHERE `id` = $id")->fetch(); </div></br>';
echo '<div style="font-size:1.25em;color:orange">PREPARED STATEMENT: </div>';
echo '<div style="font-size:1.25em;color:red">$row = DB::run("SELECT `password`, `secret`, `status` FROM `users` WHERE `id` =?", [$id])->fetch(); <font size=2 color=green>Maybe fetch(PDO::FETCH_LAZY) OR fetch(PDO::FETCH_ASSOC)</font></div></br>';
          end_frame();
          
          print ("<div id='fetch_row'></div>");
begin_frame("fetch_row");
echo '<div style="font-size:1.25em;color:orange">SQLI: </div>';
echo '<div style="font-size:1.25em;color:red">$r = SQL_Query_exec("SELECT name, parent_cat FROM categories WHERE id=$catid");
</br>$r = mysqli_fetch_row($r); </div></br>';
echo '<div style="font-size:1.25em;color:orange">PDO: </div>';
echo '<div style="font-size:1.25em;color:red">$r = DB::run("SELECT name, parent_cat FROM categories WHERE id=$catid")->fetch(); <font size=2 color=green>Maybe also fetch(PDO::FETCH_LAZY)</font>;
</div></br>';
echo '<div style="font-size:1.25em;color:orange">PREPARED STATEMENT: </div>';
echo '<div style="font-size:1.25em;color:red">$r = DB::run("SELECT name, parent_cat FROM categories WHERE id=?", [$catid]) </div>';
          end_frame();

          
          print ("<div id='num_rows'></div>");
       begin_frame("num_rows");
echo '<div style="font-size:1.25em;color:orange">SQLI: </div>';
echo '<div style="font-size:1.25em;color:red">	$res = SQL_Query_exec("SELECT name, parent_cat FROM categories WHERE id=$catid ");
</br>if (mysqli_num_rows($res) > 0) </div></br>';
echo '<div style="font-size:1.25em;color:orange">PDO: </div>';
echo '<div style="font-size:1.25em;color:red">$res = DB::run("SELECT name, parent_cat FROM categories WHERE id=$catid ");
</br>if ($res->rowCount() > 0) </div></br>';
echo '<div style="font-size:1.25em;color:orange">PREPARED STATEMENT: </div>';
echo '<div style="font-size:1.25em;color:red">$r = DB::run("SELECT name, parent_cat FROM categories WHERE id=?", [$catid])
</br>if ($res->rowCount() > 0) </div>';
 end_frame();

          print ("<div id='real_escape_string'></div>");
begin_frame("real_escape_string");
echo '<div style="font-size:1.25em;color:orange">SQLI: </div>';
echo '<div style="font-size:1.25em;color:red">$reason = mysqli_real_escape_string($GLOBALS["DBconnector"],$_POST["reason"]);</div></br>';
echo '<div style="font-size:1.25em;color:orange">PDO: </div>';
echo '<div style="font-size:1.25em;color:red">$reason = $_POST["reason"]; </div>';
          end_frame();
          
          print ("<div id='insert_id'></div>");
begin_frame("insert_id");
echo '<div style="font-size:1.25em;color:orange">SQLI: </div>';
echo '<div style="font-size:1.25em;color:red">$ret = SQL_Query_exec("INSERT INTO torrents (name, parent_cat WHERE id=$catid)");
</br>$id = mysqli_insert_id($GLOBALS["DBconnector"]); </div></br>';
echo '<div style="font-size:1.25em;color:orange">PDO: </div>';
echo '<div style="font-size:1.25em;color:red">$ret = DB::run("SELECT name, parent_cat FROM categories WHERE id=$catid ");
</br>$id = DB::lastInsertId();</div></br>';
echo '<div style="font-size:1.25em;color:orange">PREPARED STATEMENT: </div>';
echo '<div style="font-size:1.25em;color:red">$ret = DB::run("SELECT name, parent_cat FROM categories WHERE id=?", [$catid])
</br>$id = DB::lastInsertId();</div>';
          end_frame();
          
                    print ("<div id='errno'></div>");
begin_frame("errno");
echo '<div style="font-size:1.25em;color:orange">SQLI: </div>';
echo '<div style="font-size:1.25em;color:red">if (mysqli_errno($GLOBALS["DBconnector"]) == 1062) </div></br>';
echo '<div style="font-size:1.25em;color:orange">PDO: </div>';
echo '<div style="font-size:1.25em;color:red">if ($ret->errorCode() == 1062) </div>';
          end_frame();
          
          print ("<div id='affected_row'></div>");
begin_frame("affected_row");
echo '<div style="font-size:1.25em;color:orange">SQLI: </div>';
echo '<div style="font-size:1.25em;color:red"> SQL_Query_exec("UPDATE name, parent_cat WHERE id=$catid");
</br>if (mysqli_affected_rows($GLOBALS["DBconnector"]) && $self["seeder"] != $seeder){</div></br>';
echo '<div style="font-size:1.25em;color:orange">PDO: </div>';
echo '<div style="font-size:1.25em;color:red">$peerupd = DB::run("UPDATE name, parent_cat WHERE id=$catid");
</br>if ($peerupd && $self["seeder"] != $seeder){ </div></br>';
echo '<div style="font-size:1.25em;color:orange">PREPARED STATEMENT: </div>';
echo '<div style="font-size:1.25em;color:red">$peerupd = DB::run("UPDATE name, parent_cat WHERE id=?", [$catid]);
</br>if ($peerupd && $self["seeder"] != $seeder){ </div>';
          end_frame();
          
          print ("<div id='result'></div>");
begin_frame("result");
echo '<div style="font-size:1.25em;color:orange">SQLI: </div>';
echo '<div style="font-size:1.25em;color:red">$res = SQL_Query_exec("SELECT COUNT(*) FROM messages WHERE `sender` = " . $CURUSER["id"] . " AND `location` = template");
</br>$template = mysqli_result($res, 0); </div></br>';
echo '<div style="font-size:1.25em;color:orange">PDO: </div>';
echo '<div style="font-size:1.25em;color:red">$res = DB::run(""SELECT COUNT(*) FROM messages WHERE `sender` = " . $CURUSER["id"] . " AND `location` = template");
</br>$template = $res->fetchColumn(); </div></br>';
echo '<div style="font-size:1.25em;color:orange">PREPARED STATEMENT: </div>';
echo '<div style="font-size:1.25em;color:red">$res = DB::run(""SELECT COUNT(*) FROM messages WHERE `sender` = ? AND `location` =?", [".$CURUSER["id"]."],[template]);
</br>$template = $res->fetchColumn(); </div>';
          end_frame();
          
                    print ("<div id='both'></div>");
begin_frame("both");
echo '<div style="font-size:1.25em;color:orange">SQLI: </div>';
echo '<div style="font-size:1.25em;color:red">$res = SQL_Query_exec("SELECT `id`, `question`, `answer`, `flag`, `categ` FROM `faq` WHERE `type`=item ORDER BY `order` ASC");
</br>while ($arr = mysqli_fetch_array($res, MYSQLI_BOTH)) {</div></br>';
echo '<div style="font-size:1.25em;color:orange">PDO: </div>';
echo '<div style="font-size:1.25em;color:red">$res = DB::run("SELECT `id`, `question`, `answer`, `flag`, `categ` FROM `faq` WHERE `type`=item ORDER BY `order` ASC");
</br>while ($arr = $res->fetch(PDO::FETCH_BOTH)) {</div>';
          end_frame();