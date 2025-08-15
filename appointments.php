<?php include 'header.php'; ?>

<?php
// Create / Update appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    $patient_id = $_POST['patient_id'] ?? '';
    $doctor_id = $_POST['doctor_id'] ?? '';
    $datetime = $_POST['appointment_date'] ?? '';
    $notes = $_POST['notes'] ?? '';

    $date = date('Y-m-d', strtotime($datetime));
    $time = date('H:i:s', strtotime($datetime));

    if (isset($_POST['id']) && $_POST['id'] !== '') { // update
        $id = (int)$_POST['id'];
        run_stmt($mysqli, "UPDATE Appointment SET patient_id=?, doc_id=?, appoint_date=?, appoint_time=?, notes=? WHERE appoint_id=?",
            'iisssi', [$patient_id,$doctor_id,$date,$time,$notes,$id]);
        flash('ok','Appointment updated');
    } else { // create
        run_stmt($mysqli, "INSERT INTO Appointment(patient_id, doc_id, appoint_date, appoint_time, notes) VALUES(?,?,?,?,?)",
            'iisss', [$patient_id,$doctor_id,$date,$time,$notes]);
        flash('ok','Appointment added');
    }
    header('Location: appointments.php'); exit;
}

// Delete
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    run_stmt($mysqli, "DELETE FROM Appointment WHERE appoint_id=?", 'i', [$id]);
    flash('ok','Appointment deleted');
    header('Location: appointments.php'); exit;
}

$editing = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $editing = run_stmt($mysqli, "SELECT * FROM Appointment WHERE appoint_id=?", 'i', [$eid])->get_result()->fetch_assoc();
}

$patients = $mysqli->query("SELECT patient_id, first_name, last_name FROM Patient ORDER BY first_name");
$doctors = $mysqli->query("SELECT doctor_id, first_name, last_name FROM Doctor ORDER BY first_name");

$appointments = $mysqli->query("
    SELECT a.*, p.first_name AS pfn, p.last_name AS pln, d.first_name AS dfn, d.last_name AS dln 
    FROM Appointment a
    JOIN Patient p ON a.patient_id = p.patient_id
    JOIN Doctor d ON a.doc_id = d.doctor_id
    ORDER BY a.appoint_date ASC, a.appoint_time ASC
");
?>

<div class="row g-3">
  <!-- Form -->
  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong><?php echo $editing?'Edit Appointment':'Add Appointment'; ?></strong></div>
      <div class="card-body">
        <form method="post">
          <?php if ($editing): ?><input type="hidden" name="id" value="<?php echo (int)$editing['appoint_id']; ?>"><?php endif; ?>

          <div class="mb-3">
            <label class="form-label">Patient</label>
            <select required name="patient_id" class="form-select">
              <option value="">-- Select --</option>
              <?php while($p = $patients->fetch_assoc()): ?>
                <option value="<?php echo $p['patient_id']; ?>" <?php if(($editing['patient_id'] ?? '')==$p['patient_id']) echo 'selected'; ?>>
                  <?php echo h($p['first_name'].' '.$p['last_name']); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Doctor</label>
            <select required name="doctor_id" class="form-select">
              <option value="">-- Select --</option>
              <?php while($d = $doctors->fetch_assoc()): ?>
                <option value="<?php echo $d['doctor_id']; ?>" <?php if(($editing['doc_id'] ?? '')==$d['doctor_id']) echo 'selected'; ?>>
                  <?php echo h($d['first_name'].' '.$d['last_name']); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Date & Time</label>
            <input type="datetime-local" name="appointment_date" class="form-control" value="<?php 
                if($editing) echo h($editing['appoint_date'].'T'.$editing['appoint_time']); 
            ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control"><?php echo h($editing['notes'] ?? ''); ?></textarea>
          </div>

          <button class="btn btn-primary" type="submit">
            <?php echo $editing?'Update':'Add'; ?> Appointment
          </button>
          <?php if ($editing): ?>
            <a href="appointments.php" class="btn btn-secondary">Cancel</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <!-- List -->
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong>Appointments List</strong></div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Patient</th>
              <th>Doctor</th>
              <th>Date</th>
              <th>Time</th>
              <th>Notes</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php while($row = $appointments->fetch_assoc()): ?>
            <tr>
              <td><?php echo (int)$row['appoint_id']; ?></td>
              <td><?php echo h($row['pfn'].' '.$row['pln']); ?></td>
              <td><?php echo h($row['dfn'].' '.$row['dln']); ?></td>
              <td><?php echo h($row['appoint_date']); ?></td>
              <td><?php echo h($row['appoint_time']); ?></td>
              <td><?php echo h($row['notes'] ?? ''); ?></td>
              <td>
                <a href="?edit=<?php echo $row['appoint_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                <form method="post" style="display:inline-block" onsubmit="return confirm('Delete this appointment?')">
                  <input type="hidden" name="delete_id" value="<?php echo $row['appoint_id']; ?>">
                  <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
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
