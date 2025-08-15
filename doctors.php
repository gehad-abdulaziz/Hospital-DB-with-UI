<?php include 'header.php'; ?>

<?php
// Fetch departments for dropdown
$departments = $mysqli->query("SELECT * FROM Department ORDER BY dep_name ASC");

// Create / Update doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    $first = $_POST['first_name'] ?? '';
    $last = $_POST['last_name'] ?? '';
    $dep_id = (int)($_POST['dep_id'] ?? 0);
    $phone = $_POST['phone'] ?? '';

    if (isset($_POST['id']) && $_POST['id'] !== '') { // update
        $id = (int)$_POST['id'];
        run_stmt($mysqli, "UPDATE Doctor SET first_name=?, last_name=?, dep_id=?, phone=? WHERE doctor_id=?",
            'ssssi', [$first,$last,$dep_id,$phone,$id]);
        flash('ok','Doctor updated');
    } else { // create
        run_stmt($mysqli, "INSERT INTO Doctor(first_name,last_name,dep_id,phone) VALUES(?,?,?,?)",
            'ssis', [$first,$last,$dep_id,$phone]);
        flash('ok','Doctor added');
    }
    header('Location: doctors.php'); exit;
}

// Delete doctor
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    run_stmt($mysqli, "DELETE FROM Doctor WHERE doctor_id=?", 'i', [$id]);
    flash('ok','Doctor deleted');
    header('Location: doctors.php'); exit;
}

// Edit doctor
$editing = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $editing = run_stmt($mysqli, "SELECT * FROM Doctor WHERE doctor_id=?", 'i', [$eid])->get_result()->fetch_assoc();
}

// Fetch doctors with department name
$doctors = $mysqli->query("
    SELECT Doctor.*, Department.dep_name 
    FROM Doctor 
    LEFT JOIN Department ON Doctor.dep_id = Department.dep_id 
    ORDER BY Doctor.doctor_id ASC
");
?>

<div class="row g-3">
  <!-- Form -->
  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong><?php echo $editing?'Edit Doctor':'Add Doctor'; ?></strong></div>
      <div class="card-body">
        <form method="post">
          <?php if ($editing): ?><input type="hidden" name="id" value="<?php echo (int)$editing['doctor_id']; ?>"><?php endif; ?>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">First name</label>
              <input required name="first_name" class="form-control" value="<?php echo h($editing['first_name'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Last name</label>
              <input required name="last_name" class="form-control" value="<?php echo h($editing['last_name'] ?? ''); ?>">
            </div>
            <div class="col-md-12">
              <label class="form-label">Specialty</label>
              <select required name="dep_id" class="form-control">
                <option value="">Select Department</option>
                <?php while($dep = $departments->fetch_assoc()): ?>
                  <option value="<?php echo $dep['dep_id']; ?>" <?php if (($editing['dep_id'] ?? 0) == $dep['dep_id']) echo 'selected'; ?>>
                    <?php echo h($dep['dep_name']); ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="col-md-12">
              <label class="form-label">Phone</label>
              <input name="phone" class="form-control" value="<?php echo h($editing['phone'] ?? ''); ?>">
            </div>
            <div class="col-12 mt-3">
              <button class="btn btn-primary" type="submit">
                <?php echo $editing?'Update':'Add'; ?> Doctor
              </button>
              <?php if ($editing): ?>
                <a href="doctors.php" class="btn btn-secondary">Cancel</a>
              <?php endif; ?>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- List -->
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong>Doctors List</strong></div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Specialty</th>
              <th>Phone</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php while($row = $doctors->fetch_assoc()): ?>
            <tr>
              <td><?php echo (int)$row['doctor_id']; ?></td>
              <td><?php echo h($row['first_name'].' '.$row['last_name']); ?></td>
              <td><?php echo h($row['dep_name']); ?></td>
              <td><?php echo h($row['phone']); ?></td>
              <td>
                <a href="?edit=<?php echo $row['doctor_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                <form method="post" style="display:inline-block" onsubmit="return confirm('Delete this doctor?')">
                  <input type="hidden" name="delete_id" value="<?php echo $row['doctor_id']; ?>">
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
