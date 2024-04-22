<?php
require_once('./config.php');
date_default_timezone_set('Asia/Manila');

$schedule = $_GET['schedule'] ?? date('Y-m-d'); 
$doctor_id = $_POST['doctor_id'] ?? 0; 

$qry = $conn->prepare("SELECT appointment_time FROM appointment_list WHERE schedule = ? AND doctor_id = ?");
$qry->bind_param("si", $schedule, $doctor_id);

if (!$qry->execute()) {
    die('Error executing the query: ' . $conn->error);
}

$booked_times = [];
$result = $qry->get_result();
while ($row = $result->fetch_assoc()) {
    $booked_times[] = $row['appointment_time'];
}

// Retrieve service IDs directly
$service_ids = [];
$services = $conn->prepare("SELECT id FROM service_list WHERE delete_flag = 0");
$services->execute();
$services->bind_result($service_id);
while ($services->fetch()) {
    $service_ids[] = $service_id;
}

?>

<div class="container-fluid">
    <form action="" id="appointment-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <input type="hidden" name="schedule" value="<?php echo isset($schedule) ? $schedule : '' ?>">
        <dl>
            <dt class="text-muted">Appointment Schedule</dt>
            <dd class=" pl-3"><b><?= date("F d, Y",strtotime($schedule)) ?></b></dd>
        </dl>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <fieldset>
                    <legend class="text-muted">Owner Information</legend>
                    <div class="form-group">
                        <label for="owner_name" class="control-label">Name</label>
                        <input type="text" name="owner_name" id="owner_name" class="form-control form-control-border" placeholder="Juan Dela Cruz" value ="<?php echo isset($owner_name) ? $owner_name : '' function validateName(name) {
    var nameRegex = /^[a-zA-Z]+\s[a-zA-Z]+$/;
    return nameRegex.test(name);
}
?>" required>
                    </div>
                    <div class="form-group">
                        <label for="contact" class="control-label">Contact</label>
                        <input type="text" name="contact" id="contact" class="form-control form-control-border" placeholder="09xxxxxxxx" value ="<?php echo isset($contact) ? $contact : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control form-control-border" placeholder="juan234@sample.com" value ="<?php echo isset($email) ? $email : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="address" class="control-label">Address</label>
                        <textarea type="email" name="address" id="address" class="form-control form-control-sm rounded-0" rows="3" placeholder="St,John, Guimba, Nueva Ecija, 3115" required><?php echo isset($address) ? $address : '' ?></textarea>
                    </div>
                    <div class="form-group">
    <label for="doctor_id" class="control-label">Select Doctor</label>
    <select id="doctor_id" name="doctor_id" class="form-control form-control-border">
        <?php
        $qry = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name FROM `users` WHERE type = 3 ORDER BY CONCAT(firstname, ' ', lastname) ASC");
        while ($row = $qry->fetch_assoc()):
            $selected = ($doctor_id == $row['id']) ? 'selected' : '';
        ?>
            <option value="<?php echo $row['id']; ?>" <?php echo $selected; ?>><?php echo $row['name']; ?></option>
        <?php endwhile; ?>
    </select>
</div>
<div class="form-group">
    <label for="appointment_time" class="control-label">Select Time</label>
    <select id="appointment_time_<?php echo $doctor_id; ?>" name="appointment_time" class="form-control form-control-border">
        <?php
        function isTimeBooked($selectedTime) {
            global $conn;
            
            $query = "SELECT COUNT(*) AS count FROM appointment_list WHERE appointment_time = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $selectedTime);
            $stmt->execute();
            $stmt->store_result(); // Store the result set
            $stmt->bind_result($count); // Bind the result to $count
            $stmt->fetch(); // Fetch the result
        
            return $count > 0;
        
}

// Function to book the appointment
function bookAppointment($selectedTime, $doctorId, $userId) {
    // Perform the actual booking operation in your backend
    // Example: Insert the appointment into your appointments table
    INSERT INTO appointments_list (doctor_id, user_id, appointment_time) VALUES ($doctorId, $userId, $selectedTime)
    // Replace this with your actual booking logic

    // Return true or false based on the success of the booking operation
    return false ;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data (e.g., owner name, contact number, email, address) in PHP if needed

    // Get form data
    $selectedTime = $_POST['appointment_time']; // Assuming you have a form field named 'appointment_time'
    $doctorId = $_POST['doctor_id']; // Assuming you have a form field named 'doctor_id'
    $userId = $_POST['user_id']; // Assuming you have a form field named 'user_id'

    // Check if the selected time is already booked
    if (isTimeBooked($selectedTime)) {
        echo json_encode(["status" => "error", "msg" => "This appointment time is already booked. Please choose another time."]);
        exit;
    }

    // Perform the actual booking
    if (bookAppointment($selectedTime, $doctorId, $userId)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "msg" => "Failed to book the appointment. Please try again later."]);
    }
}
    
        ?>
    </select>
</div>
                </fieldset>
            </div>
            <div class="col-md-6">
                <fieldset>
                    <legend class="text-muted">Pet Information</legend>
                    <div class="form-group">
                        <label for="category_id" class="control-label">Pet Type</label>
                        <select name="category_id" id="category_id" class="form-control form-control-border select2">
                            <option value="" selected disabled></option>
                            <?php 
                            $categories = $conn->query("SELECT * FROM category_list where delete_flag = 0 ".(isset($category_id) && !empty($category_id) ? " or id = '{$category_id}'" : "")." order by name asc");
                            while($row = $categories->fetch_assoc()):
                            ?>
                            <option value="<?= $row['id'] ?>" <?= isset($category_id) && in_array($row['id'],explode(',', $category_id)) ? "selected" : "" ?> <?= $row['delete_flag'] == 1 ? "disabled" : "" ?>><?= ucwords($row['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="breed" class="control-label">Breed</label>
                        <input type="text" name="breed" id="breed" class="form-control form-control-border" placeholder="Siberian Husky" value ="<?php echo isset($breed) ? $breed : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="age" class="control-label">Age</label>
                        <input type="text" name="age" id="age" class="form-control form-control-border" placeholder="1 yr. old" value ="<?php echo isset($age) ? $age : '' ?>" required>
                    </div>
                </fieldset>
                <div class="form-group">
                    <label for="service_id" class="control-label">Service(s)</label>
                    
                    <?php 
                        $services = $conn->query("SELECT * FROM service_list where delete_flag = 0 ".(isset($service_id) && !empty($service_id) ? " or id in ('{$service_id}')" : "")." order by name asc");
                        while($row = $services->fetch_assoc()){
                            unset($row['description']);
                            $service_arr[] = $row;
                        }
                        ?>
                    <select name="service_id[]" id="service_id" class="form-control form-control-border select2" multiple>
        <?php foreach ($service_arr as $service) : ?>
            <option value="<?= $service['id'] ?>"><?= $service['name'] ?></option>
        <?php endforeach; ?>
                    </select>
                    
                </div>
            </div>
        </div>
    </form>
</div>
<script>

// Function to validate owner name
function validateName(name) {
    var nameRegex = /^[a-zA-Z]+\s[a-zA-Z]+$/;
    return nameRegex.test(name);
}

// Function to validate contact number
function validateContact(contact) {
    var contactRegex = /^\d{11}$/;
    return contactRegex.test(contact);
}

// Function to validate email
function validateEmail(email) {
    var emailRegex = /^[^\s@]+@gmail\.com$/;
    return emailRegex.test(email);
}

// Function to validate address
function validateAddress(address) {
    var addressRegex = /(Brgy|Barangay),\s\S+,\s\S+/;
    return addressRegex.test(address);
}

// Function to validate time selection
function validateTime() {
    var currentTime = new Date();
    var currentHour = currentTime.getHours();
    var currentMinutes = currentTime.getMinutes();
    var selectedTime = $('#appointment_time_<?php echo $doctor_id; ?>').val();
    var selectedHour = parseInt(selectedTime.split(":")[0]);
    var selectedMinutes = parseInt(selectedTime.split(":")[1]);
    
    // Check if the selected time is in the past or already booked
    if ((selectedHour < currentHour) || (selectedHour === currentHour && selectedMinutes < currentMinutes)) {
        alert('Please select a valid future time slot.');
        return false;
    }

    // Additional validation if needed for booked times
    // Example: Check if the selected time is already booked
    // Replace this logic with your actual booking status check

    return true;
}

$(function () {
    $('#appointment-form').submit(function (event) {
        // Validate owner name
        var name = $('#owner_name').val();
        if (!validateName(name)) {
            alert('Please enter a valid first and last name with letters only.');
            event.preventDefault();
            return;
        }

        // Validate contact number
        var contact = $('#contact').val();
        if (!validateContact(contact)) {
            alert('Contact number must be 11 digits long with no special characters.');
            event.preventDefault();
            return;
        }

        // Validate email
        var email = $('#email').val();
        if (!validateEmail(email)) {
            alert('Please enter a valid Gmail address (e.g., example@gmail.com).');
            event.preventDefault();
            return;
        }

        // Validate address
        var address = $('#address').val();
        if (!validateAddress(address)) {
            alert('Address must include Barangay/Brgy, Municipality, and Province.');
            event.preventDefault();
            return;
        }

        // Validate time selection
        if (!validateTime()) {
            event.preventDefault();
            return;
        }
    });
});
</script>