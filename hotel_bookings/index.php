<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
    <h1 class="text-center">Hotel Booking Database Management</h1>
    <div class="d-flex justify-content-center mb-3">
        <select id="tableSelector" class="form-select w-auto" onchange="updateTable()">
            <option value="clients">Clients</option>
            <option value="employees">Employees</option>
            <option value="rooms">Rooms</option>
            <option value="bookings">Bookings</option>
            <option value="payments">Payments</option>
            <option value="employees_activities">Employees Activities</option>
        </select>
        <button class="btn btn-outline-secondary ms-2" onclick="searchData()">Search</button>
    </div>
    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
        <button class="btn btn-outline-primary" onclick="displayData()">Display data</button>
        <button class="btn btn-outline-primary" onclick="fixId()">Fix ID</button>
        <div class="vr"></div>
        <button class="btn btn-outline-success" onclick="addRecord()">Add</button>
        <button class="btn btn-outline-danger" onclick="deleteRecord()">Delete</button>
        <button class="btn btn-outline-warning" onclick="updateRecord()">Update</button>
        <div class="vr"></div>
        <button class="btn btn-outline-dark" onclick="totalRecords()">Total</button>
        <button class="btn btn-outline-dark" onclick="sortById()">Sort</button>
        <button class="btn btn-outline-dark" onclick="aggregateData()">Calculate</button>
        <button class="btn btn-outline-dark" onclick="performCrossJoin()">Cross Join</button>
    </div>
    <div id="results" class="mt-4">
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
