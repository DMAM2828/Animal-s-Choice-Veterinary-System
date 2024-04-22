<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['doctor_id'])) {
    $doctor_id = $_SESSION['doctor_id'];
} elseif (isset($_SESSION['type']) && $_SESSION['type'] == 1) {
    // User is an admin, set $doctor_id to null or any other value that makes sense
    $doctor_id = null;
} else {
    echo "Error: 'doctor_id' or 'type' is not set in the session.";
    exit();
}

// Fetch appointments
if ($_SESSION['type'] == 1) {
    // Administrator, fetch all appointments
    $qry = $conn->query("SELECT * FROM `appointment_list` ORDER BY unix_timestamp(`date_created`) DESC");
} else {
    // Doctor, fetch appointments for the specific doctor
    $qry = $conn->query("SELECT * FROM `appointment_list` WHERE doctor_id = $doctor_id ORDER BY unix_timestamp(`date_created`) DESC");
}

$i = 1;
?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">List of Appointments</h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-hover table-striped table-bordered" id="appointmentsTable">
                <colgroup>
                    <col width="5%">
                    <col width="20%">
                    <col width="20%">
                    <col width="25%">
                    <col width="20%">
                    <col width="10%">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date Created</th>
                        <th>Code</th>
                        <th>Owner</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $qry->fetch_assoc()) : ?>
                        <tr>
                            <td class="text-center"><?php echo $i++; ?></td>
                            <td class=""><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
                            <td><?php echo ($row['code']) ?></td>
                            <td class=""><p class="truncate-1"><?php echo ucwords($row['owner_name']) ?></p></td>
                            <td class="text-center">
                                <?php
                                switch ($row['status']) {
                                    case 0:
                                        echo '<span class="rounded-pill badge badge-primary">Pending</span>';
                                        break;
                                    case 1:
                                        echo '<span class="rounded-pill badge badge-success">Confirmed</span>';
                                        break;
                                    case 3:
                                        echo '<span class="rounded-pill badge badge-danger">Cancelled</span>';
                                        break;
                                }
                                ?>
                            </td>
                            <td align="center">
                                <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                    Action
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item" href="./?page=appointments/view_details&id=<?php echo $row['id'] ?>" data-id=""><span class="fa fa-window-restore text-gray"></span> View</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.delete_data').click(function () {
            _conf("Are you sure to delete this appointment permanently?", "delete_appointment", [$(this).attr('data-id')])
        })

        $('#appointmentsTable').DataTable({
            "order": [[0, "asc"]],
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "columnDefs": [
                { "orderable": false, "targets": 5 }
            ]
        });
    });

    function delete_appointment($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_appointment",
            method: "POST",
            data: { id: $id },
            dataType: "json",
            error: err => {
                console.log(err)
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function (resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    location.reload();
                } else {
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
            }
        });
    }
</script>
