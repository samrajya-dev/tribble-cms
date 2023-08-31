<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
  { 
header('location:index.php');
}
else{
   
if(isset($_POST['submit']))
{
$title=$_POST['title'];
$brief=$_POST['brief'];

$sql="INSERT INTO courses(Title, Brief, status) VALUES(:title,:brief,'1')";

$query = $dbh->prepare($sql);
$query->bindParam(':title',$title,PDO::PARAM_STR);
$query->bindParam(':brief',$brief,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();

  if($lastInsertId) {
    echo '"
    <script>
      alert("Landing Page Service Added Successfully");
    </script>"';
    //window.location.href="manage-camping.php?page=1";
  }
  else {
   echo '"
   <script>
    alert("Something went wrong, please try again!");
   </script>"'; 
 }
}


if(isset($_GET['statusid']))
{
$statusid=$_GET['statusid'];
$sql="UPDATE courses SET status='1' WHERE Id=:statusid";
$query = $dbh->prepare($sql);
$query -> bindParam(':statusid',$statusid, PDO::PARAM_STR);
$query -> execute();
}
if(isset($_GET['statusuid']))
{
$statusuid=$_GET['statusuid'];
$sql="UPDATE courses SET status='0' WHERE Id=:statusuid";
$query = $dbh->prepare($sql);
$query -> bindParam(':statusuid',$statusuid, PDO::PARAM_STR);
$query -> execute();
}
if(isset($_GET['del']))
{
$id=$_GET['del'];
$sql = "DELETE FROM courses WHERE Id=:id";
$query = $dbh->prepare($sql);
$query -> bindParam(':id',$id, PDO::PARAM_STR);
$query -> execute();
$lastInsertId=$query -> execute();
  if($lastInsertId) {
    echo '"
    <script>
      alert("Service Deleted successfully");
    </script>"';
  }
  else {
   echo '"
   <script>
    alert("Something went wrong, please try again!");
   </script>"'; 
 }
} 
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>CMS | Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="icon" type="image/png" href="../assets/logo/vishwa_connectome_logo.png">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/esm/popper.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <script>
      if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
      }
    </script>
    <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
</head>
<body>
  <div class="page-wrapper chiller-theme toggled">
    <?php include('includes/sidebar.php');?>

    <main class="page-content">

      <div class="container">
        <h2>Add Courses</h2>
        <hr>
        <form method="post" enctype="multipart/form-data">

            <div class="">
              <label for="title" class="form-label">Title</label>
              <input class="form-control" type="text" name="title" placeholder="Title" id="title">
            </div>
            <div class="">
              <label for="brief" class="form-label">Brief </label>
              <textarea class="form-control" type="text" name="brief" placeholder="Brief" id="brief"></textarea>
            </div>
            <div class="col-md-3 pt-4 mt-1">
              <input class="btn bg-warning" type="submit" name="submit" value="Submit">
            </div>
          </div>
        </form>


        <h2 class="pt-5">Courses</h2>
        <hr>


        <div class="row mb-5 g-3 p-5">

          <?php 
          $results_per_page = 20;
          $sql = "SELECT * from courses";
          $query = $dbh -> prepare($sql);
          $query->execute();
          $results=$query->fetchAll(PDO::FETCH_OBJ);
          $cnt=1;

          $number_of_rows = $query->rowCount();
          $number_of_page = ceil ($number_of_rows / $results_per_page);  

          if (!isset ($_GET['page']) ) {
            $page = 1;  
          } else {  
            $page = $_GET['page'];  
          }
          $page_first_result = ($page-1) * $results_per_page;
          $sql = "SELECT * FROM courses LIMIT " . $page_first_result . ',' . $results_per_page;
          $query = $dbh -> prepare($sql);
          $query->execute();
          $results=$query->fetchAll(PDO::FETCH_OBJ);

          if($query->rowCount() > 0)
          {
          foreach($results as $result)
          { ?>

          <div class="card text-center border-success m-3 p-1" style="width: 16rem;">
          <?php
            $img = ($result->Title);
            if ($img == NULL) { ?>
              <?php echo "";?>
              <?php } else {?>
                <?php echo $result->Title;?>
              <?php }?>
                <div class="card-footer text-center bg-transparent border-success">
                  <p><?php echo $result->Brief;?></p>
                  <a class="btn btn-danger" href="courses.php?page=<?php echo $_GET['page'];?>&del=<?php echo $result->Id;?>" onclick="return confirm('Are you sure you want to delete?');">Delete</a>

                  <?php
                  $status = ($result->status);
                  if ($status == '0') { ?>
                  <a class="btn" href="courses.php?page=<?php echo $_GET['page'];?>&statusid=<?php echo $result->Id;?>" style="color:white; background: red;">
                    Off
                    <i class="bi bi-toggle2-off"></i>
                  </a>
                  <?php }else {?>
                  <a class="btn" href="courses.php?page=<?php echo $_GET['page'];?>&statusuid=<?php echo $result->Id;?>" style="color:white; background: green;">
                    On
                    <i class="bi bi-toggle2-on"></i>
                  </a>
                  <?php } ?>
                </div>
          </div>

          <?php }} ?>

          <nav aria-label="Page navigation">
            <ul class="pagination justify-content-end pt-3">

              <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                <a class="page-link" href="courses.php?page=<?php echo $page-1; ?>">Previous</a>
              </li>

              <?php for($i = 1; $i <= $number_of_page; $i++ ): ?>
              <li class="page-item <?php if($page == $i) {echo 'active'; } ?>">
                <a class="page-link" href="courses.php?page=<?= $i; ?>"> <?= $i; ?> </a>
              </li>
              <?php endfor; ?>

              <?php $pageid=intval($_GET['page']);?>
              <li class="page-item <?php if($page >= $number_of_page) { echo 'disabled'; } ?>">
                <a class="page-link" href="courses.php?page=<?php echo $pageid+1; ?>">Next</a>
              </li>
            </ul>
          </nav>
        </div>




      </div>

    </main>
  </div>
</body>
</html>
<?php } ?>
