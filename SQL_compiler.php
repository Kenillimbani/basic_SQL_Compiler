<?php
session_start();
?>
<html>
<head>
<title>SQL_Compiler</title>
</head>
<body>
<form action="kenz_sqlcompiler.php" method="post" >

<?php
if(isset($_POST['go']))
	$_SESSION['db_name']=$_POST['dbname'];
if(!isset($_SESSION['db_name']))
{echo'Enter Database Name: <input type="text" name="dbname"/>
<input type="submit" name="go" value="Go"/>';
}
?>
<?php
if(isset($_SESSION['db_name']))
{
echo '<h3>Run SQL query/queries on server Kenz : </h3>

<textarea name="query" rows="10" cols="80" ></textarea>
<br/>
<input type="submit" name="submit" value="Run"/>
</form>';
}?>
<?php

	if(isset($_POST['submit']))
	{
		$connect=mysqli_connect('localhost','root','',"$_SESSION[db_name]");
		$Author_name='Kenil_limbani';
		
		//echo "<h4> Output of Query </h4>";
		
		// For Know Table Name & Coloums ...
		
		$split_string=explode(" ",$_POST['query']);
		
		for($i=0;$i<count($split_string);$i=$i+1)
		{
			if($split_string[$i]=='FROM' || $split_string[$i]=='INTO' || $split_string[$i]=='TABLE' || $split_string[$i]=='UPDATE')
			{
				$table_name=$split_string[$i+1];
				break;
			}
		}
		
		// Check if there is any other character like ;(semicolon)
		
		if(ctype_upper($table_name) || ctype_lower($table_name)) // Return false if there is any other than [a-z] upper and lower
		{
			$split_tablename=explode("(",$table_name);
			echo "<h3>Output of Table: ".$split_tablename[0]."</h3>";
		}
		
		// split table name BY WHERE as it is only retrival from database ... it may be ( in case of Into
		else
		{
			if($split_string[0]=='INSERT' || $split_string[0]=='CREATE')
			{
				$split_tablename=explode("VALUES",$table_name);
				if(!ctype_upper($split_tablename[0]) || !ctype_lower($split_tablename[0]))
					$split_tablename=explode("(",$split_tablename[0]);
				
			}			
			else if($split_string[0]=='DELETE' || $split_string[0]=='SELECT')
			{
				$split_tablename=explode("WHERE",$table_name);
				//echo $split_tablename[0];
				if(!ctype_upper($split_tablename[0]) || !ctype_lower($split_tablename[0]))
					$split_tablename=explode("(",$split_tablename[0]);
			}		
			else if($split_string[0]=='UPDATE')
			{
				$split_tablename=explode("SET",$table_name);
				
			}
			else
			{
				$split_tablename=explode(";",$table_name);
			}
			echo "<h3>Output of Table: ".$split_tablename[0]."</h3>";
		}
		
		
		
		// RUN DIFFERENT QUERIES ...
		if($split_string[0]=='INSERT' || $split_string[0]=='CREATE' || $split_string[0]=='DELETE' || $split_string[0]=='UPDATE')
		{
			mysqli_query($connect,"$_POST[query]");
			$record=mysqli_query($connect,"SELECT * FROM $split_tablename[0]");
		}
		else if($split_string[0]=='SELECT')
		{
			$record=mysqli_query($connect,"$_POST[query]");
		}
		else if($split_string[0]=='DROP')
		{
			mysqli_query($connect,"$_POST[query]");
			echo "<h2>TABLE IS DELETED</h2>";
		}
		else
		{
			mysqli_query($connect,"$_POST[query]");
			$record=mysqli_query($connect,"SELECT * FROM $split_tablename[0]");
		}
		
		// FOR DISPLAY TABLES ...
		// retrive coloums name ...
		
		$table=mysqli_query($connect,"SHOW COLUMNS FROM $split_tablename[0]");
		
		if(!$table)// table not exist ...
		echo "<h2>TABLE is NOT Exist !! Please Make it First</h2>";
		
		else // If Table exist than all this things .... 
		{
			
		$table_coloums=array();
		$i=0;
		while($coloums=mysqli_fetch_assoc($table))
		{
			$table_coloums[$i]=$coloums['Field'];
			//echo $table_coloums[$i];
			$i=$i+1;
		}
		$j=$i; // length of coloums ...
		
		echo '<table width="300" cellspacing="1" cellpadding="2" border="1">

		<tr>';
		
		for($i=0;$i<$j;$i=$i+1)
		{
			echo "<th>".$table_coloums[$i]."</th>"; // all coloums .. 
		}
		echo '</tr>';
		
				if($record==TRUE) // if Data is available on $record ... mean search success
				{
					
					while($data=mysqli_fetch_assoc($record))
					{
						echo"<tr>";
						
						for($i=0;$i<$j;$i=$i+1)
						{
							if(isset($data[$table_coloums[$i]]))
								echo"<td>".$data[$table_coloums[$i]]."</td>"; // to display if available 
							else
								echo"<td> </td>"; // Put blank if Data not available 
						}
						
						echo "</tr>";
					}
					
				}
			
				else
					echo "<tr><td colspan='4'><center>NO DATA FOUND !!</center></td></tr>";
			
			echo '</table>';
		}
		
	}
	?>
	</table>
	</body>
	</html>
