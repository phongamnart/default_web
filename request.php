<?php
include("_check_session.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $document = 2;
    $ismenu = 1;
    $condition2 = "";
    $current_menu = "documents";
    include_once('_head.php');
    $conDB = new db_conn();
    $current_time = date('Y-m-d');
    $convertDate = strtotime($current_time);
    $newCurrentTime = date("d-m-Y", $convertDate);

    $req_discipline = isset($_SESSION['req_discipline']) ? $_SESSION['req_discipline'] : '';
    $req_work = isset($_SESSION['req_work']) ? $_SESSION['req_work'] : '';
    $req_type = isset($_SESSION['req_type']) ? $_SESSION['req_type'] : '';

    if ($req_discipline != "") {
        $condition = " AND `discipline` = '" . $req_discipline . "'";
    } else {
        $condition = "";
    }
    if ($req_work != "") {
        $condition2 = " AND `work` = '" . $req_work . "'";
        $condition .= $condition2;
    } else {
        $condition .= "";
    }
    if ($req_type != "") {
        $condition2 .= " AND `type` = '" . $req_type . "'";
        $condition .= $condition2;
    } else {
        $condition .= "";
    }
    $mail = $_SESSION['user_mail'];
    $sql = "SELECT * FROM `approval` WHERE `mail` = '$mail'";
    $result = $conDB->sqlQuery($sql);
    while ($obj = mysqli_fetch_assoc($result)) {
        if ($obj['role'] == 'ADMIN') {
            $strSQL = "SELECT `documents`.*, `request`.*, `documents`.`id` AS `id`, `request`.`id` as `reqID` FROM `documents` LEFT JOIN `request` ON `documents`.`doc_no` = `request`.`doc_no`
            WHERE `documents`.`admin` = 1 AND `documents`.`enable` = 1" . $condition;
            $objQuery = $conDB->sqlQuery($strSQL);
        } else {
            $strSQL = "SELECT `documents`.*, `request`.*, `documents`.`id` AS `id`, `request`.`id` as `reqID` FROM `documents` LEFT JOIN `request` ON `documents`.`doc_no` = `request`.`doc_no`
            WHERE `documents`.`enable` = 1 AND `request`.`createdby` = '$mail'" . $condition;
            $objQuery = $conDB->sqlQuery($strSQL);
        }
    }

    ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include_once('_navbar.php'); ?>
        <?php include_once('_menu.php'); ?>
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>History request</h1>
                        </div>
                    </div>
                </div>
            </section>
            <section class="content">
                <div>
                    <button type="button" class="btn btn-app flat" onclick="window.location.href='documents.php'" title="<?php echo BTN_DISCARD; ?>">
                        <img src="dist/img/icon/multiply.svg" style="padding:3px;" width="24"><br>
                        <?php echo BTN_DISCARD; ?>
                    </button>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Discipline <em></em></label>
                                                <select class="custom-select" onchange="setFilter('req_discipline',this.value)">
                                                    <option value="" <?php if ($req_discipline == '') {
                                                                            echo "selected";
                                                                        } ?>>All</option>
                                                    <?php
                                                    $sql2 = "SELECT DISTINCT `discipline` FROM `documents` WHERE `createdby` = '$mail'";
                                                    $objQueryDisc = $conDB->sqlQuery($sql2);

                                                    while ($objResult = mysqli_fetch_assoc($objQueryDisc)) { ?>
                                                        <option value="<?php echo $objResult['discipline']; ?>" <?php if ($req_discipline == $objResult['discipline']) {
                                                                                                                    echo "selected";
                                                                                                                } ?>>
                                                            <?php echo $objResult['discipline']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Work <em></em></label>
                                                <select class="custom-select" onchange="setFilter('req_work',this.value)">
                                                    <option value="" <?php if ($req_work == '') {
                                                                            echo "selected";
                                                                        } ?>>All</option>
                                                    <?php
                                                    if ($req_discipline != "") {
                                                        $condition2 = " AND `discipline` = '$req_discipline'";
                                                    }
                                                    $sql2 = "SELECT DISTINCT `work` FROM `documents` WHERE `createdby` = '$mail'" . $condition2;
                                                    $objQueryWork = $conDB->sqlQuery($sql2);

                                                    while ($objResult = mysqli_fetch_assoc($objQueryWork)) { ?>
                                                        <option value="<?php echo $objResult['work']; ?>" <?php if ($req_work == $objResult['work']) {
                                                                                                                echo "selected";
                                                                                                            } ?>>
                                                            <?php echo $objResult['work']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Type <em></em></label>
                                                <select class="custom-select" onchange="setFilter('req_type',this.value)">
                                                    <option value="" <?php if ($req_type == '') {
                                                                            echo "selected";
                                                                        } ?>>All</option>
                                                    <?php
                                                    if ($req_work != "") {
                                                        $condition2 = " AND `work` = '$req_work'";
                                                    }
                                                    $sql2 = "SELECT DISTINCT `type` FROM `documents` WHERE `createdby` = '$mail'" . $condition2;
                                                    $objQueryType = $conDB->sqlQuery($sql2);

                                                    while ($objResult = mysqli_fetch_assoc($objQueryType)) { ?>
                                                        <option value="<?php echo $objResult['type']; ?>" <?php if ($req_type == $objResult['type']) {
                                                                                                                echo "selected";
                                                                                                            } ?>>
                                                            <?php echo $objResult['type']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="datatable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th width="30">No.<br><em></em></th>
                                                <th width="30">Tools<br><em></em></th>
                                                <th width="80">Discipline​<br><em></em></th>
                                                <th width="90">Document No.​<br><em></em></th>
                                                <th width="500">Document Title<br><em></em></th>
                                                <th width="80">Date<br><em></em></th>
                                                <th width="200">Create By<br><em></em></th>
                                                <th width="150">Status<br><em></em></th>
                                                <th width="150">Type of request<br><em></em></th>
                                                <th width="500">Reject reason<br><em></em></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $index = 1;
                                            while ($objResult = mysqli_fetch_assoc($objQuery)) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $index++; ?></td>
                                                    <td align="center">
                                                        <?php if ($objResult['type_request'] == 'Download') { ?>
                                                            <?php if ($objResult['status_req'] == 2) { ?>
                                                                <?php
                                                                $expire = $objResult['expire'];
                                                                $convertDate = strtotime($expire);
                                                                $newExpire = date("d-m-Y", $convertDate);
                                                                ?>
                                                                <?php if ($newCurrentTime < $newExpire) { ?>
                                                                    <img src="dist/img/icon/doc.png" onclick="window.location.href='download.php?no=<?php echo md5($objResult['id']) ?>'" title="Word" width="35" style="padding: 5px;cursor: pointer;" />
                                                                <?php } else { ?>
                                                                    <img src="dist/img/icon/doc.png" onclick="" title="Word" width="35" style="padding: 5px;cursor: not-allowed;opacity:0.2;" />
                                                                <?php } ?>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?php echo $objResult['discipline'] ?></td>
                                                    <td><?php echo $objResult['doc_no'] ?></td>
                                                    <td><?php echo $objResult['method_statement'] ?></td>
                                                    <td>
                                                        <?php if ($objResult['type_request'] == 'Download') { ?>
                                                            <?php
                                                            $date = $objResult['date_req'];
                                                            $convertDate = strtotime($date);
                                                            $newDate = date("d-m-Y", $convertDate);
                                                            echo $newDate;
                                                            ?>
                                                        <?php } elseif ($objResult['type_request'] == 'Delete') { ?>
                                                            <?php
                                                            $date = $objResult['date_delete'];
                                                            $convertDate = strtotime($date);
                                                            $newDate = date("d-m-Y", $convertDate);
                                                            echo $newDate;
                                                            ?>
                                                        <?php } else { ?>
                                                            <?php
                                                            $date = $objResult['date_revise'];
                                                            $convertDate = strtotime($date);
                                                            $newDate = date("d-m-Y", $convertDate);
                                                            echo $newDate;
                                                            ?>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?php echo $objResult['createdby'] ?></td>
                                                    <td>
                                                        <?php if ($objResult['type_request'] == 'Download') { ?>
                                                            <?php if ($objResult['status_req'] == 2) {
                                                                $expire = $objResult['expire'];
                                                                $convertDate = strtotime($expire);
                                                                $newExpire = date("d-m-Y", $convertDate);
                                                                if ($newCurrentTime > $newExpire) {
                                                                    $exp = "UPDATE `request` SET `status_req` = '0'";
                                                                    $conDB->sqlQuery($exp);
                                                                } else {
                                                                    echo "Expire " . $newExpire;
                                                                }
                                                            ?>
                                                            <?php } elseif ($objResult['status_req'] == 1) {
                                                                $class = 'class="text-primary"' ?>
                                                                <span <?php echo $class ?>>Wait approve</span>
                                                            <?php } else {
                                                                $class = 'class="text-danger"' ?>
                                                                <span <?php echo $class ?>>Not approved</span>
                                                            <?php } ?>
                                                        <?php } elseif ($objResult['type_request'] == 'Delete') { ?>
                                                            <?php if ($objResult['status_del'] == 2) {
                                                                $class = 'class="text-success"' ?>
                                                                <span <?php echo $class ?>>Approved</span>
                                                            <?php } elseif ($objResult['status_del'] == 1) {
                                                                $class = 'class="text-primary"' ?>
                                                                <span <?php echo $class ?>>Wait approve</span>
                                                            <?php } else {
                                                                $class = 'class="text-danger"' ?>
                                                                <span <?php echo $class ?>>Not approved</span>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <?php if ($objResult['status_rev'] == 2) {
                                                                $class = 'class="text-success"' ?>
                                                                <span <?php echo $class ?>>Approved</span>
                                                            <?php } elseif ($objResult['status_rev'] == 1) {
                                                                $class = 'class="text-primary"' ?>
                                                                <span <?php echo $class ?>>Wait approve</span>
                                                            <?php } else {
                                                                $class = 'class="text-danger"' ?>
                                                                <span <?php echo $class ?>>Not approved</span>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?php echo $objResult['type_request'] ?></td>
                                                    <td><?php echo $objResult['comment'] ?></td>
                                                <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php include_once('_footer.php'); ?>
    </div>
    <!-- ./wrapper -->
    <?php include_once('_script.php'); ?>
    <script>
        setTimeout(function() {
                $('#datatable').DataTable({
                    "stateSave": true,
                    "paging": true,
                    "responsive": true,
                    "lengthChange": true,
                    "searching": true,
                    "autoWidth": true,
                    "ordering": true,
                    "info": true,
                    "scrollX": true,
                });
            },
            500);
    </script>

</html>