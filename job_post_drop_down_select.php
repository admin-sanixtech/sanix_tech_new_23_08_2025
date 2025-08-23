<div class="card mb-3 p-3">
    <div class="row align-items-center">
        <div class="col-md-2">
            <label class="form-label">Filter by City:</label>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="cityFilter" onchange="filterByCity(this.value)">
                <option value="">All Cities</option>
                <option value="mumbai">Mumbai</option>
                <option value="delhi">Delhi</option>
                <option value="bangalore">Bangalore</option>
                <option value="chennai">Chennai</option>
                <option value="kolkata">Kolkata</option>
                <option value="Hyderabad">Hyderabad</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Filter by Role:</label>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="roleFilter" onchange="filterByRole(this.value)">
                <option value="">All Roles</option>
                <option value="developer">Developer</option>
                <option value="designer">Designer</option>
                <option value="manager">Manager</option>
                <option value="analyst">Analyst</option>
                <option value="tester">Tester</option>
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

function filterByRole(role) {
    if(role) {
        console.log("Selected role: " + role);
        // Add your filtering logic here
    }
}
</script>