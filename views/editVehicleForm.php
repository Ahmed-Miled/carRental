<form id="editVehicleForm" action="/carRental/controller/manipuleVehicle.php" method="POST">
    <input type="hidden" name="id" value="<?= htmlspecialchars($car['id']) ?>">
    <input type="hidden" name="action" value="edit">
    <div class="mb-3">
        <label>Model</label>
        <input type="text" class="form-control" name="model" value="<?= htmlspecialchars($car['model']) ?>" readonly>
    </div>
    <div class="mb-3">
        <label>Price</label>
        <input type="number" class="form-control" name="price" value="<?= htmlspecialchars($car['price']) ?>">
    </div>
    <button type="submit" class="btn btn-success">Save Changes</button>
</form>
