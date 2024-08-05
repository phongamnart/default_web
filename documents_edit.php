<?php
include("_check_session.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $documents  = 0;
    $doc_type  = "documents";
    $ismenu = 1;
    $current_menu = "documents";
    $get_id = isset($_GET['no']) ? $_GET['no'] : '';
    $get_mode = isset($_GET['m']) ? $_GET['m'] : '';
    include_once('_head.php');
    $conDB = new db_conn();
    $from = $_SESSION['user_name'];
    $strSQL = "SELECT * FROM `documents` WHERE md5(`id`) = '$get_id' LIMIT 1";
    $objQuery = $conDB->sqlQuery($strSQL);
    while ($objResult = mysqli_fetch_assoc($objQuery)) {
        $doc_id = $objResult['id'];
        $doc_no = $objResult['doc_no'];
        $discipline = $objResult['discipline'];
        $work = $objResult['work'];
        $type = $objResult['type'];
        $method_statement = $objResult['method_statement'];
        $preparedby = $objResult['preparedby'];
        $checkedby = $objResult['checkedby'];
        $remark = $objResult['remark'];
        $approved = $objResult['approved'];
        if ($objResult['date'] != "") {
            $date = date("d/m/Y", strtotime($objResult['date']));
        }
    }
    $strSQL = "SELECT `documents_line`.`id` AS `id`,`contents`.`name` 
    FROM `documents_line` LEFT JOIN `contents` ON `documents_line`.`content_id` = `contents`.`id` 
    WHERE md5(`doc_id`) = '$get_id' AND `documents_line`.`enable` = 1 ORDER BY `documents_line`.`content_id` ASC";
    $objQuery_line = $conDB->sqlQuery($strSQL);
    $check_content = $conDB->sqlNumrows($strSQL);

    $strSQL2 = "SELECT * FROM `approval` WHERE `mail` = '$checkedby' ";
    $objQuery2 = $conDB->sqlQuery($strSQL2);
    while ($objResult = mysqli_fetch_assoc($objQuery2)) {
        $approval_name = $objResult['name'];
    }

    if (md5($doc_no . '1') == $get_mode) {
        $documents  = 2;
    }

    $currentTime = date("Y-m-d");

    ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include_once('_navbar.php'); ?>
        <?php include_once('_menu.php'); ?>
        <?php
        if ($documents > 1) {
            $mode = "";
            $mode_select = "";
        } else {
            $mode = "readonly";
            $mode_select = "disabled";
        }
        ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1><?php echo "Document No. : " . $doc_no; ?></h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <section class="content">
                <!-- Main content -->
                <div>
                    <!-- menu header -->
                    <button type="button" class="btn btn-app flat" onclick="window.location.href='documents.php'" title="<?php echo BTN_DISCARD; ?>">
                        <img src="dist/img/icon/multiply.svg" style="padding:3px;" width="24"><br>
                        <?php echo BTN_DISCARD; ?>
                    </button>
                    <?php if ($mode != "readonly") { ?>
                        <button type="button" class="btn btn-app flat" onclick="window.open('documents_pdf.php?no=<?php echo md5($doc_id); ?>', '_blank');" title="PDF">
                            <img src="dist/img/icon/pdf.png" width="24"><br>
                            PDF
                        </button>
                        <button type="button" class="btn btn-app flat" onclick="previewWord('<?php echo md5($doc_id); ?>', 'download.php?no=<?php echo md5($doc_id) ?>')" title="Word">
                            <img src="dist/img/icon/doc.png" width="24"><br>
                            Word
                        </button>

                        <?php
                        $sql_check = "SELECT COUNT(*) as `total`, SUM(`checked`) as `total_checked` FROM `documents_line` WHERE md5(`doc_id`) = '$get_id'";
                        $result_check = $conDB->sqlQuery($sql_check);
                        while ($objResult_check = mysqli_fetch_assoc($result_check)) {
                            $total = $objResult_check['total'];
                            $total_checked = $objResult_check['total_checked'];
                            $all_checked = ($total == $total_checked);
                        }
                        ?>
                        <?php if ($all_checked) { ?>
                            <button type="button" class="btn btn-app flat" onclick="saveWord('<?php echo md5($doc_id); ?>','<?php echo $checkedby; ?>','<?php echo $approval_name; ?>','<?php echo $method_statement; ?>','<?php echo $doc_no; ?>','<?php echo $preparedby; ?>','<?php echo $currentTime; ?>','Create')" title="Save">
                                <img src="dist/img/icon/send.png" width="24"><br>
                                Send Approve
                            </button>
                        <?php } else { ?>
                            <button type="button" class="btn btn-app flat" data-toggle="modal" data-target="#alertCheck" title="Save">
                                <img src="dist/img/icon/send.png" width="24"><br>
                                Send Approve
                            </button>
                        <?php } ?>
                    <?php } ?>
                </div><!-- /menu header -->
                <div class="row" style="padding: 0px 10px;">
                    <!-- General 1 -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Title Head</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body row">
                                <div class="col-md-12">
                                    <?php
                                    $strSQL_cancel = "SELECT * FROM `documents` WHERE md5(`id`) = '$get_id' AND `comment` IS NOT NULL AND `comment` != '' LIMIT 1";
                                    $objQuery_cancel = $conDB->sqlQuery($strSQL_cancel);
                                    while ($objResult_cancel = mysqli_fetch_assoc($objQuery_cancel)) {
                                        if ($objResult_cancel['created_reject'] != "") {
                                            $created_reject = date("d/m/Y", strtotime($objResult_cancel['created_reject']));
                                        }
                                    ?>
                                        <div class="alert alert-warning alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h5><i class="fa fa-exclamation-circle text-warning"></i> Reject by <?php echo $objResult_cancel['rejectby']; ?> at <?php echo $created_reject ?></h5>
                                            Reason : <?php echo $objResult_cancel['comment']; ?><br>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-md-8">
                                    <form method="post" id="documents" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Title <em></em></label>
                                                    <input type="text" class="form-control" name="method_statement" onchange="dataPost('method_statement', this.value)" value="<?php echo htmlentities($method_statement); ?>" <?php echo $mode; ?> />
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Document No. <em></em></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="<?php echo $doc_no; ?>" readonly>
                                                        <?php if ($mode != "readonly") { ?>
                                                            <span class="input-group-append">
                                                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#noselectModal"><i class="fas fa-search"></i></button>
                                                            </span>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Date <em></em></label>
                                                    <div class="input-group date" id="date" data-target-input="nearest">
                                                        <input type="text" onchange="dataPost('date', convertDateFormat(this.value))" value="<?php echo $date; ?>" <?php echo $mode; ?> class="form-control datetimepicker-input" data-target="#date">
                                                        <div class="input-group-append" data-target="#date" data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Discipline <em></em></label>
                                                    <input type="text" class="form-control" value="<?php echo $discipline ?>" <?php echo $mode; ?> />
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Work <em></em></label>
                                                    <input type="text" class="form-control" value="<?php echo $work ?>" <?php echo $mode; ?> />
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Type <em></em></label>
                                                    <input type="text" class="form-control" value="<?php echo $type ?>" <?php echo $mode; ?> />
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Prepared By <em></em></label>
                                                    <input type="text" class="form-control" name="preparedby" value="<?php echo $preparedby; ?>" <?php echo $mode; ?> />
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Remark <em></em></label>
                                                    <textarea class="form-control" rows="3" name="remark" onchange="dataPost('remark', this.value)" <?php echo $mode; ?>><?php echo htmlentities($remark); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Content Discription</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- .card-body -->
                                <div class="row">
                                    <?php if ($mode != "readonly") { ?>
                                        <button type="button" class="btn btn-app flat" title="Add Contents" data-toggle="modal" data-target="#selectcontents">
                                            <img src="dist/img/icon/list.png" width="20"><br>
                                            Add Contents
                                        </button>
                                        <button type="button" class="btn btn-app flat" title="Reload Contents" onclick="reloadContent(<?php echo $doc_id ?>)">
                                            <img src="dist/img/icon/renew.svg" width="20"><br>
                                            Reload Contents
                                        </button>
                                        <div class="col-md-12">
                                            <span class="text-danger" style="font-size: 14px;"> * กด Reload Contents เพื่อแสดงหัวข้อ</span>
                                        </div>
                                    <?php } ?>
                                </div>

                                <table class="table table-bordered" style="font-size: 0.8em;">
                                    <thead>
                                        <tr>
                                            <th width="80">No.<br><em></em></th>
                                            <th width="100">#</th>
                                            <th width="80">Written</th>
                                            <th width="80">Reject</th>
                                            <th>Content<br><em></em></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $index = 1;
                                        while ($objResult = mysqli_fetch_assoc($objQuery_line)) {
                                            $sql = "SELECT * FROM `documents_line_cont` WHERE `line_id` = '" . $objResult['id'] . "'";
                                            $result = $conDB->sqlQuery($sql);
                                            if ($result->num_rows > 0) {
                                                $sql_update = "UPDATE `documents_line` SET `checked` = 1 WHERE `id` = '" . $objResult['id'] . "'";
                                                $conDB->sqlQuery($sql_update);
                                            }

                                            $sql1 = "SELECT * FROM `documents_line` WHERE `id` = '" . $objResult['id'] . "' AND `comment` IS NOT NULL AND `comment` != '' LIMIT 1";
                                            $result1 = $conDB->sqlQuery($sql1);
                                        ?>
                                            <tr>
                                                <td>
                                                    <span><?php echo $index; ?></span>

                                                <td align="center">
                                                    <?php if ($mode != "readonly") { ?>
                                                        <img src="dist/img/icon/edit.svg" onclick="window.location.href='documents_line_edit.php?no=<?php echo md5($objResult['id']) . '&m=' . md5($doc_no . '1'); ?>'" title="Edit<?php echo $objResult['id']; ?>" width="30" style="padding-right: 10px;cursor: pointer;" />
                                                    <?php } else { ?>
                                                        <img src="dist/img/icon/search.svg" onclick="window.location.href='documents_line_edit.php?no=<?php echo md5($objResult['id']); ?>'" title="View<?php echo $objResult['id']; ?>" width="30" style="padding-right: 10px;cursor: pointer;" />
                                                    <?php } ?>

                                                </td>
                                                <td align="center">
                                                    <?php if ($result->num_rows > 0) { ?>
                                                        <img src="dist/img/icon/mark.png" width="25">
                                                    <?php } else { ?>
                                                        <img src="dist/img/icon/mark_gray.png" width="25">
                                                    <?php } ?>
                                                </td>
                                                <td align="center">
                                                    <?php if ($result1->num_rows > 0) { ?>
                                                        <img src="dist/img/icon/warning.svg" width="25">
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <span><?php echo $objResult['name']; ?></span>
                                                </td>
                                            </tr>
                                        <?php $index++;
                                        } ?>
                                    </tbody>
                                </table>
                            </div><!-- /.card-body -->
                        </div>
                    </div>
                </div>
                <div id="selectcontents" class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Select Contents</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.reload()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php
                                    $index = 1;
                                    $strSQL_content = "SELECT * FROM `contents` WHERE `enable` = 1";
                                    $objQuery_content = $conDB->sqlQuery($strSQL_content);
                                    while ($objResult_content = mysqli_fetch_assoc($objQuery_content)) {
                                        $strSQL = "SELECT * FROM `documents_line` WHERE `doc_id` = '" . $doc_id . "' AND `content_id` = '" . $objResult_content['id'] . "' AND `enable` = 1";
                                        $exist = $conDB->sqlNumrows($strSQL);
                                        if ($exist > 0) {
                                            $enable = "1";
                                        } else {
                                            $enable = "0";
                                        }
                                        $checked = $objResult_content['checked']
                                    ?>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $objResult_content['number'] . ". " . $objResult_content['name']; ?> <em></em></label>
                                                <?php if ($checked == 0) { ?>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="enable<?php echo $objResult_content['id'] ?>" onChange="switchChange('<?php echo $doc_id; ?>','<?php echo $objResult_content['id']; ?>',this)" value="<?php echo $enable; ?>" <?php if ($enable == "1") {
                                                                                                                                                                                                                                                                                                    echo "checked";
                                                                                                                                                                                                                                                                                                } ?> <?php echo $mode; ?> />
                                                        <label class="custom-control-label" for="enable<?php echo $objResult_content['id'] ?>"></label>
                                                    </div>
                                                <?php } else { ?>
                                                    <br><small class="badge badge-success">&nbsp;Required&nbsp;</small>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php
                                        $index++;
                                    } ?>
                                    <button class="btn btn-primary" onclick="reloadContent(<?php echo $doc_id ?>)">Select</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="alertCheck" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Warning</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="card-body">
                                <em>
                                    <p>Unable to send request Because incomplete content</p>
                                </em>
                                <em>
                                    <p>If you want to save, press the discard button to exit.</p>
                                </em>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- /General 1 -->
                <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content" style="padding-top: 20px;">
                            <center>
                                <p id="callbackMsg"></p>
                            </center>
                        </div>
                    </div>
                </div>
        </div>
        </section>
        <?php include_once('_footer.php'); ?>
    </div>
    <?php include_once('_script.php'); ?>
    <script>
        function dataPost(field, value) {
            updateValue('documents', '<?php echo $get_id; ?>', field, value);
        }

        <?php if ($mode == "") { ?>
            $('#date').datepicker({
                format: 'dd/mm/yyyy'
            });
        <?php } ?>

        <?php if ($check_content == 0) { ?>
            // A $( document ).ready() block.
            $(document).ready(function() {
                $("#selectcontents").modal('show');
                console.log("ready!");

            });

        <?php } ?>
    </script>

</html>