<?php

$camundaApiUrl = 'http://localhost:8080/engine-rest';


$taskIdToComplete = $_POST['processTaskId'];
$processInstanceId = $_POST['processInstanceId'];
// Prepare data for Camunda task completion
$variables = [];

foreach ($_POST as $key => $value) {
    // Add each variable to the array
    $variables[$key] = ['value' => $value];
}

// Create the final structure
$data = [
    'variables' => $variables,
];

// Complete a task with form data
function completeTask($taskId, $data, $camundaApiUrl) {
    $url = "{$camundaApiUrl}/task/{$taskId}/complete";

    $ch = curl_init($url);

    // Set cURL options for a POST request
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    // Execute cURL session and get the response
    $response = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    // Return the HTTP status code (e.g., 200 for success)
    return http_response_code();
}

// Example usage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $httpStatusCode = completeTask($taskIdToComplete, $data, $camundaApiUrl);

    if ($httpStatusCode == 200) {
        echo "Task completed successfully.";
        echo "Next Task";
        echo '<form hx-get="rideform.php?pid='.$processInstanceId.'" hx-target="#main-content" method="get">';
        echo '<button type="submit">Start Process</button>';
        echo '</form>';
    } else {
        echo "Failed to complete the task. HTTP Status Code: {$httpStatusCode}";
    }
} else {
    echo "Form not submitted.";
}

?>

            
        
<!-- array(3) { ["customerName"]=> string(4) "Waza" ["customerLocation"]=> string(8) "Luanshya" ["customerDestiation"]=> string(6) "Lusaka" } -->