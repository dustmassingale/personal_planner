<?php require_once __DIR__ . '/../components/header.php'; ?>

<main class="container">
  <h1>Define Your Values</h1>

  <section id="add" class="card">
    <input id="valueName" placeholder="Value name (e.g. Integrity)" />
    <input id="valueDesc" placeholder="Description (optional)" />
    <button id="addBtn">Add Value</button>
  </section>

  <section class="card">
    <ul id="valuesList" class="values-list"></ul>
  </section>
</main>

<script src="/assets/js/values.js"></script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>
<div class="w3-container w3-padding-16">
    <h2>Define Your Values</h2>
    <p>Values are the fundamental beliefs that guide your decisions and actions. Define your core values to align your goals with what matters most to you.</p>
    
    <div class="w3-row-padding">
        <div class="w3-half">
            <h3>Add New Value</h3>
            <form id="addValueForm">
                <div class="w3-section">
                    <label class="w3-text-blue"><b>Value Name</b></label>
                    <input class="w3-input w3-border" type="text" id="valueName" name="name" required>
                </div>
                <div class="w3-section">
                    <label class="w3-text-blue"><b>Description</b></label>
                    <textarea class="w3-input w3-border" id="valueDescription" name="description"></textarea>
                </div>
                <button class="w3-button w3-blue" type="submit">Add Value</button>
            </form>
        </div>
        
        <div class="w3-half">
            <h3>Your Values</h3>
            <div id="valuesList">
                <!-- Values will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadValues();
    
    // Handle form submission
    document.getElementById('addValueForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const valueData = {
            name: formData.get('name'),
            description: formData.get('description')
        };
        
        fetch('api/values.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(valueData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
            } else {
                loadValues();
                document.getElementById('addValueForm').reset();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the value.');
        });
    });
});

function loadValues() {
    fetch('api/values.php')
    .then(response => response.json())
    .then(values => {
        const valuesList = document.getElementById('valuesList');
        if (values.length === 0) {
            valuesList.innerHTML = '<p>No values defined yet.</p>';
            return;
        }
        
        let html = '<ul class="w3-ul w3-card-4">';
        values.forEach(value => {
            html += `
                <li class="w3-bar">
                    <div class="w3-bar-item">
                        <span class="w3-large">${value.name}</span>
                        <br>
                        <span class="w3-small">${value.description}</span>
                    </div>
                    <button class="w3-bar-item w3-button w3-red w3-right" onclick="deleteValue(${value.id})">Delete</button>
                </li>
            `;
        });
        html += '</ul>';
        valuesList.innerHTML = html;
    })
    .catch(error => {
        console.error('Error loading values:', error);
        document.getElementById('valuesList').innerHTML = '<p>Error loading values.</p>';
    });
}

function deleteValue(id) {
    if (confirm('Are you sure you want to delete this value?')) {
        fetch('api/values.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({id: id})
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
            } else {
                loadValues();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the value.');
        });
    }
}
</script>