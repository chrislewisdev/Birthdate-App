<!DOCTYPE html>
<html>
	<head>
		<title>Software Deployment and Evolution Assignment 1: 9525637, 9525017</title>
	</head>
	<body>
		<?php
			ini_set('display_errors',1);
			ini_set('display_startup_errors',1);
			error_reporting(-1);

			//Check POST data, if present add to database
			$host = getenv("DB_HOST");
			$db_name = getenv("DB_NAME");
			$user = getenv("DB_USER");
			$password = getenv("DB_PASSWORD");
			$connection = pg_connect("host=$host dbname=$db_name user=$user password=$password");

			pg_query($connection, "CREATE TABLE IF NOT EXISTS version (version int)");

			$results = pg_query($connection, "SELECT version FROM version");
			$line = pg_fetch_array($results, null, PGSQL_ASSOC);
			if (!$line)
			{
				pg_query($connection, "INSERT INTO version VALUES(2)");
			}
			else if ($line["version"] == 1)
			{
				//Upgrade from v1 to v2
				pg_query($connection, "ALTER TABLE birthdates ADD lastName CHAR(64) DEFAULT ''");
				pg_query($connection, "UPDATE version SET version = 2");
			}

			pg_query($connection, "CREATE TABLE IF NOT EXISTS birthdates (name CHAR(64), birthdate timestamp, dateEntered timestamp)");


			if (isset($_POST["name"]))
			{
				pg_query($connection, "INSERT INTO birthdates VALUES ('" . $_POST['name'] . "','" . $_POST['birthdate'] . "', now(),'" . $_POST['last-name'] . "')");
			}
		?>

		<form method="post" action="index.php">
			<input type="text" name="name" id="name" placeholder="Your First Name" />
			<input type="text" name="last-name" id="last-name" placeholder="Your Last Name" />
			<input type="date" name="birthdate" id="birthdate" placeholder="Birth Date" />
			<input type="submit" value="Submit" />
		</form>

		<h2>Submissions</h2>
		<?php
			//Display table of previously entered values (in reverse chronological order)
			//Martian Days are 2.7% longer than Earth's, so a simple divide should do....
			$result = pg_query($connection, "SELECT dateEntered, name, lastName, birthdate, DATE_PART('day', now() - birthdate) / 1.027 FROM birthdates ORDER BY dateEntered DESC");

			echo "<table>";
			echo "<tr><th>TimeStamp</th><th>First Name</th><th>Last Name</th><th>Birthdate</th><th>Martian Days Alive</th></tr>";

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
