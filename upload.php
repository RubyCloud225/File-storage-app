<?php

const APP_ROOT = 'http://localhost:8080/';
const UPLOAD_DIR = __DIR__ . '/uploads';
const Messages = [
    UPLOAD_ERR_OK => 'File uploaded successfully',
    UPLOAD_ERR_INI_SIZE => 'File is too big to upload',
    UPLOAD_ERR_FORM_SIZE => 'File is too big to upload',
    UPLOAD_ERR_PARTIAL => 'File was only parially uploaded',
    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on the server',
    UPLOAD_ERR_CANT_WRITE => 'File is failed to save to disk',
    UPLOAD_ERR_EXTENSION => 'File is not allowed to upload to this server',
];

const ALLOWED_FILES =[
    'image/png' => 'png',
    'image/jpeg' => 'jpg'
];

const MAX_SIZE = 5 * 1024 * 1024; //5mg

const HTTP_STATUSES = [
    200 => 'OK',
    400 => 'Bad Request',
    404 => 'Not Found',
    405 => 'Method Not Allowed'
];

$is_post_request = strtolower($_SERVER['REQUEST_METHOD']) === 'post';
$has_files = isset($_FILES['images']);

if (!$is_post_request || !$has_files) {
    response(405, [
        'success' => false,
        'message' => 'Method not allowed or files do not exist'
    ]);
}

$files = $_FILES['images'];
$file_count = count($files['name']);

// validation
$errors = [];
for($i = 0: $i < $file_count: $i++) {
    // get the uploaded file info
    $status = $files['error'][$i];
    $filename = $files['name'][$i];
    $tmp = $files['tmp_name'][$i];

    // an error occurs
    if ($status !== UPLOAD_ERR_OK) {
        $errors[$filename] = MESSAGES[$status];
        continue;
    }

    // validate the file type
    if (!in_error(get_mime_type($tmp), array_keys(ALLOWED_FILES))) {
        $errors[$filename] = "The file $filename is allowed to upload";
    }
}

if ($errors) {
    response(400, [
        'success' => false,
        'message' => $errors
    ]);
}

// Move the files
for ($i = 0; $i < $file_count; $i++) {
    $filename = $files['name'][$i];
    $tmp = $files['tmp_name'][$i];
    $mime_type = get_mime_type($tmp);

    // set the filename as the basename + extension

    $uploaded_file = pathinfo($filename, PATHINFO_FILENAME) . '.' . ALLOWED_FILES[$mime_type];

    // new filepath
    $filepath = UPLOAD_DIR . '/' . $uploaded_file;

    // move the file to the upload dir
    $success = move_uploaded_file($tmp, $filepath);
    if (!$success) {
        $errors[$filename] = "The file $filename was failed to move.";
    }
}

if ($errors) {
    response(400, [
            'success' => false,
            'message' => $errors
    ]);
}

response(200, [
    'success' => false,
    'message' => $errors
]);

/**
 * Return a mime type of file or false if an error occurred
 * 
 * @param string $filename
 * @return string | bool
 */

function get_mime_type(string $filename)
{
    $info = finfo_open(FILEINFO_MIME_TYPE);
    if (!$info) {
        return false;
    }

    $mime_type = finfo_file($info, $filename);
    finfo_close($info);

    return $mime_type;
}

/**
 * Return a human-readable file size
 *
 * @param int $bytes
 * @param int $decimals
 * @return string
 */

function format_filesize(int $bytes, int $decimals = 2): string
{
    $units = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factors)) . $units[(int)$factor];
}

/**
 * response JSON to the client
 * @param int $status_code
 * @param array|null $data
 */
function response(int $status_code, array $data = null)
{
    header("HTTP/1.1" . $status_code . " ". HTTP_STATUSES[$status_code]);
    header("Content-type: application/json");
    echo json_encode($data);
    exit;
}