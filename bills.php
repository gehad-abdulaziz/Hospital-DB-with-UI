<?php include 'header.php'; ?>

<?php
// Create / Update bill
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    $patient_id = $_POST['patient_id'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $bill_date = $_POST['bill_date'] ?? '';
    $status = $_POST['status'] ?? 'Unpaid';

    if (isset($_POST['id']) && $_POST['id'] !== '') { // update
        $id = (int)$_POST['id'];
        run_stmt($mysqli, "UPDATE Bill SET patient_id=?, total_amount=?, date=?, payment_status=? WHERE bill_id=?",
            'idssi', [$patient_id, $amount, $bill_date, $status, $id]);
        flash('ok','Bill updated');
    } else { // create
        run_stmt($mysqli, "INSERT INTO Bill(patient_id, total_amount, date, payment_status) VALUES(?,?,?,?)",
            'idss', [$patient_id, $amount, $bill_date, $status]);
        flash('ok','Bill added');
    }
    header('Location: bills.php'); exit;
}

// Delete bill
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    run_stmt($mysqli, "DELETE FROM Bill WHERE bill_id=?", 'i', [$id]);
    flash('ok','Bill deleted');
    header('Location: bills.php'); exit;
}

$editing = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $editing = run_stmt($mysqli, "SELECT * FROM Bill WHERE bill_id=?", 'i', [$eid])->get_result()->fetch_assoc();
}

$patients = $mysqli->query("SELECT patient_id, first_name, last_name FROM Patient ORDER BY first_name");

$bills = $mysqli->query("SELECT b.*, p.first_name, p.last_name
                         FROM Bill b
                         JOIN Patient p ON b.patient_id=p.patient_id
                         ORDER BY b.date DESC");
?>

<div class="row g-3">
  <!-- Form -->
  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong><?php echo $editing?'Edit Bill':'Add Bill'; ?></strong></div>
      <div class="card-body">
        <form method="post">
          <?php if ($editing): ?><input type="hidden" name="id" value="<?php echo (int)$editing['bill_id']; ?>"><?php endif; ?>

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
            <label class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="<?php echo h($editing['total_amount'] ?? ''); ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Bill Date</label>
            <input type="date" name="bill_date" class="form-control" value="<?php echo h($editing['date'] ?? ''); ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="Unpaid" <?php if(($editing['payment_status'] ?? '')=='Unpaid') echo 'selected'; ?>>Unpaid</option>
              <option value="Paid" <?php if(($editing['payment_status'] ?? '')=='Paid') echo 'selected'; ?>>Paid</option>
              <option value="Pending" <?php if(($editing['payment_status'] ?? '')=='Pending') echo 'selected'; ?>>Pending</option>
            </select>
          </div>

          <button class="btn btn-primary" type="submit">
            <?php echo $editing?'Update':'Add'; ?> Bill
          </button>
          <?php if ($editing): ?>
            <a href="bills.php" class="btn btn-secondary">Cancel</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <!-- List -->
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong>Bills List</strong></div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Patient</th>
              <th>Amount</th>
              <th>Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php while($row = $bills->fetch_assoc()): ?>
            <tr>
              <td><?php echo (int)$row['bill_id']; ?></td>
              <td><?php echo h($row['first_name'].' '.$row['last_name']); ?></td>
              <td><?php echo number_format($row['total_amount'], 2); ?></td>
              <td><?php echo h($row['date']); ?></td>
              <td><?php echo h($row['payment_status']); ?></td>
              <td>
                <a href="?edit=<?php echo $row['bill_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                <form method="post" style="display:inline-block" onsubmit="return confirm('Delete this bill?')">
                  <input type="hidden" name="delete_id" value="<?php echo $row['bill_id']; ?>">
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
