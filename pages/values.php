<?php require_once __DIR__ . '/../components/header.php'; ?>

<main class="container">
    <h1>Define Your Values</h1>

    <nav class="card">
        <strong>Step: <span id="stepLabel">1</span> / 3</strong>
        <div id="stepHint">Enter 5–10 value names to start.</div>
    </nav>

    <section id="phase1" class="card">
        <h2>Phase 1 — Enter Names</h2>
        <input id="valueName" placeholder="Add a value name (e.g. Integrity)" />
        <button id="addBtn">Add name</button>
        <p>Names entered: <span id="nameCount">0</span> (recommended 5–10)</p>
        <ul id="namesList" class="values-list"></ul>
        <div>
            <button id="toPhase2">Next: Add descriptions</button>
        </div>
    </section>

    <section id="phase2" class="card" style="display:none">
        <h2>Phase 2 — Add Descriptions</h2>
        <div id="descContainer"></div>
        <div>
            <button id="backTo1">Back</button>
            <button id="toPhase3">Next: Select core values</button>
        </div>
    </section>

    <section id="phase3" class="card" style="display:none">
        <h2>Phase 3 — Pick Core Values</h2>
        <p>Select 3–5 values to be your core values.</p>
        <ul id="selectList" class="values-list"></ul>
        <div>
            <button id="backTo2">Back</button>
            <button id="finishBtn">Finish</button>
        </div>
        <div id="finishMsg" style="margin-top:12px"></div>
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