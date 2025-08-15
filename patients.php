<?php include 'header.php'; ?>
<?php
// Create / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = $_POST['first_name'] ?? '';
    $last = $_POST['last_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $blood  = $_POST['type_of_blood'] ?? null;
    $phone  = $_POST['phone'] ?? null;
    $dob    = $_POST['DOB'] ?? null;

    if (isset($_POST['id']) && $_POST['id'] !== '') { // update
        $id = (int)$_POST['id'];
        run_stmt($mysqli, "UPDATE Patient SET first_name=?, last_name=?, address=?, gender=?, type_of_blood=?, phone=?, DOB=? WHERE patient_id=?",
            'sssssssi', [$first,$last,$address,$gender,$blood,$phone,$dob,$id]);
        flash('ok','Patient updated');
    } else { // create
        run_stmt($mysqli, "INSERT INTO Patient(first_name,last_name,address,gender,type_of_blood,phone,DOB) VALUES(?,?,?,?,?,?,?)",
            'sssssss', [$first,$last,$address,$gender,$blood,$phone,$dob]);
        flash('ok','Patient added');
    }
    header('Location: patients.php'); exit;
}

// Delete
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    run_stmt($mysqli, "DELETE FROM Patient WHERE patient_id=?", 'i', [$id]);
    flash('ok','Patient deleted');
    header('Location: patients.php'); exit;
}

$editing = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $editing = run_stmt($mysqli, "SELECT * FROM Patient WHERE patient_id=?", 'i', [$eid])->get_result()->fetch_assoc();
}

$patients = $mysqli->query("SELECT * FROM Patient ORDER BY patient_id ASC");
?>

<div class="row g-3">
  <!-- Form -->
  <div class="col-lg-4 col-md-5 col-12">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong><?php echo $editing?'Edit Patient':'Add Patient'; ?></strong></div>
      <div class="card-body">
        <form method="post">
          <?php if ($editing): ?><input type="hidden" name="id" value="<?php echo (int)$editing['patient_id']; ?>"><?php endif; ?>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">First name</label>
              <input required name="first_name" class="form-control" value="<?php echo h($editing['first_name'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Last name</label>
              <input required name="last_name" class="form-control" value="<?php echo h($editing['last_name'] ?? ''); ?>">
            </div>
            <div class="col-12">
              <label class="form-label">Address</label>
              <input name="address" class="form-control" value="<?php echo h($editing['address'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Gender</label>
              <select name="gender" class="form-select">
                <option value="">-- Select --</option>
                <option value="Male"   <?php if(($editing['gender'] ?? '')=='Male')   echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if(($editing['gender'] ?? '')=='Female') echo 'selected'; ?>>Female</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Blood Type</label>
              <select name="type_of_blood" class="form-select">
                <option value="">-- Select --</option>
                <?php
                $blood_types = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
                foreach($blood_types as $bt){
                    $sel = (($editing['type_of_blood'] ?? '')==$bt) ? 'selected' : '';
                    echo "<option value='$bt' $sel>$bt</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Phone</label>
              <input name="phone" class="form-control" value="<?php echo h($editing['phone'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Date of Birth</label>
              <input type="date" name="DOB" class="form-control" value="<?php echo h($editing['DOB'] ?? ''); ?>">
            </div>
            <div class="col-12 mt-3">
              <button class="btn btn-primary" type="submit">
                <?php echo $editing?'Update':'Add'; ?> Patient
              </button>
              <?php if ($editing): ?>
                <a href="patients.php" class="btn btn-secondary">Cancel</a>
              <?php endif; ?>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Table List -->
  <div class="col-lg-8 col-md-7 col-12">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong>Patients List</strong></div>
      <div class="card-body p-0">
        <table class="table table-bordered table-striped align-middle mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Gender</th>
              <th>Blood</th>
              <th>Phone</th>
              <th>DOB</th>
              <th>Address</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php while($row = $patients->fetch_assoc()): ?>
            <tr>
              <td><?php echo (int)$row['patient_id']; ?></td>
              <td><?php echo h($row['first_name'].' '.$row['last_name']); ?></td>
              <td><?php echo h($row['gender']); ?></td>
              <td><?php echo h($row['type_of_blood']); ?></td>
              <td><?php echo h($row['phone']); ?></td>
              <td><?php echo h($row['DOB']); ?></td>
              <td><?php echo h($row['address']); ?></td>
              <td>
                <a href="?edit=<?php echo $row['patient_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                <form method="post" style="display:inline-block" onsubmit="return confirm('Delete this patient?')">
                  <input type="hidden" name="delete_id" value="<?php echo $row['patient_id']; ?>">
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
