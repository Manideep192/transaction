<?php
$mysqli = new mysqli('localhost','root','','your_database');
if ($mysqli->connect_errno) { die("Connect error: ".$mysqli->connect_error); }
if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $desc = $_POST['description'];
    $stmt = $mysqli->prepare("INSERT INTO transactions (type, amount, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $type, $amount, $desc);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CEO Finance Tracker</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<div class="container">
    <h1><i class="fas fa-piggy-bank icon"></i> CEO Money Tracker</h1>
    <form method="post" autocomplete="off">
        <select name="type" required>
            <option value="" disabled selected>Transaction Type</option>
            <option value="received">&#xf4c0; Money Received from CEO</option>
            <option value="given">&#xf2b5; Money Given to CEO</option>
            <option value="expense">&#xf555; Money Spent on Expenses</option>
        </select>
        <input type="number" step="0.01" min="0" name="amount" placeholder="Amount (₹)" required>
        <input type="text" name="description" placeholder="Description (optional)">
        <button type="submit"><i class="fas fa-plus-circle"></i> Add Transaction</button>
    </form>
    <div class="table-container">
        <table>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Amount (₹)</th>
                <th>Description</th>
            </tr>
            <?php
            $result = $mysqli->query("SELECT * FROM transactions ORDER BY date DESC");
            while($row = $result->fetch_assoc()) {
                $typeTag = '';
                $icon = '';
                if($row['type']=='received') {
                    $typeTag = "<span class='tag-received'><i class='fas fa-arrow-down'></i> Received</span>";
                } elseif($row['type']=='given') {
                    $typeTag = "<span class='tag-given'><i class='fas fa-arrow-up'></i> Given</span>";
                } else {
                    $typeTag = "<span class='tag-expense'><i class='fas fa-receipt'></i> Expense</span>";
                }
                echo "<tr>
                      <td>".date('d-M-Y H:i',strtotime($row['date']))."</td>
                      <td>$typeTag</td>
                      <td>₹".number_format($row['amount'],2)."</td>
                      <td>".$row['description']."</td>
                      </tr>";
            }
            ?>
        </table>
    </div>
</div>
</body>
</html>
