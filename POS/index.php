<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location:./login.php");
    exit;
}
require_once('DBConnection.php');
$page = isset($_GET['page']) ? $_GET['page'] : 'pos';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucwords(str_replace('_','',$page)) ?> | Drugstore POS and Inventory System</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="jquery-ui/jquery-ui.min.css">
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="jquery-ui/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="DataTables/datatables.min.css">
    <script src="DataTables/datatables.min.js"></script>
    <script src="js/script.js"></script>
    <style>
        html,body{
            height:100%;
            width:100%;
        }
		nav {
			background-color: #76d5cb;
		}
		.navbar-brand, .nav-link, .btn{
			color: black;
			font-family: Bahnschrift;
		}
        main{
            height:100%;
            display:flex;
            flex-flow:column;
        }
        #page-container{
            flex: 1 1 auto; 
            overflow:auto;
        }
        #topNavBar{
            flex: 0 1 auto; 
        }
        .truncate-1 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }
        .truncate-3 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        .modal-dialog.large {
            width: 80% !important;
            max-width: unset;
        }
        .modal-dialog.mid-large {
            width: 50% !important;
            max-width: unset;
        }
        @media (max-width:720px){
            
            .modal-dialog.large {
                width: 100% !important;
                max-width: unset;
            }
            .modal-dialog.mid-large {
                width: 100% !important;
                max-width: unset;
            }  
        }
		.nav-link:hover {
			background-color: #DCF0ED;
			color: black;
		}
		a {
			color: black;
		}
    </style>
</head>
<body>
    <main>
    <nav class="navbar navbar-expand-lg navbar-light bg-gradient" id="topNavBar">
        <div class="container">
            <a class="navbar-brand" href="#">
            Drugstore POS and <br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Inventory System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <?php if($_SESSION['type'] == 1): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'home')? 'active' : '' ?>" aria-current="page" href="./?page=home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'products')? 'active' : '' ?>" aria-current="page" href="./?page=products">Product Inventory</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'users')? 'active' : '' ?>" aria-current="page" href="./?page=users">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'sales')? 'active' : '' ?>" aria-current="page" href="./?page=sales">Sales</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'pos')? 'active' : '' ?>" aria-current="page" href="./?page=pos">POS</a>
                    </li>
                </ul>
            </div>
            <div>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle bg-transparent border-0" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    Hello <?php echo $_SESSION['fullname'] ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="./?page=manage_account">Manage Account</a></li>
                    <li><a class="dropdown-item" href="Actions.php?a=logout">Logout</a></li>
                </ul>
            </div>
            </div>
        </div>
    </nav>
    <div class="container py-3" id="page-container">
        <?php 
            if(isset($_SESSION['flashdata'])):
        ?>
        <div class="dynamic_alert alert alert-<?php echo $_SESSION['flashdata']['type'] ?>">
        <div class="float-end"><a href="javascript:void(0)" class="text-dark text-decoration-none" onclick="$(this).closest('.dynamic_alert').hide('slow').remove()">x</a></div>
            <?php echo $_SESSION['flashdata']['msg'] ?>
        </div>
        <?php unset($_SESSION['flashdata']) ?>
        <?php endif; ?>
        <?php
            include $page.'.php';
        ?>
    </div>
    </main>
    <div class="modal fade" id="uni_modal" role='dialog' data-bs-backdrop="static">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
        </div>
        </div>
    </div>
    <div class="modal fade" id="confirm_modal" role='dialog'>
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header py-2">
            <h5 class="modal-title">Confirmation</h5>
        </div>
        <div class="modal-body">
            <div id="delete_content"></div>
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn btn-primary btn-sm rounded-0" id='confirm' onclick="">Continue</button>
            <button type="button" class="btn btn-secondary btn-sm rounded-0" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
</body>
</html>