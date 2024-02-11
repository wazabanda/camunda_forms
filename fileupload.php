<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form is submitted
    $taskId = $_POST['processTaskId'];
    $processInstanceId = $_POST['processInstanceId'];
    // Check if a file is selected
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        // Specify the Camunda task ID where you want to attach the file
        // $taskId = "your_task_id";

        // Specify the Camunda REST API endpoint for task attachment creation
        $camundaApiUrl = "http://localhost:8080/engine-rest/task/{$taskId}/attachment/create";

        // var_dump($_FILES);
        // Prepare file data for Camunda attachment
        $fileData = [
            'attachment-name' => basename($_FILES["file"]["name"]),
            'attachment-type' => $_FILES["file"]["type"],
            'attachmentDescription' => 'File uploaded via PHP form',
            'url' => $_FILES["file"]["tmp_name"]
        ];

        // Initialize cURL session
        $ch = curl_init($camundaApiUrl);

        // Set cURL options for file upload
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL session
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            echo "Error uploading file to Camunda: " . curl_error($ch);
        } else {
            // echo $response;
            // echo "File uploaded successfully to Camunda.";
            echo "
                <div class='enhanced-feedback'>
                <h1>Congraturations</h1>
                <p>File uploaded successfully</p>
                <img class='symbol' src='dist/img/ok.svg' alt=''>
                <div class='action'>";
                echo '<form hx-get="rideform.php?pid=' . $processInstanceId . '" hx-target="#main-content" method="get">';
                echo "<button type='submit' class='btn-full-width btn-plain'>Next Task</button>";
                echo "</form>
                </div>
                </div>";
        }

        // Close cURL session
        curl_close($ch);
    } else {
        echo "Please select a file to upload.";
    }
}
?>

<!-- <script>
    function refreshPage() {
        window.location.href="index.php";
    }
</script> -->