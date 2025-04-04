<?php
session_start();
require_once "config.php";

// Load Google API client
require_once 'vendor/autoload.php';

$client = new Google_Client(['client_id' => '1094464308516-h8o2c3bn4qqh393ruevvjclfq31cmjms.apps.googleusercontent.com']);
$payload = $client->verifyIdToken($_POST['credential']);

if ($payload) {
    $google_id = $payload['sub'];
    $email = $payload['email'];
    $fname = $payload['given_name'];
    $lname = $payload['family_name'];
    $picture = $payload['picture'];

    // Check if user exists
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
    if(mysqli_num_rows($sql) > 0){
        $row = mysqli_fetch_assoc($sql);
        $_SESSION['unique_id'] = $row['unique_id'];
        echo json_encode(['success' => true]);
    } else {
        // Insert new user
        $unique_id = rand(time(), 100000000);
        $sql2 = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img, google_id)
            VALUES ({$unique_id}, '{$fname}', '{$lname}', '{$email}', '', '{$picture}', '{$google_id}')");
        if($sql2){
            $_SESSION['unique_id'] = $unique_id;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Something went wrong!']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Google token!']);
}
?>