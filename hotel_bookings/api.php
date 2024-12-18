<?php
include 'db.php'; // Include database connection

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action']; // Get the action type
    $table = $_POST['table'] ?? 'clients'; // Get the table name, default to 'clients' if not specified

    // Allowed tables for processing
    $allowedTables = ['clients', 'employees', 'rooms', 'bookings', 'payments', 'employees_activities'];

    // Check if the table is valid
    if (!in_array($table, $allowedTables)) {
        echo json_encode(['error' => 'Invalid table']); // Return error if table is not allowed
        exit;
    }

    // Switch statement to handle different actions
    switch ($action) {
        case 'display':
            // Display all data from the selected table
            $stmt = $pdo->query("SELECT * FROM $table");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)); // Return fetched data as JSON
            break;

        case 'fixId':
            try {
                // Queries to fix the sequences for the ID columns in the specified tables
                $queries = [
                    "SELECT setval(pg_get_serial_sequence('clients', 'id'), MAX(id)) FROM clients;",
                    "SELECT setval(pg_get_serial_sequence('employees', 'id'), MAX(id)) FROM employees;",
                    "SELECT setval(pg_get_serial_sequence('rooms', 'id'), MAX(id)) FROM rooms;",
                    "SELECT setval(pg_get_serial_sequence('bookings', 'id'), MAX(id)) FROM bookings;",
                    "SELECT setval(pg_get_serial_sequence('payments', 'id'), MAX(id)) FROM payments;",
                    "SELECT setval(pg_get_serial_sequence('employees_activities', 'id'), MAX(id)) FROM employees_activities;"
                ];

                // Execute each query to update the sequences
                foreach ($queries as $query) {
                    $pdo->query($query); // Execute each SQL query to update the sequence
                }

                echo json_encode(['success' => true]); // Return success response if all queries executed successfully
            } catch (PDOException $e) {
                // Handle any database errors and return the error message
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
            break;



        case 'search':
            $searchTerm = isset($_POST['term']) ? $_POST['term'] : ''; // Get search term
            $searchableFields = [
                // Define searchable fields for each table
                'clients' => ['id', 'name', 'surname', 'phone', 'email'],
                'employees' => ['id', 'name', 'surname', 'position'],
                'rooms' => ['id', 'type', 'price', 'available'],
                'bookings' => ['id', 'client_id', 'room_id', 'check_in', 'check_out'],
                'payments' => ['id', 'client_id', 'amount', 'date'],
                'employees_activities' => ['id', 'employee_id', 'booking_id', 'activity'],
            ];

            // Check if the table is valid
            if (!isset($searchableFields[$table])) {
                echo json_encode(['error' => 'Invalid table']);
                exit;
            }

            $fields = $searchableFields[$table]; // Get the searchable fields for the current table
            if (empty($fields)) {
                echo json_encode(['error' => 'No searchable fields for this table']);
                exit;
            }

            // Build the SQL condition to search across all fields
            $conditions = implode(' OR ', array_map(function ($field) {
                return "$field::TEXT ILIKE :term";
            }, $fields));

            try {
                $stmt = $pdo->prepare("SELECT * FROM $table WHERE $conditions"); // Prepare the search query
                $stmt->execute(['term' => "%$searchTerm%"]); // Execute the query with the search term
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)); // Return the results
            } catch (PDOException $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]); // Return database errors
            }
            break;

        case 'add':
            // Add new record to the selected table
            $data = json_decode($_POST['data'], true); // Decode the data from the client
            $columns = implode(', ', array_keys($data)); // Prepare column names
            $placeholders = implode(', ', array_fill(0, count($data), '?')); // Prepare placeholders for values
            $stmt = $pdo->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)"); // Prepare the insert query
            $stmt->execute(array_values($data)); // Execute the query with the data values
            echo json_encode(['success' => true]); // Return success message
            break;

        case 'update':
            // Update an existing record in the selected table
            $id = $_POST['id']; // Get the ID of the record to update
            $data = json_decode($_POST['data'], true); // Decode the updated data
            $assignments = implode(', ', array_map(function ($key) {
                return "$key = ?";
            }, array_keys($data))); // Prepare column updates
            $stmt = $pdo->prepare("UPDATE $table SET $assignments WHERE id = ?"); // Prepare the update query
            $stmt->execute([...array_values($data), $id]); // Execute the update query with the data and ID
            echo json_encode(['success' => true]); // Return success message
            break;

        case 'delete':
            // Delete a record from the selected table
            $id = $_POST['id']; // Get the ID of the record to delete
            $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?"); // Prepare the delete query
            $stmt->execute([$id]); // Execute the delete query
            echo json_encode(['success' => true]); // Return success message
            break;

        case 'calculate':
            // Calculate a specific aggregate function
            $function = isset($_POST['function']) ? $_POST['function'] : ''; // Get the function
            if (!$function) {
                $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM $table"); // Default to COUNT
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['count' => $result['count']]); // Return the count
            } else {
                echo json_encode(['error' => 'Function not specified']);
                exit;
            }
            break;

        case 'sort':
            // Sort the records in ascending or descending order by a specified column
            $table = $_POST['table']; // Get the table name
            $column = $_POST['column']; // Get the column name
            $direction = $_POST['direction']; // Get the sort direction

            if (!in_array($direction, ['ASC', 'DESC'])) {
                echo json_encode(['error' => 'Invalid sort direction']);
                exit;
            }

            if ($column !== 'id') {
                echo json_encode(['error' => 'Invalid column for sorting']);
                exit;
            }

            $query = "SELECT * FROM $table ORDER BY $column $direction"; // Prepare the sort query

            try {
                $stmt = $pdo->query($query); // Execute the query
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch the results
                echo json_encode($data); // Return the sorted data
            } catch (PDOException $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]); // Return error if the query fails
            }
            break;

        case 'aggregate':
            // Perform an aggregate function
            $table = $_POST['table']; // Get the table name
            $column = $_POST['column']; // Get the column name to aggregate
            $aggregateFunction = $_POST['aggregateFunction']; // Get the aggregate function

            $validFunctions = ['SUM', 'AVG', 'COUNT', 'MAX', 'MIN']; // List of valid aggregate functions
            if (!in_array(strtoupper($aggregateFunction), $validFunctions)) {
                echo json_encode(['error' => 'Invalid aggregate function']);
                exit;
            }

            // Define allowed columns for aggregation
            $allowedColumns = [
                'clients' => ['name', 'surname', 'phone', 'email'],
                'employees' => ['name', 'position', 'salary'],
                'rooms' => ['number', 'type', 'price'],
                'bookings' => ['client_id', 'room_id', 'start_date', 'end_date'],
                'payments' => ['booking_id', 'amount', 'payment_date'],
                'employees_activities' => ['employee_id', 'activity', 'activity_date']
            ];

            if (!isset($allowedColumns[$table]) || !in_array($column, $allowedColumns[$table])) {
                echo json_encode(['error' => 'Invalid column for aggregation']);
                exit;
            }

            $query = "SELECT $column, $aggregateFunction($column) AS total FROM $table GROUP BY $column"; // Prepare the aggregate query

            try {
                $stmt = $pdo->query($query); // Execute the query
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch the aggregated data
                echo json_encode($data); // Return the aggregated result
            } catch (PDOException $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]); // Return error if the query fails
            }
            break;

        case 'crossJoin':
            // Perform a cross join between two tables
            $firstTable = $_POST['firstTable']; // Get the first table
            $secondTable = $_POST['secondTable']; // Get the second table
            $firstColumn = $_POST['firstColumn']; // Get the first column to join
            $secondColumn = $_POST['secondColumn']; // Get the second column to join

            if (!in_array($firstTable, $allowedTables) || !in_array($secondTable, $allowedTables)) {
                echo json_encode(['error' => 'Invalid table(s)']);
                exit;
            }

            // Perform the cross join query
            $query = "SELECT * FROM $firstTable CROSS JOIN $secondTable WHERE $firstTable.$firstColumn = $secondTable.$secondColumn
    ";

            try {
                $stmt = $pdo->query($query); // Execute the cross join query
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch the results
                echo json_encode($data); // Return the data
            } catch (PDOException $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]); // Return error if the query fails
            }
            break;

        default:
            echo json_encode(['error' => 'Unknown action']); // Return error for unknown actions
    }
}
