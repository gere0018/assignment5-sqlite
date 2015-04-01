<?php
//***********************************************************************************
                //PART ONE:Saving Query Results to an XML File
//***********************************************************************************

// Connect to the database by creating a new PDO object
    $db_host = "localhost";
    $db_name = "MAD9023";
    $db_user = "root";
    $db_password = "root";

    $mysqlPdo = new PDO("mysql:host=$db_host;dbname=$db_name",$db_user,$db_password);
//1 Query the players table to select all of the rows of data within it.
//******************************************************************

    //mysql_result will have two arrays, numeric and associative
    $mysql_result = $mysqlPdo->query(" SELECT * FROM players; ");

    //Create a variable that contains all the results only in an associative array format.
    $resultArray = $mysql_result->fetchAll(PDO::FETCH_ASSOC);
    //var_dump($resultArray);

//2 Use foreach loops to create a string variable formatted as XML data that contains all of the data from the query.
//******************************************************************************************

      $xmlString = "<?xml version='1.0' standalone='yes'?>\n";
      $xmlString .= "<players>\n";
      // Loop through the results
      foreach($resultArray as $row)
      {
        //var_dump($row);
        $xmlString .= "<player>\n";
        // Loop through each record to print out each $key as the name of an XML tag
        foreach($row as $key => $value)
        {
          $xmlString .=   "<". $key . ">"   . $value .   "</". $key . ">\n";
        }
        $xmlString .= "</player>\n";
      }

      $xmlString .= "</players>\n";

//3 Load the XML string variable into a SimpleXMLElement object
//******************************************************************
$players = new SimpleXMLElement($xmlString);

//4 Loop through the XML data randomly updating some of the values
//******************************************************************
 //var_dump($players);
$output = "<ul>";
foreach( $players->player as $player)
{
    if($player->id ==1){
        $player->name = "Ghadab";
        $player->color = "CC3399";
    }
    if($player->id ==3){
        $player->name = "Elias";
        $player->color ="99FF66";
    }
//5 Output the XML data to the user
//************************************

    // The ->getName() gets the name of the node that this value lives in
    $output .= "<li>". $player->id->getName() . ": (" . $player->id . ")<br/>\n";
    $output .= $player->name->getName() . ": " . $player->name . "<br/>\n";
    $output .= $player->locX->getName() . ": " . $player->locX . "<br/>";
    $output .= $player->locY->getName() . ": " . $player->locY . "<br/>";
    $output .= $player->color->getName() . ": " . $player->color . "<br/>\n</li></ul>";

}


//6 Save the SimpleXMLElement object data to an XML file
//*******************************************************

$players->asXML('players.xml');

//***********************************************************************************
                                //PART TWO:
//***********************************************************************************

//1 Select everything from the MySQL table called players:
//************************************************************
       // done above.

//2 - Save the data to an SQLite database:
//*****************************************

    // Create a connection to SQLite database file.
    $sqlitePdo = new PDO('sqlite:players.sqlite3');

    //Send a query to create a new table
    $sqlitePdo->exec("CREATE TABLE players(
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        name TEXT,
                        locX INTEGER,
                        locY INTEGER,
                        color TEXT)");


    if( $mysql_result )
    {
        $sqlIDs = array();
        while( $mysqlRow = $mysql_result->fetch(PDO::FETCH_ASSOC) )
        {
            $insertDataQuery = "INSERT INTO `players`
                            (`id`, `name`, `locX`,
                            `locY`, `color`)
                            VALUES('" .
                             $mysqlRow['id'] . "', '" .
                             $mysqlRow['name'] . "', " .
                             $mysqlRow['locX'] . ", " .
                             $mysqlRow['locY'] . ", '" .
                             $mysqlRow['color'] . "');";
            // Add each record into the new table
            $sqlitePdo->exec( $insertDataQuery);
            //Create an array to save all the MYSQL table ids.
            array_push($sqlIDs, $mysqlRow['id']);

        }
        //var_dump($sqlIDs);

    }

//3 - Delete 2 records from the SQLite table and vacuum after the delete.
//************************************************************************

    $delete2DataQuery = " DELETE FROM players WHERE id IN(4,5);";
    $sqlitePdo->exec( $delete2DataQuery);
    $sqlitePdo->query("VACUUM");

//4 - Insert 3 new records into the SQLite database.
//**************************************************

    $insertDataQuery1 = "INSERT INTO players (`id`, `name`, `locX`,
                            `locY`, `color`)
                            VALUES(11, 'Maria', 56,
                                   78, '9933CC');";

    $insertDataQuery2 = "INSERT INTO players (`id`, `name`, `locX`,
                            `locY`, `color`)
                            VALUES(12, 'Steve', 165,
                                   32, '680000 ');";
    $insertDataQuery3 = "INSERT INTO players (`id`, `name`, `locX`,
                            `locY`, `color`)
                            VALUES(13, 'Carla', 64,
                                  122, '000000 ');";

    $sqlitePdo->exec($insertDataQuery1);
    $sqlitePdo->exec($insertDataQuery2);
    $sqlitePdo->exec($insertDataQuery3);

//5 - Update the MySQL database table with the data from the SQLite table.
//*************************************************************************

    $sqlite_result = $sqlitePdo->query("SELECT * FROM players;");
     if( $mysql_result && $sqlite_result )
    {

       for($i = 0; $i< sizeof($sqlIDs); $i++){
           $ID = false;
           echo "FOR LOOP/////";
           foreach($sqlite_result as $sqliterow)
            {
               echo "SQLite ID";
               echo $sqliterow['id'];
               echo "/SQL ID";
               echo $sqlIDs[$i];
               echo"-------";
               if($sqlIDs[$i] == $sqliterow['id'])
               {
                   $ID = true;
                   echo "caca";
               }
           }

           if(!$ID){
               $deleteQuery = "DELETE FROM players WHERE id = ". $sqlIDs[$i];
               //$mysqlPdo->query($deleteQuery);
               echo $deleteQuery;
           }




        }

//            foreach($sqlite_result as $row)
//            {
//                echo . "<br>";
//
//            }
//         }
//    }
//



    }

?>

<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 5</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <header>
        <h1>Assignment 5</h1>
    </header>
    <div class = "pageContent">
        <?php echo $output; ?>


    </div>
    <footer>
        <p>Nehmat Gerige 2015</p>
    </footer>


</body>
</html>
