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


  <script>  $(document).ready(function(){
      $('.materialboxed').materialbox();
          $(document).ready(function(){
        $('.slider').slider();
      });
    });
  </script>

  <script>
  $(document).ready(function(){
    $('.modal').modal();
  })
  </script>

  </head>

  <body  style="background-color:rgba(249,249,249,1);">

    <!-- This is the Navbar at the top of the screen -->
    <nav>
      <div class="nav-wrapper teal accent-4">
        <a href="#" class="brand-logo center">Warble</a>
        <ul id="nav-mobile" class="left hide-on-med-and-down">
          <!-- This is the search part of the navbar -->

          <li><a href="edit.php">Home</a></li>
          <li><a href="help.html">Contact Us</a></li>
          <li><a href="about.html">About</a></li>
          <li><a href="logout.php">Log Out</a></li>
          <li>
            <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
              <div class="input-field">
                <input id="search" type="search" name="search">
                <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                <i class="material-icons">close</i>
              </div>
            </form>
          </li>
        </ul>
      </div>
    </nav>



    <!-- This is the popup that lets you post a tweet -->
    <div class="container">
      <ul class="collapsible" >
        <li>
          <div class="collapsible-header"><b>Post a Warble</b></div>

          <!-- This is the HTML form that appears in the browser -->
          <form class="collapsible-body" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            Warble: <input type="text" name="country">
            <div class='input-field'>
              <input type="submit" name="submit" class="waves-effect waves-light btn">
            </div>
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
    $search = $_POST['search'];
    // open connection
    $connection = mysqli_connect($host, $username, $password) or die ("Unable to connect!");
    // select database
    mysqli_select_db($connection, $dbname) or die ("Unable to select database!");
    // create query
    if ($search != ""){
        $query = "SELECT * FROM symbols WHERE tweet_contents LIKE '%$search%' ORDER BY id DESC";
    }
    else{
      $query = "SELECT * FROM symbols ORDER BY id DESC";
    }
    // execute query
    $result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());
    // see if any rows were returned
    if (mysqli_num_rows($result) > 0) {
        // print them one after another
        echo "<table cellpadding=50 border=1>";
        while($row = mysqli_fetch_row($result)) {
          //echo "</table>";
          $comments_query = "SELECT * FROM comments WHERE join_id = $row[0]";
          //echo "$row[0]";
          echo "<div class='row'>";
          echo  "<div class='col s12 m12'>";
          echo    "<div class='card-panel teal accent-4'>";
          echo      "<span class='white-text'><h5>@$row[2]:</h5> $row[1] </span>";
          echo    "<div class='right-align'>";
          echo      "<span class='white-text right-align'>$row[3]</span>";
          // likes and button below
          $likes_query = "SELECT Count(join_id) AS num_ls FROM likes WHERE join_id=$row[0]";
          $num_likes = mysqli_query($connection,$likes_query);
          $likes = mysqli_fetch_row($num_likes);
          $likes = $likes[0];

          echo      "<a href=".$_SERVER['PHP_SELF']."?like=".$row[0]." class='btn-flat btn-small waves-effect waves-light teal accent-4 white-text'><i class='material-icons white-text'>thumb_up</i>$likes</a>";
          // delete button
          if ($row[2] == $arr[1]){
            echo "<a href=".$_SERVER['PHP_SELF']."?id=".$row[0]." class='btn-flat btn-small waves-effect waves-light teal accent-4'><i class='material-icons white-text'>delete</i></a>";
          }
          echo    "</div>";
          echo    "</div>";
          echo  "</div>";
          echo  "<ul class='collapsible s12 m12' data-collapsible='accordion'>";
          echo    "<li>";
          echo      "<div class='collapsible-header'><i class='material-icons'>comment</i>Comments</div>";
          echo      "<div class='collapsible-body'><span>";
          // comments below
          $result_comments = mysqli_query($connection,$comments_query);
          // list out comments in a loop
            while($com = mysqli_fetch_row($result_comments)) {
              if ($com[1] == $row[0]){
                $user = htmlspecialchars($com[3]);
                $content = htmlspecialchars($com[2]);
                echo      "<span class='text'> $user: $content <br>";
                echo      "</span>";
                }
          }
          // form to post comment
          echo      "<form class='collapsible-body'  method='post' id='$row[0]'>";
          echo         "<input type='text' name='$row[0]'>";
          echo         "<div class='input-field'>";
          // below gets the join id to attatch the comment to the right post
          $comment = $_POST[$row[0]];
          if ($comment != "") {
              echo "<script>console.log('comment-ran-past')</script>";
              //echo '$arr[0], $arr[1], $arr[2]';
              $query = "INSERT INTO comments (comment, user, join_id) VALUES ('$comment','$arr[1]', '$row[0]')";
              //$query .= "INSERT INTO symbols (username) VALUES ('$arr[1]')";
              //$query2 = "INSERT INTO symbols (username) VALUES ('$arr[1]')";
              // run the query
              $result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());
              //$result2 = mysqli_query($connection,$query2) or die ("Error in query: $query2. ".mysql_error());
              // refresh the page to show new update
              echo "<meta http-equiv='refresh' content='0'>";
          }
          // add end tags to the form
          echo         "</div>";
          echo      "</form>";
          echo      "</span></div>";
          echo    "</li>";
          echo  "</ul>";
          echo "</div>";
        }
    } else {
        // print status message
        echo "<script> Materialize.toast('Looks like there isnt anything here', 6500); // 6500 is the duration of the toast </script>";
    }
    // free result set memory
    mysqli_free_result($connection,$result);
    // set variable values to HTML form inputs
    // use entites to make them XSS proof
    $country = $_POST['country'];
    $country = htmlspecialchars($country);

    $animal = $_POST['animal'];
    $animal = htmlspecialchars($animal);

    $comment = $_POST['comment'];
    $comment = htmlspecialchars($comment);

      // check to see if user has entered anything
    if ($country != "") {
      // build SQL query
      date_default_timezone_set("America/New_York");
      $timedate = date("F j, Y")." at ".date("g:i a");
      $query = "INSERT INTO symbols (tweet_contents, username, timedate) VALUES ('$country','$arr[1]', '$timedate')";
      //$query .= "INSERT INTO symbols (username) VALUES ('$arr[1]')";
      //$query2 = "INSERT INTO symbols (username) VALUES ('$arr[1]')";
      // run the query
      $result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());
      //$result2 = mysqli_query($connection,$query2) or die ("Error in query: $query2. ".mysql_error());
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
    // if ($comment != "") {
    //     echo "<script>console.log('comment-ran-past')</script>";
    //     //echo '$arr[0], $arr[1], $arr[2]';
    //     $query = "INSERT INTO comments (comment, user, join_id) VALUES ('$comment','$arr[1]', '$jid')";
    //     //$query .= "INSERT INTO symbols (username) VALUES ('$arr[1]')";
    //     //$query2 = "INSERT INTO symbols (username) VALUES ('$arr[1]')";
    //     // run the query
    //     $result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());
    //     //$result2 = mysqli_query($connection,$query2) or die ("Error in query: $query2. ".mysql_error());
    //     // refresh the page to show new update
    //     echo "<meta http-equiv='refresh' content='0'>";
    // }
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

    if (isset($_GET['like'])) {
      $id = $_GET['like'];

      // make only one like per person
      $comment_query = "SELECT Count(join_id) AS num_ls FROM likes WHERE join_id=$id AND profile = '$arr[1]'";
      $num_likes = mysqli_query($connection,$comment_query);
      $likes = mysqli_fetch_row($num_likes);
      $likes = $likes[0];


      if ($likes == 0){
        // create query to delete record
        echo $_SERVER['PHP_SELF'];
        $query = "INSERT INTO likes (join_id, profile) VALUES ('$id', '$arr[1]')";
        // run the query
        $result = mysqli_query($connection,$query) or die ("Error in query: $query. ".mysql_error());
        // reset the url to remove id $_GET variable
        $location = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.$location.'">';
        exit;
      }
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
