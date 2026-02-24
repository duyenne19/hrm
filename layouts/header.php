<?php
	
	session_start();
	if(!isset($_SESSION['user']))
	{
		header('Location: login.php');
		exit();
	}
	include_once(__DIR__ . '/phan-quyen.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhân sự</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">

    <link rel="stylesheet" href="assets/vendors/iconly/bold.css">
	
    <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon">
	<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
	<link rel="stylesheet" href="assets/css/bootstrap-datepicker.min.css">
	<link rel="stylesheet" href="assets/css/header.css">
	 
	<link rel="stylesheet" href="assets/vendors/choices.js/choices.min.css" />
	 <!-- THƯ VIỆN APEXCHARTS -->
    <script src="assets/js/apexcharts.js"></script>

</head>

<body>
    <div id="app">
	<!-- End header --------- -->
	<!-- Đến Menu --------- -->
	<?php include_once(__DIR__ . '/menu.php'); ?>
	<!-- Hết Menu -->
	<!-- Bat đầu Main -->
	<div id="main2">
		<!-- Bắt đầu Topbar -->
		<?php include_once(__DIR__ . '/topbar.php'); ?>
		<!-- Hết Topbar -->
		<!-- Bắt đầu conten -->
			<div id="main">
                <!-- Bắt đầu conten -->
				<!-- Hết conten -->
				<div class="content-wrapper">