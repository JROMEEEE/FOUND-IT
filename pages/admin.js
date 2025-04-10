function showTab(tabId, event) {
    // Prevent default behavior if it's a link
    if (event) {
        event.preventDefault();
    }
    
    // Hide all tabs
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Deactivate all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active');
    });
    
    // Show the selected tab
    document.getElementById(tabId).classList.add('active');
    
    // Activate the clicked button
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    } else {
        // Find the button that corresponds to this tab and activate it
        document.querySelector(`.tab-button[data-tab="${tabId}"]`).classList.add('active');
    }
}

function updateLostItemStatus(action, lostId) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `process_lost_item.php?action=${action}&lost_id=${lostId}`, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                document.getElementById(`status-lost-${lostId}`).innerText = response.new_status;
            } else {
                alert(response.message);
            }
        } else {
            alert("An error occurred while processing the request.");
        }
    };
    xhr.onerror = function () {
        alert("An error occurred while connecting to the server.");
    };
    xhr.send();
}

function updateFoundItemStatus(action, foundId) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `process_found_item.php?action=${action}&found_id=${foundId}`, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                document.getElementById(`status-found-${foundId}`).innerText = response.new_status;
            } else {
                alert(response.message);
            }
        } else {
            alert("An error occurred while processing the request.");
        }
    };
    xhr.onerror = function () {
        alert("An error occurred while connecting to the server.");
    };
    xhr.send();
}

function updateClaimStatus(action, claimId) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `process_claim.php?action=${action}&claim_id=${claimId}`, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                const statusElement = document.getElementById(`status-claim-${claimId}`);
                statusElement.innerHTML = `<span class="badge bg-${action === 'approve' ? 'success' : 'danger'}">${response.new_status}</span>`;
            } else {
                alert(response.message);
            }
        } else {
            alert("An error occurred while processing the request.");
        }
    };
    xhr.onerror = function () {
        alert("An error occurred while connecting to the server.");
    };
    xhr.send();
}

function viewProofImage(imagePath) {
    const modal = new bootstrap.Modal(document.getElementById('proofImageModal'));
    document.getElementById('proofImageDisplay').src = '../uploads/' + imagePath;
    modal.show();
}

// Initialize tabs when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Set up initial tab
    showTab('lost-items');
});