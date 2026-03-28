<?php
header('Location: /pages/values.php');
exit;
// Main entry point and routing
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Include necessary components
include 'components/header.php';
include 'components/nav.php';

switch($page) {
    case 'values':
        include 'pages/values.php';
        break;
    case 'goals':
        include 'pages/goals.php';
        break;
    case 'roles':
        include 'pages/roles.php';
        break;
    case 'weekly_plan':
        include 'pages/weekly_plan.php';
        break;
    case 'weekly_plan_view':
        include 'pages/weekly_plan_view.php';
        break;
    case 'export':
        include 'pages/export.php';
        break;
    case 'dashboard':
        include 'pages/dashboard.php';
        break;
    default:
        include 'pages/dashboard.php';
        break;
}

include 'components/footer.php';
?>