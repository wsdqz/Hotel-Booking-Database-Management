let currentTable = 'clients'; // Default table for operations

// Update the table based on user selection
function updateTable() {
    const selector = document.getElementById('tableSelector');
    currentTable = selector.value; // Get the selected table name
    displayData(); // Refresh the displayed data
}

// Send fetch request with POST method
function fetchData(action, data = {}) {
    return fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action, table: currentTable, ...data }), // Add action and table info to the request
    }).then(response => response.json()); // Return JSON response
}

// Display data in the HTML
function displayData() {
    fetchData('display').then(data => {
        const resultsDiv = document.getElementById('results');
        resultsDiv.innerHTML = generateTable(data); // Generate HTML table from data
    });
}

// Function to initialize and fix the sequences for ID columns in the database
function fixId() {
    // Send a request to the server to fix the sequences for all tables
    fetchData('fixId')
        .then(data => {
            if (data.success) {
                alert('ID fixed successfully'); // Notify user if the sequences are updated
            } else {
                alert('Error: ' + data.error); // Notify user if an error occurred
            }
        })
        .catch(err => {
            console.error('Error during fixing ID sequences:', err); // Log any errors
            alert('Error during fixing ID sequences: ' + err.message); // Notify user of error
        });
}

// Search for data based on a term
function searchData() {
    const term = prompt(`Enter a search term for the "${currentTable}" table:`);
    if (!term) return;
    fetchData('search', { term }).then(data => {
        const resultsDiv = document.getElementById('results');
        resultsDiv.innerHTML = generateTable(data); // Display search results in a table
    });
}

// Add a new record to the table
function addRecord() {
    const data = promptForData(); // Get the data to add
    fetchData('add', { data: JSON.stringify(data) }).then(() => {
        alert('Record added!'); // Notify user on success
        displayData(); // Refresh the data display
    });
}

// Update an existing record in the table
function updateRecord() {
    const id = prompt('Enter the ID of the record to update:');
    const data = promptForData(); // Get the new data for update
    fetchData('update', { id, data: JSON.stringify(data) }).then(() => {
        alert('Record updated!'); // Notify user on success
        displayData(); // Refresh the data display
    });
}

// Delete a record from the table
function deleteRecord() {
    const id = prompt('Enter the ID of the record to delete:');
    fetchData('delete', { id }).then(() => {
        alert('Record deleted!'); // Notify user on success
        displayData(); // Refresh the data display
    });
}

// Calculate and display total records count
function totalRecords() {
    fetchData('calculate').then(data => {
        const resultsDiv = document.getElementById('results');
        if (data.count !== undefined) {
            resultsDiv.innerHTML = `<h1 class="text-center">Number of records in table "${currentTable}": ${data.count}</h1>`;
        } else {
            resultsDiv.innerHTML = `<h1>Error: ${data.error}</h1>`; // Display error if calculation fails
        }
    });
}

// Sort records by ID
function sortById() {
    const direction = confirm("Sort by ID in ascending order? Click 'Cancel' for descending.") ? 'ASC' : 'DESC';

    fetchData('sort', { column: 'id', direction }).then(data => {
        if (data.error) {
            alert(`Server error: ${data.error}`);
            return;
        }
        const resultsDiv = document.getElementById('results');
        resultsDiv.innerHTML = generateTable(data); // Display sorted data
    }).catch(err => {
        console.error("Error fetching sorted data:", err);
        alert(`Error sorting data: ${err.message}`);
    });
}

// Perform aggregation (e.g., SUM, AVG)
function aggregateData() {
    const column = prompt("Enter the column name to group by (e.g. 'name', 'client_id', etc.):");
    if (!column) {
        alert("Column name is required.");
        return;
    }

    const aggregateFunction = prompt("Enter the aggregate function (e.g., SUM, AVG, COUNT):");
    if (!aggregateFunction) {
        alert("Aggregate function is required.");
        return;
    }

    fetchData('aggregate', { column, aggregateFunction }).then(data => {
        if (data.error) {
            alert(`Server error: ${data.error}`);
            return;
        }
        const resultsDiv = document.getElementById('results');
        resultsDiv.innerHTML = generateTable(data); // Display aggregated data
    }).catch(err => {
        console.error("Error fetching aggregated data:", err);
        alert(`Error calculating totals: ${err.message}`);
    });
}

// Perform a cross join between two tables
function performCrossJoin() {
    const firstTable = prompt("Enter the first table to join (e.g. 'clients'):");
    const secondTable = prompt("Enter the second table to join (e.g. 'bookings'):");

    if (!firstTable || !secondTable) {
        alert("Both tables are required.");
        return;
    }

    const firstColumn = prompt(`Enter the column to join from ${firstTable}:`);
    const secondColumn = prompt(`Enter the column to join from ${secondTable}:`);

    if (!firstColumn || !secondColumn) {
        alert("Both columns are required.");
        return;
    }

    fetchData('crossJoin', { firstTable, secondTable, firstColumn, secondColumn }).then(data => {
        if (data.error) {
            alert(`Server error: ${data.error}`);
            return;
        }
        const resultsDiv = document.getElementById('results');
        resultsDiv.innerHTML = generateTable(data); // Display cross join result
    }).catch(err => {
        console.error("Error fetching cross join data:", err);
        alert(`Error performing cross join: ${err.message}`);
    });
}

// Get data to be added or updated
function promptForData() {
    const fields = {
        clients: ['name', 'surname', 'phone', 'email'],
        employees: ['name', 'surname', 'position'],
        rooms: ['type', 'price', 'available'],
        bookings: ['client_id', 'room_id', 'check_in', 'check_out'],
        payments: ['client_id', 'amount', 'date'],
        employees_activities: ['employee_id', 'booking_id', 'activity'],
    };

    const tableFields = fields[currentTable];
    const data = {};
    tableFields.forEach(field => {
        const value = prompt(`Enter ${field}:`);
        if (value) data[field] = value;
    });
    return data;
}

// Generate an HTML table from JSON data
function generateTable(data) {
    if (!data.length) return '<h1 class="text-center">No data to display</h1>';
    const headers = Object.keys(data[0]);
    const rows = data.map(row => `<tr>${headers.map(h => `<td>${row[h]}</td>`).join('')}</tr>`).join('');
    return `<table class="table table-striped"><thead><tr>${headers.map(h => `<th>${h}</th>`).join('')}</tr></thead><tbody>${rows}</tbody></table>`;
}
