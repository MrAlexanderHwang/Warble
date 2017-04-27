<html>
  <head>
    <script
      src="https://code.jquery.com/jquery-3.2.1.min.js"
      integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
      crossorigin="anonymous">

    </script>

  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Compiled and minified CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/css/materialize.min.css">

  <!-- Compiled and minified JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/js/materialize.min.js"></script>



  <script>
  $(document).ready(function(){
    $('.modal').modal)();
  })
  </script>

  </head>

	<body>

    <nav>
      <div class="nav-wrapper">
        <a href="#" class="brand-logo center">Warble</a>
        <ul id="nav-mobile" class="left hide-on-med-and-down">
          <li><a href = "#!?"> <i class="material-icons">search</i></a></li>
          <li><a href="badges.html">My Account</a></li>
          <li><a href="badges.html">Help</a></li>
          <li><a href="badges.html">About</a></li>

        </ul>
      </div>
    </nav>

    <div class="container">

      <ul class="collapsible" data-collapsible="accordion">
        <li>
          <div class="collapsible-header"><i class="material-icons"></i><b>Post a tweet</b></div>

          <!-- This is the HTML form that appears in the browser -->
          <form class="collapsible-body" action="<?=$_SERVER['PHP_SELF']?>" method="post">
          	Tweet: <input type="text" name="country">
            <input type="submit" name="submit" class="waves-effect waves-light btn">
          </form>

        </li>
      </ul>
	<?php

	    // pass in some info;
		require("common.php");

		if(empty($_SESSION['user'])) {

			// If they are not, we redirect them to the login page.
			$location = "http://" . $_SERVER['HTTP_HOST'] . "/login.php";
			echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.$location.'">';
			//exit;

        	// Remember that this die statement is absolutely critical.  Without it,
        	// people can view your members-only content without logging in.
        	die("Redirecting to login.php");
    	}

		// To access $_SESSION['user'] values put in an array, show user his username
		$arr = array_values($_SESSION['user']);

		// open connection
		$connection = mysqli_connect($host, $username, $password) or die ("Unable to connect!");

		// select database
		mysqli_select_db($connection, $dbname) or die ("Unable to select database!");

		// create query
		$query = "SELECT * FROM symbols";

		// execute query
		$result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());

		// see if any rows were returned
		if (mysqli_num_rows($result) > 0) {

    		// print them one after another
    		echo "<table cellpadding=50 border=1>";
    		while($row = mysqli_fetch_row($result)) {
          //echo "</table>";
          echo "<div class='row'>";
          echo  "<div class='col s12 m5'>";
          echo    "<div class='card-panel teal'>";
          echo      "<span class='white-text'>$row[1]";
          echo      "</span>";
          echo    "</div>";
          echo  "</div>";
          echo "</div>";

        /*
            echo "<tr>";
				echo "<td>".$row[0]."</td>";
        		echo "<td>" . $row[1]."</td>";
        		echo "<td>".$row[2]."</td>";
				echo "<td><a href=".$_SERVER['PHP_SELF']."?id=".$row[0].">Delete</a></td>";
        		echo "</tr>";
            */

    		}

		} else {

    		// print status message
        echo "<script> Materialize.toast('Looks like there isnt anything new here', 6500); // 6500 is the duration of the toast </script>";
		}



		// free result set memory
		mysqli_free_result($connection,$result);

		// set variable values to HTML form inputs
		$country = $_POST['country'];
    	$animal = $_POST['animal'];

		// check to see if user has entered anything
		if ($country != "") {
	 		// build SQL query
			$query = "INSERT INTO symbols (country) VALUES ('$country')";
			// run the query
     		$result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());
			// refresh the page to show new update
	 		echo "<meta http-equiv='refresh' content='0'>";
		}

		// if DELETE pressed, set an id, if id is set then delete it from DB
		if (isset($_GET['id'])) {

			// create query to delete record
			echo $_SERVER['PHP_SELF'];
    		$query = "DELETE FROM symbols WHERE id = ".$_GET['id'];

			// run the query
     		$result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());

			// reset the url to remove id $_GET variable
			$location = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.$location.'">';
			exit;

		}

		// close connection
		mysqli_close($connection);

	?>

  <script>
  $(function(){
    $(".button-collapse").sideNav();
  })
  </script>
   </div>
	</body>
</html>
