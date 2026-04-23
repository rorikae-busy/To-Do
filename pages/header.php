<?php
// pages/header.php — shared header for all pages
// Usage: include 'header.php'; — pass $activePage and $pageLabel before including

$days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
$months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
$now    = new DateTime();
$dayName  = $days[$now->format('w')];
$dayNum   = $now->format('j');
$monthName = $months[(int)$now->format('n') - 1];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Planner v2 — <?= htmlspecialchars($pageTitle ?? 'Planner') ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>

<div class="app-wrapper">

    <!-- ── TAB NAVIGATION ── -->
    <nav class="nav-tabs-custom">
        <a href="todo.php"    class="nav-link <?= ($activePage==='todo')    ? 'active':'' ?>">Todo</a>
        <a href="journal.php" class="nav-link <?= ($activePage==='journal') ? 'active':'' ?>">Journal</a>
        <a href="notes.php"   class="nav-link <?= ($activePage==='notes')   ? 'active':'' ?>">Notes</a>
    </nav>

    <!-- ── MAIN LAYOUT ── -->
    <div class="planner-layout">

        <!-- Date Card -->
        <div class="date-card">
            <div class="day-name"><?= $dayName ?></div>
            <div class="day-circle"><?= $dayNum ?></div>
            <div class="month-name"><?= $monthName ?></div>
        </div>

        <!-- Right Panel starts here — closed in footer.php -->
        <div class="right-panel">
