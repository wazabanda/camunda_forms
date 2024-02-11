<?php

$processDefinitionKey = 'DemoWorkFlow';
$camundaApiUrl = 'http://localhost:8080/engine-rest';

// Create a new instance of a process

function generateRandomBusinessId()
{

    $prefix = 'BUSINESS_ID_';
    $uniqueId = uniqid($prefix, true);

    return $uniqueId;
}

// Function to create a new instance of a process with a random business ID
function createProcessInstanceWithRandomBusinessId($processDefinitionKey, $camundaApiUrl)
{
    $businessId = generateRandomBusinessId();

    $url = "{$camundaApiUrl}/process-definition/key/{$processDefinitionKey}/start";

    $ch = curl_init($url);

    // Set cURL options for a POST request
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['businessKey' => $businessId]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    // Execute cURL session and get the response
    $response = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    // Decode the JSON response
    $result = json_decode($response, true);

    // Return the process instance ID
    return $result['id'];
}


$processInstanceId = !isset($_GET['pid']) ? createProcessInstanceWithRandomBusinessId($processDefinitionKey, $camundaApiUrl) : $_GET['pid'];

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

// Example usage
$taskId = !isset($_GET['taskId']) ? getCurrentTaskId($processInstanceId, $camundaApiUrl) : $_GET['taskId'];


function getFormVariables($taskId, $camundaApiUrl)
{
    $url = "{$camundaApiUrl}/task/{$taskId}/form-variables";

    $ch = curl_init($url);

    // Set cURL options for a GET request
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL session and get the response
    $response = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    // Decode the JSON response
    $formVariables = json_decode($response, true);

    // Return the form variables
    return $formVariables;
}





// Example usage
$variables = getFormVariables($taskId, $camundaApiUrl);

// if ($currentTaskId) {
//     echo "Current Task ID: {$taskId}\n";
// } else {
//     echo "No tasks found for the process instance.\n";
// }


// Camunda API endpoint
$camundaApiUrl = 'http://localhost:8080/engine-rest';


// Fetch form data from Camunda
$ch = curl_init($camundaApiUrl . '/task/' . $taskId . '/deployed-form');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
}

curl_close($ch);


// Decode the JSON response
$formData = json_decode($response, true);


function fetchTaskAttachments($taskId) {
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
            foreach ($attachments as $attachment) {
                // var_dump($attachment);
                echo "<a href='{$attachment['url']}'>File: {$attachment['name']}</a>";
            }
        } else {
            echo "No attachments found for Task ID {$taskId}\n";
        }
    }

    // Close cURL session
    curl_close($ch);
}

// Specify the Camunda task ID

// Specify the Camunda REST API endpoint for task attachments retrieval


// Call the function to fetch attachments


// var_dump($formData);
// Check if form data is available
if (isset($formData['components'])) {
    // Render the form using PHP and HTML

    // var_dump($formData['components']);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Camunda Form</title>
    </head>

    <body>
        <h1>Camunda Form</h1>

        
        <div id="part-1">
    
        <form id="post_form" hx-post="submit.php" method="post">
        <input type="hidden" name="processInstanceId" value="<?= $processInstanceId ?>">
            <input type="hidden" name="processTaskId" value="<?= $taskId ?>">
            <?php foreach ($formData["components"] as $component) : ?>
            <div class="rounded-input">
                <?php if ($component['type'] === 'textfield') : ?>
                    <input type="text" name="<?= $component['key'] ?>" id="<?= $component['id'] ?>" value="<?= $variables[$component['key']]['value'] ?>" required>
                <?php elseif ($component['type'] === 'number') : ?>
                    <input type="number" name="<?= $component['key'] ?>" id="<?= $component['id'] ?>" value="<?= $variables[$component['key']]['value'] ?>" required>
                <?php elseif ($component['type'] === 'checkbox') : ?>
                    <input type="checkbox" name="<?= $component['key'] ?>" id="<?= $component['id'] ?>" <?php echo $variables[$component['key']]['value'] ? 'checked' : ''; ?>>
                <?php elseif ($component['type'] === 'select') : ?>
                    <select name="<?= $component['key'] ?>" id="<?= $component['id'] ?>">
                        <?php foreach ($component['values'] as $option) : ?>
                            <option value="<?= $option['value'] ?>" <?php echo $option['value'] == $variables[$component['key']]['value'] ? 'selected' : ''; ?>>
                                <?= $option['label'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($component['type'] === 'datefield') : ?>
                    <input type="date" name="<?= $component['key'] ?>" id="<?= $component['id'] ?>" value="<?= $variables[$component['key']]['value'] ?>" required>

                <?php endif; ?>
                <label for="<?= $component['key'] ?>"><?= $component['label'] ?>:</label>

            </div>
            <?php endforeach; ?>

            
            
                
                
            <button type="button" class="btn-full-width button-spacing" onclick="submitForm('save')">Save</button>
            <button type="button" class="btn-full-width button-spacing" onclick="submitForm('submit')">Submit</button>
        </form>

      <div>
        <p class="sub-text">
        <?php 
        fetchTaskAttachments($taskId);
        ?>

        </p>
      
      </div>
      
        <div>
            <button class="btn-full-width btn-plain" onclick="showUploadForm()">Upload a File</button>
        </div>
        </div>
        <form id="upload-form" class="hidden" hx-post="fileupload.php" method="post" enctype="multipart/form-data">

            <input type="hidden" name="processInstanceId" value="<?= $processInstanceId ?>">
            <input type="hidden" name="processTaskId" value="<?= $taskId ?>">
            <div class="rounded-input">
                <input type="file" name="file" id="file" required>
                <label for="file">Select File:</label>
            </div>
            <br>
            <button type="submit" class="btn-full-width">Upload File</button>
        </form>
        <script>
            function submitForm(action) {
                // Set the value of the "save" parameter based on the button clicked
                var saveParam = (action === 'save') ? 'yes' : 'no';

                // Set the value of the "save" parameter in the form
                var form = document.getElementById('post_form');
                var saveInput = document.createElement('input');
                saveInput.type = 'hidden';
                saveInput.name = action;
                saveInput.value = "yes";
                form.appendChild(saveInput);

                // Submit the form using htmx
                htmx.trigger(form, 'submit');
            }
            function showUploadForm(){
                document.getElementById("part-1").classList.add("hidden");
                document.getElementById("upload-form").classList.remove("hidden");
            }
        </script>
    </body>

    </html>
<?php
} else {
    echo 'Error: Unable to fetch form data. s';
}
?>