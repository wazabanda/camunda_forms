<h2>File Upload and Camunda Attachment Form</h2>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
    <label for="file">Select File:</label>
    <input type="file" name="file" id="file" required>
    <br>
    <input type="submit" value="Upload File to Camunda">
</form>