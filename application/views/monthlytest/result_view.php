<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><span class="glyphicon glyphicon-home"></span></a></li>
            <li class="active">Monthly Test / View Results</li>
        </ol>
    </div><!--/.row-->

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Result Record</h1>
        </div>
    </div><!--/.row-->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div  class="panel-heading">Result Record
                     <a href="<?= site_url()?>admin/student_view" type="button" style="position: relative;width: 170px; " class="btn btn-primary btn-outline margin  pull-right"><b>Add New Record</b></a>
                     <a href="<?= site_url()?>monthlytest/test_index" type="button" style="position: relative;width: 120px;left: 35px; " class="btn btn-primary btn-outline margin  pull-right"><b>Back</b></a>
                </div>
                <div class="panel-body">
                    <table data-toggle="table" data-show-refresh="true" data-search="true" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="name" data-sort-order="desc">
                        <thead>
                        <tr>
                            <th data-sortable="true"><b>Test Id</b></th>
                            <th data-sortable="true"><b>Class</b></th>
                            <th data-sortable="true"><b>Subject</b></th>
                            <th data-sortable="true"><b>Test Name</b></th>
                            <th data-sortable="true"><b>Test Month</b></th>
                            <th data-sortable="true"><b>Test Date</b></th>
                            <th data-sortable="true"><b>Total Marks</b></th>
                            <th colspan="2" class="text-center"><b>Actions</b></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($result as $array){?>
                        <tr>
                            <td><?=$array->test_id?></td>
                            <td><?=$array->course_title?></td>
                            <td><?=$array->category_title?></td>
                            <td><?=$array->test_name?></td>
                            <td><?=$array->test_month?></td>
                            <td><?=$array->test_date?></td>
                            <td><?=$array->total_marks?></td>
                             <td>
                                <a href="<?= site_url()?>monthlytest/test_studentdetails/<?= $array->test_id ?>" type="button" class="btn btn-primary glyphicon glyphicon-edit btn-outline margin">View Details</a>
                                <a href="<?= site_url()?>monthlytest/result_delete/<?= $array->test_id ?>" type="button" class="btn btn-primary glyphicon glyphicon-trash btn-outline margin"> Delete</a>
                            </td>
                           
                        </tr>
                        <?php } ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div><!--/.row-->
</div><!--/.main-->