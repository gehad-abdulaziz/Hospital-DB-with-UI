<?php require_once __DIR__.'/db.php'; require_once __DIR__.'/helpers.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hospital DB</title>
<link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/512/2965/2965564.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>

    body{background:#f7f7fb}
    .card{border:none;border-radius:1rem}
    .table thead th{white-space:nowrap}
    .nav-link.active{font-weight:600}
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">🏥 Hospital</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="patients.php">Patients</a></li>
        <li class="nav-item"><a class="nav-link" href="doctors.php">Doctors</a></li>
        <li class="nav-item"><a class="nav-link" href="appointments.php">Appointments</a></li>
        <li class="nav-item"><a class="nav-link" href="medicines.php">Medicines</a></li>
        <li class="nav-item"><a class="nav-link" href="bills.php">Bills</a></li>
        <li class="nav-item"><a class="nav-link" href="queries.php">Queries</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container my-4">
<?php if ($m = flash('ok')): ?>
  <div class="alert alert-success"><?php echo h($m); ?></div>
<?php endif; ?>
<?php if ($m = flash('err')): ?>
  <div class="alert alert-danger"><?php echo h($m); ?></div>
<?php endif; ?>