<?php

// Get the raw request body
$rawData = file_get_contents('php://input');


// Respond to LINE with success status
http_response_code(200);
echo json_encode(['status' => 'success']);
