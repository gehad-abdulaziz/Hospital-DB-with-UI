<?php include 'header.php'; ?>

<?php
// Create / Update medicine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    $name = $_POST['medicine_name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock_quantity'] ?? 0;

    if (isset($_POST['id']) && $_POST['id'] !== '') { // update
        $id = (int)$_POST['id'];
        run_stmt($mysqli, "UPDATE Medicine SET medicine_name=?, price=?, stock_quantity=? WHERE medicine_id=?",
            'sdii', [$name, $price, $stock, $id]);
        flash('ok','Medicine updated');
    } else { // create
        run_stmt($mysqli, "INSERT INTO Medicine(medicine_name, price, stock_quantity) VALUES(?,?,?)",
            'sdi', [$name, $price, $stock]);
        flash('ok','Medicine added');
    }
    header('Location: medicines.php'); exit;
}

// Delete medicine
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    run_stmt($mysqli, "DELETE FROM Medicine WHERE medicine_id=?", 'i', [$id]);
    flash('ok','Medicine deleted');
    header('Location: medicines.php'); exit;
}

$editing = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $editing = run_stmt($mysqli, "SELECT * FROM Medicine WHERE medicine_id=?", 'i', [$eid])->get_result()->fetch_assoc();
}

$medicines = $mysqli->query("SELECT * FROM Medicine ORDER BY medicine_id ASC");
?>

<div class="row g-3">
  <!-- Form -->
  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong><?php echo $editing?'Edit Medicine':'Add Medicine'; ?></strong></div>
      <div class="card-body">
        <form method="post">
          <?php if ($editing): ?><input type="hidden" name="id" value="<?php echo (int)$editing['medicine_id']; ?>"><?php endif; ?>

          <div class="mb-3">
            <label class="form-label">Name</label>
            <input required name="medicine_name" class="form-control" value="<?php echo h($editing['medicine_name'] ?? ''); ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo h($editing['price'] ?? ''); ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Stock Quantity</label>
            <input type="number" name="stock_quantity" class="form-control" value="<?php echo h($editing['stock_quantity'] ?? 0); ?>">
          </div>

          <button class="btn btn-primary" type="submit">
            <?php echo $editing?'Update':'Add'; ?> Medicine
          </button>
          <?php if ($editing): ?>
            <a href="medicines.php" class="btn btn-secondary">Cancel</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <!-- List -->
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong>Medicines List</strong></div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php while($row = $medicines->fetch_assoc()): ?>
            <tr>
              <td><?php echo (int)$row['medicine_id']; ?></td>
              <td><?php echo h($row['medicine_name']); ?></td>
              <td><?php echo number_format($row['price'], 2); ?></td>
              <td><?php echo (int)$row['stock_quantity']; ?></td>
              <td>
                <a href="?edit=<?php echo $row['medicine_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                <form method="post" style="display:inline-block" onsubmit="return confirm('Delete this medicine?')">
                  <input type="hidden" name="delete_id" value="<?php echo $row['medicine_id']; ?>">
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
