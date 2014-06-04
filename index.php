<!DOCTYPE html!>
<html>
<head>
	<title> Home </title>
		<meta http-equiv="content-Type" content="Text/html;charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="studentplattformstyle.css" />

</head>
<body>
<div id="topBanner">
	<div id="summariesListHeader">
		<?php
		echo "Latest Summaries";
		?>
	</div>
	<div id="searchBar">
		<form action="search.php" method="GET">
        	<input type="text" name="query" />
        	<input type="submit" value="Search" />
		</form>
	</div>
</div>
<div id="rightBanner1">
<div id="rightBanner1Content">

<p>Post a new summary!</p>

<?php	
// värden för pdo
$host     = "localhost";
$dbname   = "test";
$username = "guestbook";
$password = "123";
// göra pdo
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
$attr = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
$pdo = new PDO($dsn, $username, $password, $attr);

if($pdo)
{
	//har ngt postats? skriv till databas
	 if(!empty($_POST))
	 {
		$_POST = null;
		$course_id = filter_input(INPUT_POST, 'course_id');
		$title = filter_input(INPUT_POST, 'title');
		$summary = filter_input(INPUT_POST, 'summary');
		$id = filter_input(INPUT_POST, 'id');
		$user_id = filter_input(INPUT_POST, 'user_id');

		$statement = $pdo -> prepare("INSERT INTO summaries (date, course_id, title, summary, id, user_id) VALUES (NOW(), :course_id, :title, :summary, :id, :user_id)");
		
		$statement->bindParam(":course_id", $course_id);
		$statement->bindParam(":title", $title); 
		$statement->bindParam(":summary", $summary);
		$statement->bindParam(":id", $id);
		$statement->bindParam(":user_id", $user_id);
		if(!$statement->execute())
			print_r($statement->errorInfo());

	 }
?>

<form action="index.php" method="POST">
<!--Author selection-->
<p>
	<label for="user_id"> Author: </label><br />
	<select id="authorSelect" name="user_id">
		<?php
		
		foreach ($pdo->query("SELECT * FROM users ORDER BY name") as $row)
		{
		echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
		}
		?>
	</select>
</p>

<!--Course selection-->
<p>
	<label for="course_id"> Course: </label><br />
	<select id="courseSelect" name="course_id">
		<?php
		
		foreach ($pdo->query("SELECT * FROM courses ORDER BY name") as $row)
		{
		echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
		}
		?>
	</select>
</p>

<!--Title field-->
<p>
	<label for="title"> Title: </label><br />
	<textarea id="titleField" name="title" value="$title" rows="1" cols="25" placeholder="Add a title!"required="required"></textarea>
</p>

<!--Summary field--><p>
	<label for="summary"> Summary: </label><br />
	<textarea id="summaryField" name="summary" value="$summary" rows="10" cols="25" placeholder="Write a summary!" required="required"></textarea>
</p>

<!--Post button-->
<input type="submit" value="Post" />
</form>

</div>
</div>

<div id="rightBanner2">
<div id="rightBanner2Content">

<?php

echo "<ul id=\"courseList\">";
echo "<li><a href=\"index.php\"><div class=\"courseListItem\">All Courses</div></a></li>";
	foreach ($pdo->query("SELECT * FROM courses ORDER BY name") as $row){
		echo "<li><a href=\"?course_id={$row['id']}\"><div class=\"courseListItem\">{$row['name']}</div></a></li>";
	}
echo "</ul>";

?>
	
</div>
</div>

<div id="rightBanner3">
<div id="rightBanner3Content">

<?php

echo "<ul id=\"userList\">";
echo "<li><a href=\"index.php\"><div class=\"userListItem\">All Users</div></a></li>";
	foreach ($pdo->query("SELECT * FROM users ORDER BY name") as $row){
		echo "<li><a href=\"?user_id={$row['id']}\"><div class=\"userListItem\">{$row['name']}</div></a></li>";
	}
echo "</ul>";

?>
	
</div>
</div>

<div id="summariesList">

<?php

if (!empty($_GET))
{
$_GET = null;
$course_id = filter_input(INPUT_GET, 'course_id', FILTER_VALIDATE_INT);
$user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
$statement = $pdo->prepare("SELECT summaries.*,courses.name AS course_name, users.name AS user_name FROM summaries 
							JOIN courses ON courses.id=summaries.course_id 
							JOIN users ON users.id=summaries.user_id WHERE course_id=:course_id 
							OR user_id=:user_id ORDER BY date DESC");
$statement->bindParam(":course_id", $course_id);
$statement->bindParam(":user_id", $user_id);

if($statement->execute())
{
	$y = 1;
	while($row = $statement->fetch())
	{
	if($y <= 10){
	if($y % 2){
	echo "<div class=\"summaryBoxGrey\"><div id=\"summaryTitle\"><p>{$row['title']}</p></div><br />
	<div id=\"summaryDateAndUSer\"><p>Date: {$row['date']}&nbsp;&nbsp;Author: {$row['user_name']}&nbsp;&nbsp; 
	Course: {$row['course_name']}&nbsp;&nbsp; Summary ID: {$row['id']}</p></div><br />
	<div id=\"summary\"><p>{$row['summary']}</p></div></div>";
	}
	else{
	echo "<div class=\"summaryBoxWhite\"><div id=\"summaryTitle\"><p>{$row['title']}</p></div><br />
	<div id=\"summaryDateAndUSer\"><p>Date: {$row['date']}&nbsp;&nbsp;Author: {$row['user_name']}&nbsp;&nbsp; 
	Course: {$row['course_name']}&nbsp;&nbsp; Summary ID: {$row['id']}</p></div><br />
	<div id=\"summary\"><p>{$row['summary']}</p></div></div>";
	}
	}
	$y++;
	}
}
else
{
	print_r($statement->errorInfo());
}

}
else
{
$x = 1;
foreach ($pdo->query("SELECT summaries.* ,courses.name AS course_name,users.name AS user_name FROM summaries 
		JOIN courses ON courses.id=summaries.course_id 
		JOIN users ON users.id=summaries.user_id ORDER BY date DESC") as $row)
	{
	if($x <= 10){
	if($x % 2){
	echo "<div class=\"summaryBoxGrey\"><div id=\"summaryTitle\"><p>{$row['title']}</p></div><br />
	<div id=\"summaryDateAndUSer\"><p>Date: {$row['date']}&nbsp;&nbsp;Author: {$row['user_name']}&nbsp;&nbsp;
	Course: {$row['course_name']}&nbsp;&nbsp; Summary ID: {$row['id']}</p></div><br />
	<div id=\"summary\"><p>{$row['summary']}</p></div></div>";
	}
	else{
	echo "<div class=\"summaryBoxWhite\"><div id=\"summaryTitle\"><p>{$row['title']}</p></div><br />
	<div id=\"summaryDateAndUSer\"><p>Date: {$row['date']}&nbsp;&nbsp;Author: {$row['user_name']}&nbsp;&nbsp;
	Course: {$row['course_name']}&nbsp;&nbsp; Summary ID: {$row['id']}</p></div><br />
	<div id=\"summary\"><p>{$row['summary']}</p></div></div>";
	}
	}
	$x++;
	}
}
echo "<div id=\"noSummariesMessage\">No summaries found.</div>";
}


// visa alla användare(ul)

//om user klickat på ett namn, visa dess inlägg
// annars visa alla inlägg

?>

</div>

</body>
</html>