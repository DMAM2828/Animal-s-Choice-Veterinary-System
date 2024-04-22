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
$services = $conn->query("SELECT id FROM service_list where delete_flag = 0");
while ($row = $services->fetch_assoc()) {
    $service_ids[] = $row['id'];
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
                        <input type="text" name="owner_name" id="owner_name" class="form-control form-control-border" placeholder="Juan Dela Cruz" value ="<?php echo isset($owner_name) ? $owner_name : '' ?>" required>
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
        $current_time = time(); // Current timestamp
        $end_of_day = strtotime('today 17:00'); // 5:00 PM of the current day

        // If the current time is after 5:00 PM, consider the next day
        if ($current_time >= $end_of_day) {
            $current_time = strtotime('tomorrow 9:00'); // Start from 9:00 AM of the next day
        }

        // Check if the selected date is today
        $selected_date = isset($_POST['selected_date']) ? $_POST['selected_date'] : date('Y-m-d');
        $is_today = ($selected_date == date('Y-m-d'));

        // Loop from 9:00 AM to 5:00 PM
        for ($hour = 9; $hour <= 17; $hour++) {
            $formatted_hour = ($hour % 12 == 0) ? 12 : $hour % 12;
            $suffix = ($hour < 12) ? 'AM' : 'PM';

            $option_time = strtotime("$formatted_hour:00 $suffix", $current_time);

            // Check if the option time is in the future and apply restrictions accordingly
            if ($option_time > $current_time || !$is_today) {
                echo "<option value=\"" . date('H:i', $option_time) . "\">" . date('h:i A', $option_time) . "</option>";
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
    var service = $.parseJSON('<?= json_encode($service_arr) ?>') || {};
    $(function(){
        $('#uni_modal').on('shown.bs.modal',function(){
            $('#category_id').select2({
                placeholder:"Please Select Pet Type here.",
                width:'100%',
                dropdownParent:$('#uni_modal')
            })
            $('#service_id').select2({
                placeholder:"Please Select Sevice(s) Here.",
                width:'100%',
                dropdownParent:$('#uni_modal')
            })
        })
        $('#category_id').change(function(){
            var id = $(this).val()
            $('#service_id').html('')
            $('#service_id').select2('destroy')
            Object.keys(service).map(function(k){
                if($.inArray(id,service[k].category_ids.split(',')) > -1 ){

                    var opt = $("<option>")
                        opt.val(service[k].id)
                        opt.text(service[k].name)
                    $('#service_id').append(opt)
                }
            })
            $('#service_id').select2({
                placeholder:"Please Select Sevice(s) Here.",
                width:'100%',
                dropdownParent:$('#uni_modal')
            })
            $('#service_id').val('').trigger('change')
        });
        $('#appointment-form').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            var appointment_time = $('#appointment_time_<?php echo $doctor_id; ?>').val();

            // Check if the selected time is already booked
            if ($.inArray(appointment_time, <?= json_encode($booked_times) ?>) !== -1) {
                alert('This appointment time is already booked. Please choose another time.');
                return;
            }

            // Proceed with form submission if the time is available
            $('.pop-msg').remove();
            var el = $('<div>');
            el.addClass("pop-msg alert");
            el.hide();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_appointment",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: function(err) {
                    console.log(err);
                    alert_toast(err.responseText, 'error'); // Display the error message from the server
                    end_loader();
                },
                success: function(resp){
                    if(resp.status == 'success'){
                        end_loader();
                        setTimeout(() => {
                            uni_modal("Success", "success_msg.php?code=" + resp.code);
                        }, 750);
                    } else if(!!resp.msg){
                        el.addClass("alert-danger");
                        el.text(resp.msg);
                        _this.prepend(el);
                    } else {
                        el.addClass("alert-danger");
                        el.text("An error occurred due to an unknown reason.");
                        _this.prepend(el);
                    }
                    el.show('slow');
                    $('html,body,.modal').animate({scrollTop: 0}, 'fast');
                    end_loader();
        }
    });
});
});
</script>