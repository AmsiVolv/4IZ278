<?php
$isAdmin = false;
$pageTitle = 'Reservation';
require_once 'inc/user.php';

#kontrolujeme pokud je adminem
if ($isAdmin) {
    $isAdmin=true;
}
#konec kontroly admina

include './inc/header.php';

$servicesQuery=$db->prepare('SELECT * FROM services_sem');
$servicesQuery->execute();
$services=$servicesQuery->fetchAll();


?>
<head>
    <title>s</title>
    <link href='./packages/core/main.css' rel='stylesheet' />
    <link href='./packages/daygrid/main.css' rel='stylesheet' />
    <link href='./packages/timegrid/main.css' rel='stylesheet' />
    <script src='./packages/core/main.js'></script>
    <script src='./packages/interaction/main.js'></script>
    <script src='./packages/daygrid/main.js'></script>
    <script src='./packages/timegrid/main.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            $("input").on( "click", function() {
               $('#radioValue').val(($("input:checked").val()));
            });

            var isAllDay = false;
            var today = moment().format('YYYY-MM-DD HH:mm:ss');
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridDay'
                },
                firstDay:1,
                slotLabelFormat: { hour: 'numeric', minute: '2-digit', omitZeroMinute: false, hour12: false },
                allDaySlot: false,
                allDayText:false,
                minTime: "07:00:00",
                maxTime: "16:00:00",
                events: 'cal-load.php',
                navLinks: true, // can click day/week names to navigate views
                selectable: true,
                selectOverlap: false,
                selectAllow: function(select) {
                    return moment().diff(select.start) <= 0
                },
                dateClick: function(info) {
                    if (today > info.dateStr){
                        calendar.changeView('timeGrid', today);
                    }else{
                        calendar.changeView('timeGrid', info.dateStr);
                    }
                },
                select: function(start, allDay, end) {
                    isAllDay = start['allDay'];
                    if(!isAllDay){
                        let startDB = moment(start['startStr']).format('YYYY-MM-DD HH:mm:ss');
                        let endDB = moment(start['endStr']).format('YYYY-MM-DD HH:mm:ss');
                            $('#startDate').val(startDB);
                            $('#endDate').val(endDB);
                            alert('Time period successfully selected');
                    }
                },
                editable: true,
                eventLimit: true // allow "more" link when too many events
            });
            calendar.render();
        });
    </script>
</head>
<div class="section-heading">
    <h2>Book now</h2>
    <div class="line"></div>
</div>
<div class="col-12 col-md-12">
    <!-- Form -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-6 col-xl-6">
                <div class="card shadow-lg o-hidden border-0 my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col">
                                <div class="p-5">
                                    <?php
                                    if(isset($_SESSION['errors'])){
                                        echo '
                                       <div class="alert alert-dismissable alert-warning">
                                       <h4 class="text-center">
                                             We found this mistakes!
                                        </h4>';
                                        foreach ($_SESSION['errors'] as $error){
                                            echo '<p class="text-danger">'.$error.'</p>';
                                        }
                                            unset($_SESSION['errors']);
                                        echo '</div>';
                                    }else{
                                        echo
                                        '
                                    <div class="text-center">
                                        <h4 class="text-dark mb-4">Book an appointment</h4>
                                    </div>
                                        ';
                                    }
                                    ?>
                                    <hr>
                                    <form action="cal-insert.php" method="post" name="calInsertForm">
                                        <!-- Button trigger modal -->
                                        <input type="hidden" name="csrf" value="<?php echo (getCSRF($_SERVER['PHP_SELF'])); ?>">
                                        <div class="form-group  text-center mb-lg-3 mt-lg-3">
                                            <button type="button" class="showMe btn btn-light" data-toggle="modal" data-target="#calendarModalWindow">
                                                Choose a date <i class="fa fa-calendar"></i>
                                            </button>
                                            <button type="button" class="showMe btn btn-light" data-toggle="modal" data-target="#serviceModalWindow">
                                                <input type="hidden" id="radioValue" name="serName" value="<?php echo htmlspecialchars(@$_POST['serName']);?>">
                                                Choose a service
                                            </button>
                                         <!-- Button trigger modal end -->
                                        </div>
                                        <div class="form-group text-center">
                                            <label>
                                                <button type="button" class="showMe btn btn-light" data-toggle="modal" data-target="#calendarModalWindow">
                                                    <input type="text" value="<?php echo htmlspecialchars(@$_POST['start']);?>" id="startDate" name="start" placeholder="Start date:" readonly required>
                                                </button>
                                            </label>
                                        </div>
                                        <div class="form-group text-center">
                                            <label>
                                                <button type="button" class="showMe btn btn-light" data-toggle="modal" data-target="#calendarModalWindow">
                                                    <input type="text" value="<?php echo htmlspecialchars(@$_POST['end']);?>" id="endDate" name="end" placeholder="End date:" readonly required>
                                                </button>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="description" id="description" class="form-control mb-30" placeholder="Maybe some additional information?" value="<?php echo htmlspecialchars(@$_POST['description']);?>"/>
                                        </div>
                                        <hr>
                                        <button name="submit" id="sumbit" class="btn btn-primary btn-block text-white btn-user" type="submit">Make a reservation</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Form end -->
</div>

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="calendarModalWindow">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id='calendar' class="col-12 col-md-12"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="calendarSave" data-dismiss="modal">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- First modal end -->
<!-- Second modal window -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="serviceModalWindow">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id='service' class="col-12 col-md-12">
                <div class="text-center">
                    <h4 class="text-dark mb-4">You can choose one service from this list</h4>
                </div>
                <form action="#" method="post">
                    <?php
                    foreach ($services as $service){
                        echo '
                           <div>
                            <input type="radio" name="service"  value="'.htmlspecialchars(@$service['name']).'" id="'.htmlspecialchars(@$service['name']).'">
                            <label for="'.htmlspecialchars(@$service['name']).'">'.htmlspecialchars(@$service['name']).'</label>
                          </div>
                        ';
                    }
                    ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- Second modal window end-->

<?php
include 'inc/footer.php';
?>
</body>
</html>
