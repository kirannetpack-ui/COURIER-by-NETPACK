<!-- Tracking Update Modal -->
<div id="trackingUpdateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-4 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-sync-alt text-teal-600 mr-2"></i> Update Tracking
            </h3>
            <button onclick="closeTrackingModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="trackingUpdateForm" class="space-y-4">
            @csrf
            <input type="hidden" id="modalShipmentId" name="shipment_id" value="">

            <div>
                <label class="block text-sm font-medium mb-1">Tracking Number</label>
                <input type="text" id="modalTrackingNumber" readonly 
                       class="w-full bg-gray-100 border rounded-lg px-3 py-2 font-mono text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Status *</label>
                <select id="modalStatus" name="status" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="pending">📋 Order Placed</option>
                    <option value="confirmed">✅ Confirmed</option>
                    <option value="processing">⚙️ Processing</option>
                    <option value="picked_up">📦 Picked Up</option>
                    <option value="in_transit">🚚 In Transit</option>
                    <option value="customs_clearance">🛃 Customs Clearance</option>
                    <option value="out_for_delivery">🚚 Out for Delivery</option>
                    <option value="delivered">✅ Delivered</option>
                    <option value="failed_delivery">❌ Delivery Failed</option>
                    <option value="returned">↩️ Returned</option>
                    <option value="cancelled">❌ Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Location</label>
                <input type="text" id="modalLocation" name="location" 
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500"
                       placeholder="e.g., Kathmandu Hub">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Notes</label>
                <textarea id="modalNotes" name="notes" rows="2" 
                          class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500"
                          placeholder="Additional details..."></textarea>
            </div>

            <div id="modalMessage" class="hidden"></div>

            <div class="flex gap-3 pt-2">
                <button type="submit" id="modalSubmitBtn" 
                        class="flex-1 bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">
                    <i class="fas fa-save mr-2"></i> Update Status
                </button>
                <button type="button" onclick="closeTrackingModal()" 
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openTrackingModal(shipmentId, trackingNumber) {
    document.getElementById('modalShipmentId').value = shipmentId;
    document.getElementById('modalTrackingNumber').value = trackingNumber;
    document.getElementById('trackingUpdateModal').classList.remove('hidden');
    document.getElementById('modalMessage').classList.add('hidden');
    document.getElementById('modalStatus').focus();
}

function closeTrackingModal() {
    document.getElementById('trackingUpdateModal').classList.add('hidden');
}

// Handle form submission
document.getElementById('trackingUpdateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const shipmentId = document.getElementById('modalShipmentId').value;
    const status = document.getElementById('modalStatus').value;
    const location = document.getElementById('modalLocation').value;
    const notes = document.getElementById('modalNotes').value;
    const messageDiv = document.getElementById('modalMessage');
    const submitBtn = document.getElementById('modalSubmitBtn');
    
    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
    
    fetch('/tracking/update-status/' + shipmentId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            status: status,
            location: location,
            description: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        messageDiv.className = 'p-3 rounded-lg ' + (data.success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');
        messageDiv.textContent = data.message || (data.success ? '✅ Status updated successfully!' : '❌ Failed to update status');
        messageDiv.classList.remove('hidden');
        
        if (data.success) {
            // Reload page after 1.5 seconds
            setTimeout(() => {
                location.reload();
            }, 1500);
        }
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Update Status';
    })
    .catch(error => {
        messageDiv.className = 'p-3 rounded-lg bg-red-100 text-red-700';
        messageDiv.textContent = '❌ Error: ' + error.message;
        messageDiv.classList.remove('hidden');
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Update Status';
    });
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTrackingModal();
    }
});

// Close modal on click outside
document.getElementById('trackingUpdateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTrackingModal();
    }
});
</script>