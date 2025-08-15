
CREATE TABLE Patient (
    patient_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(20) NOT NULL,
    last_name VARCHAR(20) NOT NULL,
    address VARCHAR(50),
    gender ENUM('Male', 'Female') NOT NULL,
    type_of_blood ENUM('A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'),
    phone VARCHAR(15) UNIQUE,
    DOB DATE 
);

-- =========================
-- =========================
CREATE TABLE Department (
    dep_id INT PRIMARY KEY AUTO_INCREMENT,
    dep_name VARCHAR(20)
);

-- =========================
-- =========================

CREATE TABLE Doctor (
    doctor_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(20) NOT NULL,
    last_name VARCHAR(20) NOT NULL,
    Email VARCHAR(50),
    phone VARCHAR(15),
    dep_id INT,
    FOREIGN KEY (dep_id) REFERENCES Department(dep_id)
);

-- =========================
-- =========================

CREATE TABLE Appointment (
    appoint_id INT PRIMARY KEY AUTO_INCREMENT,
    appoint_date DATE not null,
    appoint_time TIME,
    status VARCHAR(20),
    doc_id INT,
    patient_id INT,
    FOREIGN KEY (doc_id) REFERENCES Doctor(doctor_id),
    FOREIGN KEY (patient_id) REFERENCES Patient(patient_id)
);
ALTER TABLE Appointment
ADD CONSTRAINT unique_doctor_appointment
UNIQUE (appoint_date, appoint_time, doc_id);


-- =========================
-- =========================

CREATE TABLE Medicine (
    medicine_id INT PRIMARY KEY AUTO_INCREMENT,
    medicine_name VARCHAR(100),
    price DECIMAL(8,2),
    stock_quantity INT DEFAULT 0
);

-- =========================
-- =========================

CREATE TABLE Medical_record (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    treatment TEXT,
    diagnosis TEXT,
    date_of_visit DATE,
    patient_id INT,
    doctor_id INT,
    appoint_id INT,
    FOREIGN KEY (patient_id) REFERENCES Patient(patient_id),
    FOREIGN KEY (doctor_id) REFERENCES Doctor(doctor_id),
    FOREIGN KEY (appoint_id) REFERENCES Appointment(appoint_id)
);

-- =========================
-- =========================
CREATE TABLE Bill (
    bill_id INT PRIMARY KEY AUTO_INCREMENT,
    total_amount DECIMAL(10,2) CHECK (total_amount >= 0),
    payment_status ENUM('Paid', 'Unpaid', 'Pending'),
    date DATE NOT NULL,
    patient_id INT,
    FOREIGN KEY (patient_id) REFERENCES Patient(patient_id)
);

-- =========================
-- =========================
CREATE TABLE Prescription (
    prescrip_id INT PRIMARY KEY AUTO_INCREMENT ,
    duration VARCHAR(50),
    dosage_instructions TEXT,
    record_id INT,
    FOREIGN KEY (record_id) REFERENCES Medical_record(record_id)
);
-- =========================
-- (junction table for prescription & medicine) because it was many to many relation
-- =========================
CREATE TABLE Pres_med (
    medi_id INT,
    pres_id INT,
    PRIMARY KEY (medi_id, pres_id),
    FOREIGN KEY (medi_id) REFERENCES Medicine(medicine_id),
    FOREIGN KEY (pres_id) REFERENCES Prescription(prescrip_id)
);


SHOW TABLES;

-- ==================================================================================

-- =========================
-- Patient
-- =========================
INSERT INTO Patient (first_name, last_name, address, gender, type_of_blood, phone, DOB) VALUES
('Ahmed', 'Hassan', 'Cairo, Egypt', 'Male', 'A+', '0100000001', '1990-05-12'),
('Sara', 'Ali', 'Giza, Egypt', 'Female', 'B-', '0100000002', '1988-09-23'),
('Omar', 'Khaled', 'Alexandria, Egypt', 'Male', 'O+', '0100000003', '1995-03-15'),
('Mona', 'Youssef', 'Cairo, Egypt', 'Female', 'AB+', '0100000004', '1992-07-30'),
('Khaled', 'Mahmoud', 'Mansoura, Egypt', 'Male', 'A-', '0100000005', '1985-12-01'),
('Layla', 'Morad', 'Cairo, Egypt', 'Female', 'O+', '0100000006', '2000-1-17'),
('Yusuf', 'Ali', 'Giza, Egypt', 'Male', 'O-', '0100000007', '2005-7-19'),
('Bosy', 'Hussein', 'Aswan, Egypt', 'Female', 'A+', '0100000008', '2010-2-14'),
('Sama', 'Salah', 'Suhag, Egypt', 'Female', 'AB-', '0100000009', '2015-5-5'),
('Soly', 'Saber', 'Giza, Egypt', 'Male', 'B+', '0100000010', '2020-6-6');

-- =========================
-- Department
-- =========================
INSERT INTO Department (dep_name) VALUES
('Cardiology'),       
('Neurology'),        
('Pediatrics'),      
('Orthopedics'),     
('Dermatology'),      
('General Surgery'),  
('Dentistry'),        
('Internal Medicine'),
('ENT');             

-- =========================
-- Doctor
-- =========================
INSERT INTO Doctor (first_name, last_name, Email, phone, dep_id) VALUES
('Mohamed', 'Adel', 'mohamed.adel@hospital.com', '0101000001', 1),
('Ayman', 'Hassan', 'ayman.hassan@hospital.com', '0101000010', 1),
('Mona', 'ElSayed', 'mona.elsayed@hospital.com', '0101000011', 1),
('Yasmin', 'Fouad', 'yasmin.fouad@hospital.com', '0101000002', 2),
('Hany', 'Lotfy', 'hany.lotfy@hospital.com', '0101000003', 3),
('Rania', 'Kamel', 'rania.kamel@hospital.com', '0101000004', 4),
('Tamer', 'Ibrahim', 'tamer.ibrahim@hospital.com', '0101000005', 5),
('Tamem', 'Khaled', 'tamem.Khaled@hospital.com', '0101000016', 5),
('Amira', 'Sami', 'amira.sami@hospital.com', '0101000006', 6),
('Laila', 'Fathy', 'laila.fathy@hospital.com', '0101000012', 7),
('Sherif', 'Tawfik', 'sherif.tawfik@hospital.com', '0101000013', 7),
('Ola', 'Mahmoud', 'ola.mahmoud@hospital.com', '0101000008', 8),
('Fady', 'Halim', 'fady.halim@hospital.com', '0101000014', 9),
('Fdl', 'Hany', 'fdl.Hany@hospital.com', '0101000015', 9);

-- =========================
-- Appointment
-- =========================
INSERT INTO Appointment (appoint_date, appoint_time, status, doc_id, patient_id) VALUES
('2025-08-15', '09:00:00', 'Scheduled', 1, 1),
('2025-08-15', '10:00:00', 'Scheduled', 1, 2),
('2025-08-15', '09:30:00', 'Completed', 2, 3),
('2025-08-15', '11:00:00', 'Cancelled', 3, 4),
('2025-08-16', '09:00:00', 'Scheduled', 4, 5),
('2025-08-16', '10:00:00', 'Completed', 5, 6),
('2025-08-16', '11:00:00', 'Scheduled', 6, 7),
('2025-08-17', '09:00:00', 'Cancelled', 7, 8),
('2025-08-17', '09:30:00', 'Scheduled', 8, 9),
('2025-08-17', '10:00:00', 'Completed', 9, 10),
('2025-08-18', '09:00:00', 'Scheduled', 10, 1),
('2025-08-18', '09:30:00', 'Scheduled', 11, 2),
('2025-08-18', '10:00:00', 'Completed', 12, 3),
('2025-08-19', '09:00:00', 'Scheduled', 13, 4),
('2025-08-19', '09:30:00', 'Scheduled', 14, 5);

-- =========================
-- Medicine
-- =========================
INSERT INTO Medicine (medicine_name, price, stock_quantity) VALUES
('Atenolol 50mg', 60.00, 80),
('Nitroglycerin 0.5mg', 75.00, 50),

('Carbamazepine 200mg', 90.00, 40),
('Levodopa 250mg', 120.00, 30),

('Pediatric Multivitamin Syrup', 25.00, 100),
('Paracetamol Syrup 120mg/5ml', 15.00, 150),

('Calcium + Vitamin D Tablets', 50.00, 90),
('Diclofenac Gel 1%', 35.00, 60),

('Hydrocortisone Cream 1%', 28.00, 70),
('Clotrimazole Cream 1%', 22.00, 50),

('Ceftriaxone 1g Injection', 65.00, 40),
('Metronidazole 500mg', 30.00, 60),

('Chlorhexidine Mouthwash 0.2%', 18.00, 80),
('Amoxicillin 500mg', 45.00, 100),

-- Internal Medicine (باطنة)
('Omeprazole 20mg', 35.00, 90),
('Metformin 500mg', 55.00, 100),

-- ENT (أنف وأذن وحنجرة)
('Cetirizine 10mg', 18.00, 80),
('Fluticasone Nasal Spray', 95.00, 40);

-- =========================
-- Medical_record
-- =========================
INSERT INTO Medical_record (treatment, diagnosis, date_of_visit, patient_id, doctor_id, appoint_id) VALUES
('Blood pressure control, prescribed Atenolol 50mg', 'Hypertension', '2025-08-15', 1, 1, 1),
('Follow-up for chest pain, Nitroglycerin as needed', 'Angina', '2025-08-15', 2, 1, 2),
('Neurological assessment, prescribed Carbamazepine 200mg', 'Epilepsy', '2025-08-15', 3, 2, 3),
('Physical therapy and pain management', 'Knee injury', '2025-08-16', 4, 6, 6),
('Vitamin supplements for growth', 'Vitamin D deficiency', '2025-08-16', 5, 5, 5),
('Dermatology treatment: Hydrocortisone Cream', 'Eczema', '2025-08-17', 6, 7, 7),
('Paracetamol syrup for fever', 'Flu', '2025-08-17', 7, 5, 8),
('Dental treatment: Amoxicillin 500mg', 'Dental infection', '2025-08-18', 8, 12, 10),
('ENT: Cetirizine 10mg', 'Allergic rhinitis', '2025-08-18', 9, 14, 11),
('Metformin for blood sugar control', 'Diabetes', '2025-08-19', 10, 11, 14);


-- =========================
-- Bill
-- =========================
INSERT INTO Bill (total_amount, payment_status, date, patient_id) VALUES
(200.00, 'Paid', '2025-08-15', 1),
(150.00, 'Pending', '2025-08-18', 1),
(180.00, 'Paid', '2025-08-15', 2),
(90.00, 'Unpaid', '2025-08-18', 2),
(220.00, 'Paid', '2025-08-15', 3),
(300.00, 'Paid', '2025-08-16', 4),
(120.00, 'Pending', '2025-08-19', 4),
(250.00, 'Paid', '2025-08-16', 5),
(70.00, 'Paid', '2025-08-17', 7),
(150.00, 'Pending', '2025-08-18', 8),
(60.00, 'Paid', '2025-08-18', 9),
(200.00, 'Paid', '2025-08-19', 10);

-- =========================
-- Prescription
-- =========================
-- =========================
-- Prescription
-- =========================
INSERT INTO Prescription (duration, dosage_instructions, record_id) VALUES
('30 days', 'Take 1 tablet of Atenolol 50mg daily and 1 Nitroglycerin 0.5mg as needed', 1),
('365 days', 'Take 1 Nitroglycerin 0.5mg tablet when chest pain occurs', 2),
('60 days', 'Take 1 Carbamazepine 200mg tablet twice daily and 1 Levodopa 250mg daily', 3),
('14 days', 'Apply Diclofenac Gel 1% on affected area twice daily and Take Calcium + Vitamin D daily', 4),
('30 days', 'Take 1 Pediatric Multivitamin Syrup 5ml daily', 5),
('7 days', 'Apply Hydrocortisone Cream 1% twice daily and Clotrimazole Cream 1% twice daily', 6),
('5 days', 'Take Paracetamol Syrup 5ml every 6 hours if fever occurs', 7),
('10 days', 'Take Amoxicillin 500mg tablet twice daily and use Chlorhexidine Mouthwash 0.2%', 8),
('7 days', 'Take Cetirizine 10mg tablet once daily and use Fluticasone Nasal Spray', 9),
('30 days', 'Take Metformin 500mg tablet once daily with meals and Take Omeprazole 20mg tablet once daily', 10),
('15 days', 'Take 1 tablet of Atenolol 50mg daily', 1),
('7 days', 'Take Nitroglycerin 0.5mg as needed', 2),
('30 days', 'Take Levodopa 250mg once daily', 3),
('7 days', 'Apply Calcium + Vitamin D once daily', 4),
('15 days', 'Take Pediatric Multivitamin Syrup 5ml daily', 5),
('5 days', 'Apply Clotrimazole Cream 1% twice daily', 6),
('3 days', 'Take Paracetamol Syrup 5ml every 6 hours', 7),
('7 days', 'Use Chlorhexidine Mouthwash 0.2% twice daily', 8),
('5 days', 'Use Fluticasone Nasal Spray once daily', 9),
('15 days', 'Take Omeprazole 20mg tablet once daily', 10);
-- =========================
-- Pres_med (junction table)
-- =========================
INSERT INTO Pres_med (medi_id, pres_id) VALUES
(1, 31),(2, 31),(2, 32),
(3, 33),(4, 33),
(8, 34),(7, 34), 
(5, 35),(9, 36),
(10, 36),(6, 37),
(12, 38),(11, 38),
(17, 39),(18, 39),
(16, 40),(15, 40),
(1, 41),(2, 42),
(4, 43),(7, 44),
(5, 45),(10, 46),
(6, 47),(11, 48), 
(18, 49),(15, 50);




SELECT * FROM Patient;
SELECT * FROM Department;
SELECT * FROM Doctor;
SELECT * FROM Appointment;
SELECT * FROM Medicine;
SELECT * FROM Medical_record;
SELECT * FROM Bill;
SELECT * FROM Prescription;
SELECT * FROM Pres_med;
-- =========================================
-- Query 1: Show all patients with the doctors they visited, the department, 
-- the number of appointments each patient had with each doctor, 
-- and the total billed amount.
SELECT 
    p.patient_id,
    p.first_name AS patient_first_name,
    p.last_name AS patient_last_name,
    d.first_name AS doctor_first_name,
    d.last_name AS doctor_last_name,
    dep.dep_name,
    COUNT(a.appoint_id) AS total_appointments,
    COALESCE(SUM(b.total_amount), 0) AS total_billed
FROM Patient p
LEFT JOIN Appointment a ON p.patient_id = a.patient_id
LEFT JOIN Doctor d ON a.doc_id = d.doctor_id
LEFT JOIN Department dep ON d.dep_id = dep.dep_id
LEFT JOIN Bill b ON p.patient_id = b.patient_id
GROUP BY p.patient_id, d.doctor_id, dep.dep_id
ORDER BY p.patient_id, d.doctor_id;

-- =========================================
-- Query 2: Show each patient with the total number of appointments 
-- and total billed amount only per patient.
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
ORDER BY p.patient_id;

-- =========================================
-- Query 3: Count the number of unique patients for each doctor.
SELECT 
    d.first_name, 
    d.last_name, 
    COUNT(DISTINCT a.patient_id) AS total_patients
FROM Doctor d
LEFT JOIN Appointment a ON d.doctor_id = a.doc_id
GROUP BY d.doctor_id
ORDER BY total_patients DESC;

-- This query selects the first 4 patients whose total billed amount is greater than the average bill amount (165.833333).
SELECT 
    p.first_name, 
    p.last_name, 
    p.patient_id,
    SUM(b.total_amount) AS total_billed
FROM Patient p
JOIN Bill b ON p.patient_id = b.patient_id
GROUP BY p.patient_id, p.first_name, p.last_name
HAVING SUM(b.total_amount) > (SELECT AVG(total_amount) FROM Bill)
LIMIT 4;


-- This query lists 12 prescriptions along with their dosage instructions
-- and concatenates the names of all medicines included in each prescription.
SELECT 
    pr.prescrip_id, 
    pr.dosage_instructions, 
    GROUP_CONCAT(m.medicine_name SEPARATOR ', ') AS medicines
FROM Prescription pr
JOIN Pres_med pm ON pr.prescrip_id = pm.pres_id
JOIN Medicine m ON pm.medi_id = m.medicine_id
GROUP BY pr.prescrip_id
limit 12;


-- List AB- patients with their medicines and the department of their doctor
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
GROUP BY p.patient_id, dep.dep_id;


-- This query lists all medicines and counts how many times each medicine has been prescribed,
SELECT m.medicine_name, COUNT(*) AS times_prescribed
FROM Pres_med pm
JOIN Medicine m ON pm.medi_id = m.medicine_id
GROUP BY m.medicine_id
ORDER BY times_prescribed DESC;

-- This query shows the patient with the highest number of distinct medicines prescribed
SELECT CONCAT(p.first_name, ' ', p.last_name) AS full_name,
       COUNT(DISTINCT pm.medi_id) AS total_medicines
FROM Patient p
JOIN Medical_record mr ON p.patient_id = mr.patient_id
JOIN Prescription pr ON mr.record_id = pr.record_id
JOIN Pres_med pm ON pr.prescrip_id = pm.pres_id
GROUP BY p.patient_id
ORDER BY total_medicines DESC
LIMIT 1;


-- This query shows patients who have visited multiple doctors
SELECT CONCAT(p.first_name, ' ', p.last_name) AS full_name,
       COUNT(DISTINCT a.doc_id) AS num_doctors
FROM Patient p
JOIN Appointment a ON p.patient_id = a.patient_id
GROUP BY p.patient_id
HAVING COUNT(DISTINCT a.doc_id) > 1
ORDER BY num_doctors DESC;
