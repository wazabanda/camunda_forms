<?php

// Specify the Camunda task ID
$taskId = "your_task_id";

// Specify the Camunda REST API endpoint for task attachments retrieval
$camundaApiUrl = "http://localhost:8080/engine-rest/task/{$taskId}/attachment";

// Initialize cURL session
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
        echo "Attachments for Task ID {$taskId}:\n";
        foreach ($attachments as $attachment) {
            echo "Attachment ID: {$attachment['id']}, Name: {$attachment['name']}, Type: {$attachment['type']}\n";
        }
    } else {
        echo "No attachments found for Task ID {$taskId}\n";
    }
}

// Close cURL session
curl_close($ch);
?>
