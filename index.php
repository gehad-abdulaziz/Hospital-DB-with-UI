<?php include 'header.php'; ?>

<?php
$stats = [
  'patients' => $mysqli->query("SELECT COUNT(*) c FROM Patient")->fetch_assoc()['c'] ?? 0,
  'doctors' => $mysqli->query("SELECT COUNT(*) c FROM Doctor")->fetch_assoc()['c'] ?? 0,
  'appointments_today' => run_stmt($mysqli, "SELECT COUNT(*) c FROM Appointment WHERE appoint_date = CURDATE()")
      ->get_result()->fetch_assoc()['c'] ?? 0,
  'unpaid_bills' => $mysqli->query("SELECT COUNT(*) c FROM Bill WHERE payment_status='Unpaid'")->fetch_assoc()['c'] ?? 0,
  'medicines' => $mysqli->query("SELECT COUNT(*) c FROM Medicine")->fetch_assoc()['c'] ?? 0,
];
?>

<div class="row g-3">
  <div class="col-md-2">
    <div class="card shadow-sm border-0 text-white bg-primary">
      <div class="card-body d-flex align-items-center">
        <div class="me-3"><i class="fas fa-users fa-2x"></i></div>
        <div>
          <h6 class="fw-light">Patients</h6>
          <h2 class="mb-0"><?php echo (int)$stats['patients']; ?></h2>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="card shadow-sm border-0 text-white bg-success">
      <div class="card-body d-flex align-items-center">
        <div class="me-3"><i class="fas fa-user-md fa-2x"></i></div>
        <div>
          <h6 class="fw-light">Doctors</h6>
          <h2 class="mb-0"><?php echo (int)$stats['doctors']; ?></h2>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="card shadow-sm border-0 text-white bg-warning">
      <div class="card-body d-flex align-items-center">
        <div class="me-3"><i class="fas fa-calendar-check fa-2x"></i></div>
        <div>
          <h6 class="fw-light">Appointments Today</h6>
          <h2 class="mb-0"><?php echo (int)$stats['appointments_today']; ?></h2>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="card shadow-sm border-0 text-white bg-danger">
      <div class="card-body d-flex align-items-center">
        <div class="me-3"><i class="fas fa-file-invoice-dollar fa-2x"></i></div>
        <div>
          <h6 class="fw-light">Unpaid Bills</h6>
          <h2 class="mb-0"><?php echo (int)$stats['unpaid_bills']; ?></h2>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="card shadow-sm border-0 text-white bg-info">
      <div class="card-body d-flex align-items-center">
        <div class="me-3"><i class="fas fa-pills fa-2x"></i></div>
        <div>
          <h6 class="fw-light">Medicines</h6>
          <h2 class="mb-0"><?php echo (int)$stats['medicines']; ?></h2>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mt-4">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong>Latest Appointments</strong></div>
      <div class="card-body table-responsive">
        <table class="table table-hover table-sm align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th><th>Date</th><th>Time</th><th>Status</th><th>Doctor</th><th>Patient</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $res = $mysqli->query("SELECT a.appoint_id, a.appoint_date, a.appoint_time, a.status, CONCAT(d.first_name,' ',d.last_name) doc, CONCAT(p.first_name,' ',p.last_name) pat 
                                 FROM Appointment a 
                                 JOIN Doctor d ON a.doc_id=d.doctor_id 
                                 JOIN Patient p ON a.patient_id=p.patient_id 
                                 ORDER BY a.appoint_date DESC, a.appoint_time DESC 
                                 LIMIT 10");
          while($r = $res->fetch_assoc()): ?>
            <tr>
              <td><?php echo (int)$r['appoint_id']; ?></td>
              <td><?php echo h($r['appoint_date']); ?></td>
              <td><?php echo h($r['appoint_time']); ?></td>
              <td>
                <span class="badge bg-<?php echo $r['status']==='Confirmed'?'success':($r['status']==='Pending'?'warning text-dark':'secondary'); ?>">
                  <?php echo h($r['status']); ?>
                </span>
              </td>
              <td><?php echo h($r['doc']); ?></td>
              <td><?php echo h($r['pat']); ?></td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong>Recent Bills</strong></div>
      <div class="card-body table-responsive">
        <table class="table table-hover table-sm align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th><th>Date</th><th>Patient</th><th>Total</th><th>Status</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $res = $mysqli->query("SELECT b.bill_id, b.date, CONCAT(p.first_name,' ',p.last_name) pat, b.total_amount, b.payment_status 
                                 FROM Bill b 
                                 JOIN Patient p ON b.patient_id=p.patient_id 
                                 ORDER BY b.date DESC, b.bill_id DESC 
                                 LIMIT 10");
          while($r = $res->fetch_assoc()): ?>
            <tr>
              <td><?php echo (int)$r['bill_id']; ?></td>
              <td><?php echo h($r['date']); ?></td>
              <td><?php echo h($r['pat']); ?></td>
              <td><?php echo number_format((float)$r['total_amount'], 2); ?></td>
              <td>
                <span class="badge bg-<?php 
                  echo $r['payment_status']==='Paid' ? 'success' : 
                       ($r['payment_status']==='Pending' ? 'warning text-dark' : 'danger'); ?>">
                  <?php echo h($r['payment_status']); ?>
                </span>
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
