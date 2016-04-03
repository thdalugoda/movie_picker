<?php

ob_start();
class database
{
private $func;


function __construct($pdo)
{
$this->pdo=$pdo; 
}



function addmovie(){
$name = $_POST['name'];
$category = $_POST['category'];
$desc = $_POST['description'];

$direct = $_POST['director'];		
$music = $_POST['music'];
$lang = $_POST['language'];
$year = $_POST['year'];
$imdb = $_POST['imdb'];
$query= $this->pdo->prepare("insert into movie_list (movie_name,category,description,director,music,language,year,imdb,status) 
values ('$name','$category','$desc','$direct','$music','$lang','$year','$imdb',0)");
$query->execute(); 
$pid= $this->pdo->lastInsertId();
$newname = "$pid.jpg";
move_uploaded_file($_FILES['movie_img']['tmp_name'], "../../app/product_img/$newname");
print '<div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4>	<i class="icon fa fa-check"></i> Success!</h4>
                    New Movie was added successfully.
                  </div>';
}


function newuser()
{
if(isset($_POST['s_name']))
{
$un=$_POST['s_name'];
$pw=$_POST['s_pass'];
$query= $this->pdo->prepare("insert into users(username, password) values ('$un','$pw')");
$query->execute();
print "<div class='alert alert-primary alert-white-alt rounded'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
								<div class='icon'><i class='fa fa-check'></i></div>
								<strong>Success! </strong>Thank You For your Registration</div>";

}

}


function ai()
{

$user=$_SESSION['login_username'];
$query=$this->pdo->prepare("select * from sel_movie where user='$user' ");
$query->execute();
while ($res=$query->fetch(PDO::FETCH_OBJ))
{

$movie=$res->movie_id;
$query2=$this->pdo->prepare("select * from sel_movie where movie_id=$res->movie_id AND user!='user' ");
$query2->execute();
while ($res2=$query2->fetch(PDO::FETCH_OBJ))
{

$movie2=$res2->movie_id;
$user2=$res2->user;
$query3=$this->pdo->prepare("select * from sel_movie where movie_id!=$movie2 AND user='$user2'  ");
$query3->execute();
while ($res3=$query3->fetch(PDO::FETCH_OBJ))
{

$rec_movie= $res3->movie_id;
$query4=$this->pdo->prepare("select * from movie_list where movie_id=$rec_movie group by  movie_id ");
$query4->execute();

while ($res4=$query4->fetch(PDO::FETCH_OBJ))
{

print "<tr>
	<td><img width=75 src='../../app/product_img/$res4->movie_id.jpg'><br>
                        <a class='label label-default' href='allmovies.php?update=$res->movie_id'></td>
                        <td>$res4->movie_name</td>
<td>$res4->category</td>
<td>$res4->description</td>
<td>$res4->director</td>
<td>$res4->language</td>
<td>$res4->year</td>
<td>$res4->imdb</td>
<td><a class='fa fa-thumbs-up' href='../tables/recommend.php?like=$res4->movie_id'><span class='glyphicon-class'> Like </span></a><a class='fa fa-thumbs-down' href='../tables/recommend.php?dislike=$res4->movie_id'><span class='glyphicon-class'> Dislike </span></a><a class='fa fa-heart' href=''><span class='glyphicon-class'> $res4->status Likes </span></a> </td></tr>";
}
}
}
}
}





function likemovie()
{
 if(isset($_GET['like']))
{
$like=$_GET['like'];
$query=$this->pdo->prepare("select status from movie_list where movie_id=$like");
$query->execute();
$res=$query->fetch(PDO::FETCH_OBJ);
$status=$res->status;
$newstatus=$status+1;
$query1=$this->pdo->prepare("update movie_list SET Status=$newstatus where movie_id=$like");
$query1->execute();
}
}

function dislikemovie()
{
 if(isset($_GET['dislike']))
{
$user=$_SESSION['login_username'];
$dislike=$_GET['dislike'];
$query=$this->pdo->prepare("select status from movie_list where movie_id=$dislike");
$query->execute();

$query1=$this->pdo->prepare("insert into dislike values(0,'$user',$dislike)");
$query1->execute();

$res=$query->fetch(PDO::FETCH_OBJ);
$status=$res->status;
$newstatus=$status-1;
$query1=$this->pdo->prepare("update movie_list SET Status=$newstatus where movie_id=$dislike");
$query1->execute();
}
}


function mymovies()
{
$user=$_SESSION['login_username'];
$query1=$this->pdo->prepare("select * from sel_movie where user='$user'");
$query1->execute();
while ($res=$query1->fetch(PDO::FETCH_OBJ))
	{
$query2=$this->pdo->prepare("select * from movie_list where movie_id='$res->movie_id'");
$query2->execute();
while ($res2=$query2->fetch(PDO::FETCH_OBJ))
	{
print "<tr>
	<td><img width=75 src='../../app/product_img/$res2->movie_id.jpg'><br>
                        <a class='label label-default' href='allmovies.php?update=$res2->movie_id'></td>
                        <td>$res2->movie_name</td>
<td>$res2->category</td>
<td>$res2->description</td>
<td>$res2->director</td>
<td>$res2->language</td>
<td>$res2->year</td>
<td>$res2->imdb</td>
<td><a class='label label-default' href='../tables/mymovies.php?remove=$res2->movie_id'><span class='glyphicon-class'>Remove Movie</span></a> </td></tr>

";
}
}
}

function removeMovie()
{
$todelete = $_GET['remove'];
$query=$this->pdo->prepare("delete from movie_list where movie_id=$todelete");
$query->execute();

print '<div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4>	<i class="icon fa fa-check"></i> Success!</h4>
                    Movie was Removed.
                  </div>';
}


function allmovie()
{
$user=$_SESSION['login_username'];
$query2=$this->pdo->prepare("select * from dislike where user_id='$user'");
$query2->execute();
$res2=$query2->fetch(PDO::FETCH_OBJ);
$dislike=$res2->movie_id;



$query=$this->pdo->prepare("select * from movie_list where status>-11 OR movie_id!=$dislike");
$query1=$this->pdo->prepare("select * from sel_movie where user='$user'");

$query->execute();
$query1->execute();

$count=$query1->rowCount();

//if($res->movie_id!=$dislike)
//{

while ($res=$query->fetch(PDO::FETCH_OBJ))
	{


if ($count >=5){
	print "<tr>
	<td><img width=75 src='../../app/product_img/$res->movie_id.jpg'><br>
                        <a class='label label-default' href='allmovies.php?update=$res->movie_id'></td>
                        <td>$res->movie_name</td>
<td>$res->category</td>
<td>$res->description</td>
<td>$res->director</td>
<td>$res->language</td>
<td>$res->year</td>
<td>$res->imdb</td>
";
					
		print "
		<td><a>Limit Reached (Cannot Select more than 5 movies)</a> 
					</td></tr>";
	}

else 
{

print "<tr>
	<td><img width=75 src='../../app/product_img/$res->movie_id.jpg'><br>
                        <a class='label label-default' href='allmovies.php?update=$res->movie_id'></td>
                        <td>$res->movie_name</td>
<td>$res->category</td>
<td>$res->description</td>
<td>$res->director</td>
<td>$res->language</td>
<td>$res->year</td>
<td>$res->imdb</td>
";
					
		print "
		<td><a class='label label-default' href='../tables/movielist.php?vid=$res->movie_id'><span class='glyphicon-class'>Select Movie</span></a> 
							
						</td>
                      </tr>";
	
}	}


}

function selectmovie()
{
$url_id = $_GET['vid'];
$user=$_SESSION['login_username'];
$query1=$this->pdo->prepare("select * from sel_movie where user='$user' AND movie_id=$url_id");
$query=$this->pdo->prepare("select * from movie_list where movie_id=$url_id");
$query->execute();
$query1->execute();
$row = mysql_fetch_array($query1);
$total = $row[0];
if($total>0)
{
print 
"<div class='alert alert-warning alert-white rounded'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
								<div class='icon'><i class='fa fa-warning'></i> Alert!</div>
								<strong></strong> Do you want to Select the movie of ID $url_id  ? <strong><a href='../tables/movielist.php?yes=$url_id'> YES</a> | <a href='../tables/movielist.php'>NO</a> </strong>
							 </div>";
}
else
{
print 
"<div class='alert alert-warning alert-white rounded'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
								<div class='icon'><i class='fa fa-warning'></i> Alert!</div>
								<strong></strong>Do you want to Select the movie of ID $url_id  ? <strong><a href='../tables/movielist.php?yes=$url_id'> YES</a> | <a href='../tables/movielist.php'>NO</a> </strong>
							 </div>";
}
}


function yesselectmovie()
{
$user=$_SESSION['login_username'];
$toselect = $_GET['yes'];

$query1=$this->pdo->prepare("select * from sel_movie where movie_id=$toselect AND user='$user'");
$query1->execute();
$count=$query1->rowCount();
if ($count >=1){
print '<div class="alert alert-warning alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4>	<i class="icon fa fa-hourglass-start"></i> Warning!</h4>
                    You have already Selected this movie. Please Try Again.
                  </div>';
}

else
{
$query=$this->pdo->prepare("insert into sel_movie values('$user',$toselect)");
$query->execute();

print '<div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4>	<i class="icon fa fa-check"></i> Success!</h4>
                    Movie was added.
                  </div>';

}

}
function allmovies()
{
$query=$this->pdo->prepare("select * from movie_list");
$query->execute();

while ($res=$query->fetch(PDO::FETCH_OBJ))
	{
	print "<tr>
	<td><img width=75 src='../../app/product_img/$res->movie_id.jpg'><br>
                        <a class='label label-default' href='allmovies.php?update=$res->movie_id'><span class='glyphicon-class'>Change Image</span></a></td>
                        <td>$res->movie_name</td><td>$res->status";
						
		print "
		</td><td><a class='label label-default' href='../forms/edit_details.php?vid=$res->movie_id'><span class='glyphicon-class'>Edit Details</span></a> 
							<a class='label label-default' href='allmovies.php?del=$res->movie_id'><span class='glyphicon-class'>Delete</span></a> 
						</td>
                      </tr>";
	}
}



function editmovieDetails(){
$vid = $_GET['vid']	;
$query=$this->pdo->prepare("select * from movie_list where movie_id = $vid ");
$query->execute();
while ($det=$query->fetch(PDO::FETCH_OBJ))
					{
						
						
						print '<div class="form-group " >
                      <label>Movie Name</label>
                      <input type="hidden" value="'.$vid.'" name="vid" class="form-control">
                      <input type="text" value="'.$det->movie_name.'" name="name" class="form-control">
                    </div>
					<div class="form-group">	
                      <label>Category</label>
                      <input type="text" value="'.$det->category.'" name="cat" class="form-control">
                    </div>					 
					<!--<h3 class="box-title">Specifications </h3>
					<table class="table table-bordered text-center">
                    <tbody><tr>
                      <th>-->
					  <div class="form-group ">	
                      <label>Description</label>
                      <input type="text" value="'.$det->description.'" name="desc" class="form-control">
                    </div>
					<div class="form-group ">	
                      <label>Director</label>
                      <input type="text" value="'.$det->director.'" name="direct" class="form-control">
                    </div>
					<!--  <div class="form-group ">	
                      <label>Music</label>
                      <input type="text" value="'.$det->music.'" name="music" class="form-control">
                    </div>-->
					<div class="form-group ">	
                      <label>Language</label>
                       <input type="text" value="'.$det->language.'" name="lang" class="form-control">
                    </div>
					
					  <div class="form-group ">	
                      <label>Year</label>
                      <input type="text" value="'.$det->year.'" name="year" class="form-control">
                    </div>
					  <div class="form-group ">	
                      <label>IMDB Link</label>
                      <input type="text" value="'.$det->imdb.'" name="imdb" class="form-control">
                    </div>';
					
					}
					
}
function updatemovieDetails(){
 $model=$_POST['name'];
 $p_title=$_POST['cat'];
 $disp=$_POST['desc'];
 $power=$_POST['direct'];
 $trans=$_POST['music'];
 $m_grades=$_POST['lang'];
 $usp=$_POST['year'];
 $price=$_POST['imdb'];
 $vid=$_POST['vid'];

$query=$this->pdo->prepare("UPDATE movie_list SET movie_name='$model', category='$p_title' ,description='$disp',director='$power',music='$trans',language='$m_grades',year='$usp',imdb='$price' WHERE movie_id ='$vid'");
$query->execute();
print '<div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4>	<i class="icon fa fa-check"></i> Success!</h4>
                    Movie details updated successfully.
                  </div>';
}
function editProductImage(){
$vid = $_GET['update'];

print '<section class="content">
         <div class="row">
		   <div class="col-md-12">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Replace new image</h3>
                </div><!-- /.box-header -->
				<!-- form start -->
                <form action=""  method="post" role="form" enctype="multipart/form-data">
                  <div class="box-body">
					<div class="form-group" >
                      <label>Movie Poster</label>
                      <input type="file" name="product_img" class="form-control">
                    </div>
                    <input type="hidden" name="vid" value="'.$vid.'" class="form-control">
					</div>
                  <div class="box-footer ">
                    <button type="submit" name="update_img" class="btn btn-primary">UPDATE</button>
                  </div>
                </form>
              </div><!-- /.box -->
              <!-- Form Element sizes -->
              <!-- Input addon -->
            </div><!--/.col (left) -->
          </div><!-- /.row -->
        </section>';				
}
function updateProductImage(){

 $vid=$_POST['vid'];
 $newname = "$vid.jpg";
move_uploaded_file($_FILES['product_img']['tmp_name'], "../../app/product_img/$newname");	

header('location:allmovies.php?x=1');

}


function adminLogin()
{
	$username= $_POST['username'];
	$password= $_POST['password'];
	$query=$this->pdo->prepare("select * from users where username='$username' and password= '$password' LIMIT 1");
	$query->execute();
	$count =$query->rowCount();
	if ($count == 1)
	{
		while ($school=$query->fetch(PDO::FETCH_OBJ))
	{
		session_start();
		$_SESSION['login_username']=$school->username;
		header("Location:index.php");
		exit();
	}
	
	}
	else
	{
		print '<div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-ban"></i> Alert!</h4> 
                    Incorrect username or password
                  </div>';
	}
}
function adminLogout()
{
session_start();
session_destroy();
header('Location:login.php');
exit();
}



}
?>