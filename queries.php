<?php include 'header.php'; ?>

<div class="container my-4">
  <h2>Reports & Queries</h2>

  <?php
 
  $total_patients = $mysqli->query("SELECT COUNT(*) AS total FROM Patient")->fetch_assoc()['total'];
  echo "<p><strong>Total Patients:</strong> $total_patients</p>";

  // 2. Total Doctors
  $total_doctors = $mysqli->query("SELECT COUNT(*) AS total FROM Doctor")->fetch_assoc()['total'];
  echo "<p><strong>Total Doctors:</strong> $total_doctors</p>";

  $today = date('Y-m-d');
  $appointments_today = $mysqli->query("
      SELECT a.*, p.first_name AS pfn, p.last_name AS pln, d.first_name AS dfn, d.last_name AS dln
      FROM Appointment a
      JOIN Patient p ON a.patient_id = p.patient_id
      JOIN Doctor d ON a.doc_id = d.doctor_id
      WHERE a.appoint_date = '$today'
      ORDER BY a.appoint_time ASC
  ");
  echo "<h4>Appointments Today ($today)</h4>";
  if($appointments_today->num_rows>0){
      echo "<ul>";
      while($row = $appointments_today->fetch_assoc()) {
          echo "<li>".h($row['pfn']." ".$row['pln'])." with Dr. ".h($row['dfn']." ".$row['dln'])." at ".h($row['appoint_time'])."</li>";
      }
      echo "</ul>";
  } else {
      echo "<p>No appointments today.</p>";
  }

  $query1 = $mysqli->query("
      SELECT 
          p.patient_id,
          p.first_name AS patient_first_name,
          p.last_name AS patient_last_name,
          d.first_name AS doctor_first_name,
          d.last_name AS doctor_last_name,
          dep.dep_name,
          COUNT(a.appoint_id) AS total_appointments,
          COALESCE(SUM(b.total_amount),0) AS total_billed
      FROM Patient p
      LEFT JOIN Appointment a ON p.patient_id = a.patient_id
      LEFT JOIN Doctor d ON a.doc_id = d.doctor_id
      LEFT JOIN Department dep ON d.dep_id = dep.dep_id
      LEFT JOIN Bill b ON p.patient_id = b.patient_id
      GROUP BY p.patient_id, d.doctor_id, dep.dep_id
      ORDER BY p.patient_id, d.doctor_id
  ");
  echo "<h4>Patients with Doctors & Departments</h4>";
  echo "<table class='table table-bordered table-striped'><tr><th>Patient</th><th>Doctor</th><th>Department</th><th>Total Appointments</th><th>Total Billed</th></tr>";
  while($r = $query1->fetch_assoc()) {
      echo "<tr>
          <td>".h($r['patient_first_name']." ".$r['patient_last_name'])."</td>
          <td>".h($r['doctor_first_name']." ".$r['doctor_last_name'])."</td>
          <td>".h($r['dep_name'])."</td>
          <td>".h($r['total_appointments'])."</td>
          <td>".number_format($r['total_billed'],2)."</td>
      </tr>";
  }
  echo "</table>";

  $query2 = $mysqli->query("
      SELECT 
          p.patient_id,
          p.first_name AS patient_first_name,
          p.last_name AS patient_last_name,
          COUNT(DISTINCT a.appoint_id) AS total_appointments,
          COALESCE(SUM(b.total_amount), 0) AS total_billed
      FROM Patient p
      LEFT JOIN Appointment a ON p.patient_id = a.patient_id
      LEFT JOIN Bill b ON p.patient_id = b.patient_id
      GROUP BY p.patient_id
      ORDER BY p.patient_id
  ");
  echo "<h4>Total Appointments & Billed per Patient</h4>";
  echo "<table class='table table-bordered table-striped'><tr><th>Patient</th><th>Total Appointments</th><th>Total Billed</th></tr>";
  while($r = $query2->fetch_assoc()) {
      echo "<tr>
          <td>".h($r['patient_first_name']." ".$r['patient_last_name'])."</td>
          <td>".h($r['total_appointments'])."</td>
          <td>".number_format($r['total_billed'],2)."</td>
      </tr>";
  }
  echo "</table>";

  $query3 = $mysqli->query("
      SELECT 
          d.first_name, 
          d.last_name, 
          COUNT(DISTINCT a.patient_id) AS total_patients
      FROM Doctor d
      LEFT JOIN Appointment a ON d.doctor_id = a.doc_id
      GROUP BY d.doctor_id
      ORDER BY total_patients DESC
  ");
  echo "<h4>Number of Unique Patients per Doctor</h4>";
  echo "<table class='table table-bordered table-striped'><tr><th>Doctor</th><th>Total Patients</th></tr>";
  while($r = $query3->fetch_assoc()) {
      echo "<tr>
          <td>".h($r['first_name']." ".$r['last_name'])."</td>
          <td>".h($r['total_patients'])."</td>
      </tr>";
  }
  echo "</table>";

  $query4 = $mysqli->query("
      SELECT 
          p.first_name, 
          p.last_name, 
          p.patient_id,
          SUM(b.total_amount) AS total_billed
      FROM Patient p
      JOIN Bill b ON p.patient_id = b.patient_id
      GROUP BY p.patient_id, p.first_name, p.last_name
      HAVING SUM(b.total_amount) > (SELECT AVG(total_amount) FROM Bill)
      LIMIT 4
  ");
  echo "<h4>Top 4 Patients with Billed Above Average</h4>";
  echo "<table class='table table-bordered table-striped'><tr><th>Patient</th><th>Total Billed</th></tr>";
  while($r = $query4->fetch_assoc()) {
      echo "<tr>
          <td>".h($r['first_name']." ".$r['last_name'])."</td>
          <td>".number_format($r['total_billed'],2)."</td>
      </tr>";
  }
  echo "</table>";

  $query5 = $mysqli->query("
      SELECT 
          pr.prescrip_id, 
          pr.dosage_instructions, 
          GROUP_CONCAT(m.medicine_name SEPARATOR ', ') AS medicines
      FROM Prescription pr
      JOIN Pres_med pm ON pr.prescrip_id = pm.pres_id
      JOIN Medicine m ON pm.medi_id = m.medicine_id
      GROUP BY pr.prescrip_id
      LIMIT 12
  ");
  echo "<h4>12 Prescriptions with Medicines</h4>";
  echo "<table class='table table-bordered table-striped'><tr><th>Prescription ID</th><th>Dosage</th><th>Medicines</th></tr>";
  while($r = $query5->fetch_assoc()) {
      echo "<tr>
          <td>".h($r['prescrip_id'])."</td>
          <td>".h($r['dosage_instructions'])."</td>
          <td>".h($r['medicines'])."</td>
      </tr>";
  }
  echo "</table>";

  $query6 = $mysqli->query("
      SELECT 
          p.patient_id,
          CONCAT(p.first_name, ' ', p.last_name) AS patient_full_name,
          dep.dep_name AS department_name,
          GROUP_CONCAT(m.medicine_name SEPARATOR ', ') AS medicines
      FROM Patient p
      JOIN Medical_record mr ON p.patient_id = mr.patient_id
      JOIN Prescription pr ON mr.record_id = pr.record_id
      JOIN Pres_med pm ON pr.prescrip_id = pm.pres_id
      JOIN Medicine m ON pm.medi_id = m.medicine_id
      JOIN Doctor d ON mr.doctor_id = d.doctor_id
      JOIN Department dep ON d.dep_id = dep.dep_id
      WHERE p.type_of_blood = 'AB-'
      GROUP BY p.patient_id, dep.dep_id
  ");
  echo "<h4>AB- Patients with Medicines and Department</h4>";
  echo "<table class='table table-bordered table-striped'><tr><th>Patient</th><th>Department</th><th>Medicines</th></tr>";
  while($r = $query6->fetch_assoc()) {
      echo "<tr>
          <td>".h($r['patient_full_name'])."</td>
          <td>".h($r['department_name'])."</td>
          <td>".h($r['medicines'])."</td>
      </tr>";
  }
  echo "</table>";

  $query7 = $mysqli->query("
      SELECT m.medicine_name, COUNT(*) AS times_prescribed
      FROM Pres_med pm
      JOIN Medicine m ON pm.medi_id = m.medicine_id
      GROUP BY m.medicine_id
      ORDER BY times_prescribed DESC
  ");
  echo "<h4>Medicines Prescribed Count</h4>";
  echo "<table class='table table-bordered table-striped'><tr><th>Medicine</th><th>Times Prescribed</th></tr>";
  while($r = $query7->fetch_assoc()) {
      echo "<tr>
          <td>".h($r['medicine_name'])."</td>
          <td>".h($r['times_prescribed'])."</td>
      </tr>";
  }
  echo "</table>";

  $query8 = $mysqli->query("
      SELECT CONCAT(p.first_name, ' ', p.last_name) AS full_name,
             COUNT(DISTINCT pm.medi_id) AS total_medicines
      FROM Patient p
      JOIN Medical_record mr ON p.patient_id = mr.patient_id
      JOIN Prescription pr ON mr.record_id = pr.record_id
      JOIN Pres_med pm ON pr.prescrip_id = pm.pres_id
      GROUP BY p.patient_id
      ORDER BY total_medicines DESC
      LIMIT 1
  ");
  $r = $query8->fetch_assoc();
  echo "<h4>Patient with Most Distinct Medicines</h4>";
  echo "<p>".h($r['full_name'])." - Total Medicines: ".h($r['total_medicines'])."</p>";

  $query9 = $mysqli->query("
      SELECT CONCAT(p.first_name, ' ', p.last_name) AS full_name,
             COUNT(DISTINCT a.doc_id) AS num_doctors
      FROM Patient p
      JOIN Appointment a ON p.patient_id = a.patient_id
      GROUP BY p.patient_id
      HAVING COUNT(DISTINCT a.doc_id) > 1
      ORDER BY num_doctors DESC
  ");
  echo "<h4>Patients Visiting Multiple Doctors</h4>";
  if($query9->num_rows > 0){
      echo "<table class='table table-bordered table-striped'><tr><th>Patient</th><th>Number of Doctors</th></tr>";
      while($r = $query9->fetch_assoc()){
          echo "<tr>
              <td>".h($r['full_name'])."</td>
              <td>".h($r['num_doctors'])."</td>
          </tr>";
      }
      echo "</table>";
  } else {
      echo "<p>No patient has visited multiple doctors.</p>";
  }
  ?>

</div>

<?php include 'footer.php'; ?>
