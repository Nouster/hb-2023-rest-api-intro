<?php
header('Content-type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: http://localhost:5500');

require_once 'data/users.php';

echo json_encode($users);
