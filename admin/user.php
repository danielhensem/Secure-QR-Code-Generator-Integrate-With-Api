<?php include "../componet/conn.php"; ?>
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> User account has been created.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php
session_start();
if (!isset($_SESSION["adminlogin"]) || $_SESSION["adminlogin"] == "adlogout") {
    header("location:admin-login.php");
}
?>
<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="admin-style.css">


    <style>
        table {
            width: 70%;
            align-items: center;
            margin-top: 50px;
            margin-bottom: 30px;
            /* background-color: #333; */
            color: black;
            font-size: 20px;
            /* box-shadow: 15px 15px 4px yellowgreen; */
        }

        .t_hading {
            height: 40px;
            text-align: center;
        }

        .t_hading th {
            text-align: center;
            border-bottom: 1px rgb(217, 220, 219);
            font-size: 15px;
        }

        .t_body tr td {
            padding: 10px 30px;
            text-align: center;
            border-bottom: 0.1px rgb(217, 220, 219) solid;
            font-size: 10px;
        }

        .t_body tr td i {
            padding: 10px 15px;
            color: blue;
        }
    </style>
</head>

<body>

    <body id="body-pd">
        <nav>
            <header class="header" id="header">
                <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
                <div class="header-title">
                    <h3>Manage User Account</h3>
                </div>
                <div>Welcome <?php echo $_SESSION["name"] ?></div>
            </header>
            <div class="l-navbar" id="nav-bar">
                <nav class="nav">
                    <div>
                        <a href="admin-dashboard.php" class="nav_logo"> <i class='bx bx-layer nav_logo-icon'></i> <span
                                class="nav_logo-name">SQ-Tech SOLVER</span> </a>
                        <div class="nav_list">
                            <a href="admin-dashboard.php" class="nav_link "> <i class='bx bx-grid-alt nav_icon'></i>
                                <span class="nav_name">Dashboard</span> </a>
                            <a href="user.php" class="nav_link active"> <i class='bx bx-user nav_icon'></i> <span
                                    class="nav_name">Users</span> </a>
                            
                            <a href="category.php" class="nav_link"> <i class='bx bx-bookmark nav_icon'></i> <span
                                    class="nav_name">Info</span> </a>
                            

                        </div>
                    </div>
                    <a href="admin-componet/logout.php" class="nav_link"> <i class='bx bx-log-out nav_icon'></i> <span
                            class="nav_name">SignOut</span> </a>
                </nav>
            </div>
        </nav>
        <!--Container Main start-->

        <!-- <div class="container"> -->

        <div class="container py-5">
            <div class="row">
                <div class="col-lg-9">
                    <div class="user-subnav"
                        style="display:flex;justify-content: left; width: max-content; border-radius:40px margin-top:40px;">
                        <div class="navlinks" style="color:red;">
                            <a href="user.php" class="active" style="font-size:19px;">User</a>
                            <a href="admin.php" style="font-size:19px;">Admin</a>
                        </div>
                    </div>
                    <!-- </div>         -->


                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css"
                        rel="stylesheet">
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script
                        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>


                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button class="btn"
                            style="margin-top:10px; border-radius:40px;width:18%; background-color:black; color:white;"
                            data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fa-solid fa-plus"></i> Add Account
                        </button>
                    </div>

                    <div
                        style="display:flex; font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; font-weight: bold; font-size: 34px;">
                        <p>
                            <?php
                            $result = $con->query("SELECT COUNT(*) as total FROM users");
                            $row = $result->fetch_assoc();
                            echo "Total Users: " . $row['total'];
                            ?>
                        </p>
                    </div>
                    <div style=" font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size:15px; font-weight: 35px; margin-right:40px;">Welcome Admin to the User Management System. You are allowed to add new user by clicking the add user button.
                    Then, you are allowed to view user details include how many they has generate, share, receive, being friend and numbers of accessed qr code.
                    You can choose whether to delete or disable the user's account. Finally, you can interact with user with send notification to them.</p></div>



                    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="addUserForm" method="POST" action="adduser.php">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="userName" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="userName" name="name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="userEmail" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="userEmail" name="email"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="userPassword" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="userPassword"
                                                name="password" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="userStatus" class="form-label">Status</label>
                                            <select class="form-select" id="userStatus" name="status">
                                                <option value="0">Active</option>
                                                <option value="1">Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Add Account</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <h4 class="mb-3 text-center" style="margin-top:70px;">Notification</h4>
                    <div class="dashboard p-3"
                        style=" height:240px; background-color:darkgrey; box-shadow: 10px -10px 50px darkgrey; border-radius: 40px;">

                        <!-- Label and input -->
                        <div class="mb-3">
                            <label for="sendToUser" class="form-label fw-bold">Send To:</label>
                            <input type="text" id="sendToUser" name="user_id" class="form-control"
                                placeholder="Enter User ID">

                            <textarea id="sendMessage" name="message" style="margin-top:5px;" class="form-control"
                                rows="4" placeholder="Enter Message"></textarea>

                            <br>
                            <!-- Buttons in one line -->
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-sm" id="sendBtn"
                                    style="border-radius:20px">Send</button>
                                <button type="button" class="btn btn-success btn-sm" id="sendAllBtn"
                                    style="border-radius:20px">Send to All</button>

                            </div>
                        </div>

                    </div>
                </div>
                <div class="container py-5">
                    <div class="row">
                        <!-- Left: Users Table (75%) -->
                        <div class="col-lg-9">
                            <div class="table-responsive shadow-sm rounded"
                                style="max-height: 700px; overflow-y: auto;">
                                <table class="table table-hover align-middle text-center" id="usersTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="font-size:17px;">ID</th>
                                            <th style="font-size:17px;" >Name</th>
                                            <th style="font-size:17px;">Email</th>
                                            <th style="font-size:17px;">Status</th>
                                            <th style="font-size:17px;">View</th>
                                            <th style="font-size:17px;">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $select = "SELECT * FROM users "; // limit 10 rows
                                        $q1 = mysqli_query($con, $select);
                                        while ($rec = mysqli_fetch_array($q1)) {
                                            $userId = htmlspecialchars($rec['id']);
                                            ?>
                                            <tr data-userid="<?= $userId ?>">
                                                <td style="font-size:14px;"><?= $userId ?></td>
                                                <td style="font-size:14px;"><?= htmlspecialchars($rec['name']); ?></td>
                                                <td style="font-size:14px;"><?= htmlspecialchars($rec['email']); ?></td>
                                                <td >
                                                    <a href="up-ins-del/dis-enb.php?uid=<?= $userId ?>">
                                                        <button type="button"
                                                            class="btn btn-<?= ($rec['status'] == 1) ? 'danger' : 'success' ?> btn-sm">
                                                            <?= ($rec['status'] == 1) ? 'Disable' : 'Enable' ?>
                                                        </button>
                                                    </a>
                                                </td>
                                                <td>
                                                    <button class="btn btn-info btn-sm viewProfileBtn">View Profile</button>
                                                </td>
                                                <td>
                                                    <button class="btn btn-danger btn-sm DeleteBtn"
                                                        data-userid="<?= $userId ?>">Delete</button>

                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Right: Account Dashboard (25%) -->
                        <div class="col-lg-3">
                            <h4 class="mb-3 text-center">Account Dashboard</h4>

                            <div class="row g-2"> <!-- vertical gap between boxes -->

                                <div class="col-12">
                                    <div class="p-3 bg-primary text-white rounded shadow text-center">
                                        <h6>ID</h6>
                                        <p id="boxId">-</p>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="p-3 bg-secondary text-white rounded shadow text-center text-break">
                                        <h6>Name</h6>
                                        <p id="boxName" class="text-break">-</p>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="p-3 bg-success text-white rounded shadow text-center text-break">
                                        <h6>Email</h6>
                                        <p id="boxEmail" class="text-break">-</p>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="p-3 bg-warning text-white rounded shadow text-center">
                                        <h6>QR Generated</h6>
                                        <p id="boxGenerated">0</p>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="p-3 bg-info text-white rounded shadow text-center">
                                        <h6>QR Shared</h6>
                                        <p id="boxShared">0</p>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="p-3 bg-dark text-white rounded shadow text-center">
                                        <h6>QR Received</h6>
                                        <p id="boxReceived">0</p>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="p-3 bg-danger text-white rounded shadow text-center">
                                        <h6>Friends</h6>
                                        <p id="boxFriends">0</p>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="p-3 bg-secondary text-white rounded shadow text-center">
                                        <h6>QR Accessed</h6>
                                        <p id="boxAccessed">0</p>
                                    </div>
                                </div>

                            </div> <!-- row g-2 -->
                        </div>

                    </div>
                </div>





                <!-- jQuery AJAX Script -->
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js for mini chart -->

                <script>

                    $(document).ready(function () {
                        // Normal send
                        $('#sendBtn').click(function () {
                            let userId = $('#sendToUser').val();
                            let message = $('#sendMessage').val();

                            if (userId.trim() === "" || message.trim() === "") {
                                alert("⚠ Please fill in both fields.");
                                return;
                            }

                            $.post("send_notification.php", { user_id: userId, message: message }, function (response) {
                                if (response === "success_single") {
                                    alert("✅ Notification sent successfully!");
                                    location.reload();
                                } else {
                                    alert("❌ Failed to send notification.");
                                }
                            });
                        });

                        // Send to All
                        $('#sendAllBtn').click(function () {
                            let userId = $('#sendToUser').val();
                            let message = $('#sendMessage').val();

                            if (userId.toLowerCase() !== "all") {
                                alert("⚠ To send to all, please type 'all' in the User ID field.");
                                return;
                            }

                            if (message.trim() === "") {
                                alert("⚠ Message cannot be empty.");
                                return;
                            }

                            $.post("send_notification.php", { user_id: userId, message: message }, function (response) {
                                if (response === "success_all") {
                                    alert("✅ Notification sent to ALL users!");
                                    location.reload();
                                } else {
                                    alert("❌ Failed to send to all users.");
                                }
                            });
                        });
                    });


                    $(document).ready(function () {
                        $('#addUserForm').submit(function (e) {
                            e.preventDefault(); // prevent default form submit

                            var formData = $(this).serialize(); // get form data

                            $.ajax({
                                url: 'adduser.php', // backend file
                                method: 'POST',
                                data: formData,
                                dataType: 'json', // expect JSON from backend
                                success: function (response) {
                                    if (response.success) {
                                        // Close modal
                                        $('#addUserModal').modal('hide');

                                        // Show success alert
                                        $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                            'User added successfully!' +
                                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                            '</div>').prependTo('body');

                                        // Optional: reset form
                                        $('#addUserForm')[0].reset();

                                        // Optional: reload user table
                                        location.reload(); // reload page to reflect changes
                                    } else {
                                        alert(response.error || "Error adding user");
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error(xhr.responseText);
                                    alert('Unexpected error occurred!');
                                }
                            });
                        });
                    });

                    $(document).ready(function () {
                        $('.DeleteBtn').click(function () {
                            var userId = $(this).data('userid'); // assuming each button has data-userid
                            if (!userId) {
                                alert("User ID not found!");
                                return;
                            }

                            if (confirm("Are you sure you want to delete this user? This action cannot be undone.")) {
                                $.ajax({
                                    url: 'delete_user.php', // a separate PHP file to handle deletion (code 3)
                                    type: 'POST',
                                    data: { terminateAccount: 1, user_id: userId },
                                    dataType: 'json', // important!
                                    success: function (response) {
                                        // assuming response = JSON { success: true }
                                        if (response.success) {
                                            alert("User deleted successfully!");
                                            location.reload(); // reload page to reflect changes
                                        } else {
                                            alert("Error deleting user: " + (response.error || "Unknown error"));
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error(xhr.responseText);
                                        alert("AJAX error: " + error);
                                    }
                                });
                            }
                        });



                        $('.viewProfileBtn').click(function () {
                            var tr = $(this).closest('tr');
                            var userId = tr.data('userid'); // get the data attribute
                            console.log("Clicked userId:", userId); // debug

                            if (!userId) {
                                alert("User ID not found!");
                                return;
                            }

                            var range = 'all';

                            $.ajax({
                                url: 'fetch_data.php',
                                method: 'POST',
                                data: { user_id: userId, range: 'all' },
                                dataType: 'json',
                                success: function (data) {
                                    if (!data || data.error) {
                                        alert(data.error || "No data received");
                                        return;
                                    }

                                    // --- Update user info boxes ---
                                    $('#boxId').text(data.user.id || '');
                                    $('#boxName').text(data.user.name || '');
                                    $('#boxEmail').text(data.user.email || '');
                                    $('#boxGenerated').text(data.generated || 0);
                                    $('#boxShared').text(data.shared || 0);
                                    $('#boxReceived').text(data.received || 0);
                                    $('#boxFriends').text(data.friends || 0);

                                    var totalAccessed = 0;
                                    if (data.accessed && Array.isArray(data.accessed)) {
                                        totalAccessed = data.accessed.reduce((sum, item) => sum + (item.count || 0), 0);
                                    }
                                    $('#boxAccessed').text(totalAccessed);

                                    drawAccessChart(data.accessed || []);
                                },
                                error: function (xhr, status, error) {
                                    console.error("AJAX Error:", status, error);
                                    console.log(xhr.responseText);
                                }
                            });
                        });

                        // function drawAccessChart(accessedData){
                        //     if($('#dashboardBoxes').length === 0) return;

                        //     if($('#accessChart').length === 0){
                        //         $('#dashboardBoxes').append('<canvas id="accessChart" height="100"></canvas>');
                        //     }

                        //     var ctx = document.getElementById('accessChart').getContext('2d');
                        //     if(window.accessChartInstance) window.accessChartInstance.destroy();

                        //     var labels = accessedData.map(item => item.date || '');
                        //     var counts = accessedData.map(item => item.count || 0);

                        //     window.accessChartInstance = new Chart(ctx, {
                        //         type: 'line',
                        //         data: {
                        //             labels: labels,
                        //             datasets: [{
                        //                 label: 'QR Accessed',
                        //                 data: counts,
                        //                 backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        //                 borderColor: 'rgba(54, 162, 235, 1)',
                        //                 borderWidth: 2,
                        //                 fill: true,
                        //                 tension: 0.3
                        //             }]
                        //         },
                        //         options: {
                        //             responsive: true,
                        //             maintainAspectRatio: false,
                        //             plugins: { legend: { display: false } },
                        //             scales: { y: { beginAtZero: true } }
                        //         }
                        //     });
                        // }

                    });
                </script>


                <!--Container Main end-->

                <script>
                    document.addEventListener("DOMContentLoaded", function (event) {

                        const showNavbar = (toggleId, navId, bodyId, headerId) => {
                            const toggle = document.getElementById(toggleId),
                                nav = document.getElementById(navId),
                                bodypd = document.getElementById(bodyId),
                                headerpd = document.getElementById(headerId)

                            // Validate that all variables exist
                            if (toggle && nav && bodypd && headerpd) {
                                toggle.addEventListener('click', () => {
                                    // show navbar
                                    nav.classList.toggle('show')
                                    // change icon
                                    toggle.classList.toggle('bx-x')
                                    // add padding to body
                                    bodypd.classList.toggle('body-pd')
                                    // add padding to header
                                    headerpd.classList.toggle('body-pd')
                                })
                            }
                        }

                        showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header')

                        /*===== LINK ACTIVE =====*/
                        const linkColor = document.querySelectorAll('.nav_link')

                        function colorLink() {
                            if (linkColor) {
                                linkColor.forEach(l => l.classList.remove('active'))
                                this.classList.add('active')
                            }
                        }
                        linkColor.forEach(l => l.addEventListener('click', colorLink))

                    });

                    function checkdel() {
                        if (confirm('Are you sure you want to delete user?')) {
                            return true
                        } else {
                            return false
                        }

                    }
                </script>

    </body>

</html>



<?php $con->close(); ?>