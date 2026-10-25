
<?php
$host = getenv('DB_HOST') ?: '127.0.0.1';
$portValue = getenv('DB_PORT');
$port = $portValue === false || $portValue === '' ? 3307 : filter_var(
    $portValue,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1, 'max_range' => 65535]]
);
$dbname = getenv('DB_NAME') ?: 'nexa_auth';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD');
if ($password === false) {
    $password = '';
}

if ($port === false) {
    error_log('Invalid DB_PORT value. The port must be between 1 and 65535.');
    http_response_code(500);
    exit('Invalid MySQL port. Set DB_PORT to a port between 1 and 65535.');
}

try {
    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $conn->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    $driverCode = $e->errorInfo[1] ?? null;
    if ($driverCode === 1049) {
        exit("MySQL is reachable, but the '$dbname' database does not exist. Create it and import schema.sql.");
    }
    if ($driverCode === 1045) {
        exit('MySQL rejected the configured username or password. Check DB_USERNAME and DB_PASSWORD.');
    }
    if ($e->getCode() === '2002' || $e->getCode() === '2003') {
        exit("Could not connect to MySQL at $host:$port. Start MySQL in XAMPP and check its configured port.");
    }
    exit('Database connection failed. Check the server log and your MySQL settings.');
}
?>
