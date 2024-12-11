<!-- Edit Device Log Modal -->
<div class="modal fade" id="editDeviceLogModal" tabindex="-1" aria-labelledby="editDeviceLogModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDeviceLogModalLabel">Add Device Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateDeviceLogForm">
                    <input type="hidden" class="form-control" id="log_id" name="id">
                    
                    <div class="mb-3">
                        <label for="device_id" class="form-label">Device ID</label>
                        <input type="text" class="form-control" id="device_id" name="device_id">
                    </div>

                    <div class="mb-3">
                        <label for="time" class="form-label">Time</label>
                        <input type="datetime-local" class="form-control" id="time" name="time">
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-control" id="type" name="type">
                            <option value="CheckIn">CheckIn</option>
                            <option value="CheckOut">CheckOut</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="imported" class="form-label">Imported</label>
                        <input type="number" class="form-control" id="imported" name="imported" min="0" max="1">
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date">
                    </div>

                    <div class="mb-3">
                    </div>

                    <button  type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
