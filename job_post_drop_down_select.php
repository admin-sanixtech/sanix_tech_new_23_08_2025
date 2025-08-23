<div class="card mb-3 p-3">
    <div class="row align-items-center">
        <div class="col-md-3">
            <label class="form-label">Filter by City:</label>
        </div>
        <div class="col-md-6">
            <select class="form-select" id="cityFilter" onchange="filterByCity(this.value)">
                <option value="">All Cities</option>
                <option value="mumbai">Mumbai</option>
                <option value="delhi">Delhi</option>
                <option value="bangalore">Bangalore</option>
                <option value="chennai">Chennai</option>
                <option value="kolkata">Kolkata</option>
            </select>
        </div>
    </div>
</div>

<script>
function filterByCity(city) {
    if(city) {
        console.log("Selected city: " + city);
        // Add your filtering logic here
    }
}
</script>