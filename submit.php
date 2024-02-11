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


function fetchTaskAttachmentss($taskId): iterable
{
    // Initialize cURL session
    $camundaApiUrl = "http://localhost:8080/engine-rest/task/{$taskId}/attachment";
    $ch = curl_init($camundaApiUrl);
    // Set cURL options for GET request
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL session
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        echo "Error fetching attachments from Camunda: " . curl_error($ch);
    } else {
        // Decode the JSON response
        $attachments = json_decode($response, true);

        // Output the list of attachments
        if (!empty($attachments)) {
            // echo "Attachments for Task ID {$taskId}:\n";
            curl_close($ch);
            return $attachments;
        } else {
            curl_close($ch);

            // echo "No attachments found for Task ID {$taskId}\n";
        }
    }
    return array();
    // Close cURL session

}

function getCurrentTaskId($processInstanceId, $camundaApiUrl)
{
    $url = "{$camundaApiUrl}/task?processInstanceId={$processInstanceId}";

    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL session and get the response
    $response = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    // Decode the JSON response
    $tasks = json_decode($response, true);

    // Check if tasks are available and return the task id
    if (!empty($tasks)) {
        return $tasks[0]['id']; // Assuming the first task in the list is the current task
    } else {
        return null; // No tasks found for the process instance
    }
}

// Complete a task with form data
function completeTask($taskId, $data, $camundaApiUrl, $processInstanceId)
{
    $url = "";
    // var_dump($_POST);
    if (isset($_POST['save'])) {
        // echo "saving";
        // var_dump ($data);
        $url = "{$camundaApiUrl}/task/{$taskId}/variables";

        $ch = curl_init($url);

        $modifications = array();

        foreach ($data as $key => $value) {
            // Skip "save" key and other keys that are not needed for modifications
            if ($key !== "save") {
                $modifications[$key] = array(
                    "value" => $value,
                    "type" => gettype($value) == "integer" ? "Integer" : "String"
                );
            }
        }

        $result = array(
            "modifications" => $modifications["variables"]['value'],
            // "deletions" => array("aThirdVariable", "FourthVariable") // Replace with the actual variables to delete
        );

        // var_dump($result);

        $jsonResult = json_encode($result, JSON_PRETTY_PRINT);



        // Set cURL options for a POST request
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonResult);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        // Execute cURL session and get the response
        $response = curl_exec($ch);
        // echo "------";
        // var_dump($response);
        // Close cURL session
        curl_close($ch);

        // Return the HTTP status code (e.g., 200 for success)
        return http_response_code();
    }

    if (isset($_POST['submit'])) {
        // echo "submit";
        $url = "{$camundaApiUrl}/task/{$taskId}/complete";

        $ch = curl_init($url);

        // Set cURL options for a POST request
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        // Execute cURL session and get the response
        $response = curl_exec($ch);
        // var_dump($response);


        $tsId = getCurrentTaskId($processInstanceId, $camundaApiUrl);
        $attachments = fetchTaskAttachmentss($taskId);
        if (curl_errno($ch)) {
            echo "Erro completing task: " . curl_error($ch);
        } else {
            foreach ($attachments as $attachment) {
                $camundaApiUrl = "http://localhost:8080/engine-rest/task/{$tsId}/attachment/create";

                // var_dump($_FILES);
                // Prepare file data for Camunda attachment
                $fileData = [
                    'attachment-name' => $attachment['name'],
                    'attachment-type' => $attachment['type'],
                    'attachmentDescription' => 'File uploaded via PHP form',
                    'url' => $attachments['url']
                ];

                // Initialize cURL session
                $chr = curl_init($camundaApiUrl);

                // Set cURL options for file upload
                curl_setopt($chr, CURLOPT_POST, true);
                curl_setopt($chr, CURLOPT_POSTFIELDS, $fileData);
                curl_setopt($chr, CURLOPT_RETURNTRANSFER, true);

                // Execute cURL session
                $response = curl_exec($chr);

                // Check for errors
                if (curl_errno($ch)) {
                    echo "Error uploading file to Camunda: " . curl_error($ch);
                } else {
                    // var_dump($response);
                    echo "File uploaded successfully to Camunda.";
                }
                curl_close($chr);
                // Close cURL session

            }
        }



        // Close cURL session
        curl_close($ch);

        // Return the HTTP status code (e.g., 200 for success)
        return http_response_code();
    }
}

// Example usage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $httpStatusCode = completeTask($taskIdToComplete, $data, $camundaApiUrl, $processInstanceId);
    // var_dump($_POST);

    if ($httpStatusCode == 200) {
        echo "
        <div class='enhanced-feedback'>
        <h1>Congraturations</h1>
        <p>Task completed successfully</p>
        <img class='symbol' src='dist/img/ok.svg' alt=''>
        <div class='action'>";
        echo '<form hx-get="rideform.php?pid=' . $processInstanceId . '" hx-target="#main-content" method="get">';
        echo "<button type='submit' class='btn-full-width btn-plain'>Next Task</button>";
        echo "</form>
        </div>
        </div>";
        
      
    } else {
        echo "Failed to complete the task. HTTP Status Code: {$httpStatusCode}";
    }
} else {
    echo "Form not submitted.";
}

?>



<!-- array(3) { ["customerName"]=> string(4) "Waza" ["customerLocation"]=> string(8) "Luanshya" ["customerDestiation"]=> string(6) "Lusaka" } -->