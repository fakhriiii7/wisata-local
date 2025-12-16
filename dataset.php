<?php
require_once 'config/config.php';

$db = (new Database())->getConnection();

// Data users dengan nama yang unik
$users = [
    ['Fakhri Azmar', 'fakhri.azmar@email.com', '081298765431'],
    ['Zahra Kalista', 'zahra.kalista@email.com', '081287654329'],
    ['Rafi Ananta', 'rafi.ananta@email.com', '081276543218'],
    ['Salsabila Naura', 'salsabila.naura@email.com', '081265432107'],
    ['Kenzo Arjuna', 'kenzo.arjuna@email.com', '081254321096'],
    ['Nayla Putri', 'nayla.putri@email.com', '081243210985'],
    ['Bagas Pratama', 'bagas.pratama@email.com', '081232109874'],
    ['Citra Melati', 'citra.melati@email.com', '081221098763'],
    ['Daffa Rizki', 'daffa.rizki@email.com', '081210987652'],
    ['Elsa Permata', 'elsa.permata@email.com', '081209876541']
];

echo "Memulai insert users...\n";

foreach ($users as $user) {
    $nama_lengkap = $user[0];
    $email = $user[1];
    $no_telepon = $user[2];
    
    // Generate password: nama_pertama + 123
    $nama_pertama = explode(' ', $nama_lengkap)[0]; // Ambil kata pertama
    $password = strtolower($nama_pertama) . '123';  // Convert ke lowercase + 123
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert ke database
    $stmt = $db->prepare("INSERT INTO users (nama_lengkap, email, password, no_telepon, role) VALUES (?, ?, ?, ?, 'user')");
    
    if ($stmt->execute([$nama_lengkap, $email, $hashed_password, $no_telepon])) {
        echo "✅ User created: $nama_lengkap\n";
        echo "   Email: $email\n";
        echo "   Password: $password\n";
        echo "   Password Hash: $hashed_password\n";
        echo "   ---\n";
    } else {
        echo "❌ Failed to create: $nama_lengkap\n";
    }
}

echo "Selesai!\n";
?>