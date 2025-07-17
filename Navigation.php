<?php

require_once 'config/database.php';

// ตรวจสอบการล็อกอิน
checkLogin();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบรายงานการทำงานแผนกไอที</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top custom-navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><i class="fa fa-desktop"></i> ระบบรายงานการทำงานแผนกไอที</a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
                <ul class="nav navbar-nav">
                    <li class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">
                          <a href="index.php"><i class="fa fa-dashboard"></i> แดชบอร์ด</a>
                    </li>
                    <li class="<?= ($currentPage == 'ITstaff.php') ? 'active' : '' ?>">
                         <a href="ITstaff.php"><i class="fa fa-users"></i> เจ้าหน้าที่ไอที</a>
                    </li>
                    <li class="<?= ($currentPage == 'work_inprogress.php') ? 'active' : '' ?>">
                         <a href="work_inprogress.php"><i class="fa fa-tasks"></i> งานที่ดำเนินการ</a>
                    </li>
                    
                    <li><a href="#reports" data-toggle="tab"><i class="fa fa-file-text"></i> รายงานรายวัน</a></li>
                   
                    <li class="<?= ($currentPage == 'Create_reports.ph') ? 'active' : '' ?>">
                         <a href="Create_reports.php"><i class="fa fa-file-text"></i> รายงานรายวัน</a>
                    </li>

                    <li class="<?= ($currentPage == 'index.php' && strpos($_SERVER['REQUEST_URI'], 'requests') !== false) ? 'active' : '' ?>">
                         <a href="requests/index.php"><i class="fa fa-inbox"></i> คำขอบริการ</a>
                    </li>
                </ul>


                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i> <?php echo htmlspecialchars($_SESSION['full_name']); ?> <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="#"><i class="fa fa-user"></i> โปรไฟล์</a></li>
                            <li><a href="#"><i class="fa fa-cog"></i> ตั้งค่า</a></li>
                            <li class="divider"></li>
                            <li><a href="logout.php"><i class="fa fa-sign-out"></i> ออกจากระบบ</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
</body>
</html>