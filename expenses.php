<?php
$servername = "localhost";
$username = "root";  
$password = "";  
$dbname = "expenses_db";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$errors = [];
$msg = [];


if (isset($_POST['submit'])) {


    $exp_amount = mysqli_real_escape_string($conn, $_POST['exp_amount']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);

    if (empty($exp_amount) || empty($category) || empty($date)) {
        $errors[] = 'All fields are required';
    }
    
    if (!is_numeric($exp_amount) || $exp_amount <= 0) {
        $errors[] = 'Expense amount must be a positive number';
    }
    
    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
        $errors[] = 'Invalid date format. Please use yyyy-mm-dd';
    }

   
    if (empty($errors)) {

        $insert_exp = "INSERT INTO expenses (exp_amount, category, date) VALUES ('$exp_amount', '$category', '$date')";
        
        if (mysqli_query($conn, $insert_exp)) {
            
            $msg[] = 'Expense added successfully.';

           
            $budget_query = "SELECT remaining_budget FROM budgets WHERE id = 1"; 
            $budget_result = mysqli_query($conn, $budget_query);
            
            if ($budget_result && mysqli_num_rows($budget_result) > 0) {
                $budget_row = mysqli_fetch_assoc($budget_result);
                $remaining_budget = $budget_row['remaining_budget'];

                
                $new_remaining_budget = $remaining_budget - $exp_amount;
                $update_budget = "UPDATE budgets SET remaining_budget = '$new_remaining_budget' WHERE id = 1";
                
                if (mysqli_query($conn, $update_budget)) {
                    $msg[] = 'Budget updated successfully!';
                } else {
                    $errors[] = 'Error updating budget: ' . mysqli_error($conn);
                }
            } else {
                $errors[] = 'Error fetching current budget.';
            }
        } else {
            $errors[] = 'Error inserting expense: ' . mysqli_error($conn);
        }
    }
}

$conn->close();
?>

