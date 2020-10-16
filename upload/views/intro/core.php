<?php
begin_frame("Folder");
print("
<br /><center><font size='4' color='red'><b>TT Micro Framework</b></font><br />
<font size='3' color='red'><b>
A little help with understanding the core, the cores been kept simple its worth looking through the core folder with the tutorial 
</b></font><br /><br>

<font size='3' color='red'><b>index.php</b></font><br />
<font size='3' color='red'><b>
All we do in index is start the TTcore all pages are loaded through the index using htaccess, 
TTcore::run();
</b></font><br /><br />

<br /><center><font size='4' color='red'><b>The Core</b></font><br /><br />
<font size='3' color='red'><b>TTcore.php</b></font><br />
<font size='3' color='red'><b>
TTcore::run() just gets the things we need using three static functions from the same class<br>
here is the 3 functions the class uses<br>
self::init(); = include files we need like config.php, define paths etc<br>
self::autoload(); = autoload all classes and libs automatically<br>
self::dispatch(); = starts the router class
</b></font><br /><br />

<font size='3' color='red'><b>Router.php</b></font><br />
<font size='3' color='red'><b>
All this class does is get the url and splits it into sections <br>
for example localhost/account/details/id<br>
account is the controller, details is the controller method & the id is a parameter
</b></font><br /><br />

<font size='3' color='red'><b>Controller.php</b></font><br />
<font size='3' color='red'><b>
Now we know the urls are taken care of we use the controller.php to auto load the models & views<br>
You will see the files in controller folder all use this core controller so we keep it simple with two functions<br>
model() = load a model <br>
view() = load a view 
</b></font><br /><br />

<font size='3' color='red'><b>Database.php</b></font><br />
<font size='3' color='red'><b>
We use pdo for database connection
</b></font><br /><br />
");
end_frame();