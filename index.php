<!DOCTYPE html>
<html>
	<head>
		<title>Software Deployment and Evolution Assignment 1: 9525637, 9525017</title>
	</head>
	<body>
		<?php
			//Check POST data, if present add to database
			$connection = pg_connect("host=localhost dbname=db_birthdate_app user=birthdate password=password");
			
			pg_query($connection, "CREATE TABLE IF NOT EXISTS version (version int)");
			
			// migration code here
			
			$results = pg_query($connection, "SELECT version FROM version");
			$line = pg_fetch_array($results, null, PGSQL_ASSOC);
			if (!$line)
			{	
				pg_query($connection, "INSERT INTO version VALUES(1)");
			}
			
			pg_query($connection, "CREATE TABLE IF NOT EXISTS birthdates (name CHAR(64), birthdate timestamp, dateEntered timestamp)");
			
			
			if (isset($_POST["name"]))
			{
				pg_query($connection, "INSERT INTO birthdates VALUES ('" . $_POST['name'] . "','" . $_POST['birthdate'] . "', now())");
			}
		?>
		
		<form method="post" action="index.php">
			<input type="text" name="name" id="name" placeholder="Your Name" />
			<input type="date" name="birthdate" id="birthdate" placeholder="Birth Date" />
			<input type="submit" value="Submit" />
		</form>
		
		<h2>Submissions</h2>
		<?php
			//Display table of previously entered values (in reverse chronological order)
			$result = pg_query($connection, "SELECT dateEntered, name, birthdate, DATE_PART('day', now() - birthdate)  FROM birthdates ORDER BY dateEntered DESC");
			
			echo "<table>";
			echo "<tr><th>TimeStamp</th><th>Name</th><th>Birthdate</th><th>Days alive</th></tr>";
			
			while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
				
				echo "<tr>";
				
				foreach ($line as $col_value) {
					echo "<td>" . $col_value . "</td>";
				}
				
				
				
				echo "</tr>";		
				
			}
			
			echo "</table>";
			
			pg_free_result($result);
			pg_close($connection);
		?>
	</body>
</html>