<?php

// This is the return for the ajax call below
if ($this->ajax) {

    // This assumes the page is already called, and an inner AJAX is called
    if (isset($this->courses)) {
        if ($this->courses == null) {
            echo "<option value=''>Course Select</option>";
            echo "<option value='Add'>Add Course</option>";
            die();
        } elseif (is_array( $this->courses )) {
            foreach ($this->courses as $value)
                echo "<option value='{$value}'>$value</option>";
            exit();  // This will stop the run and just return the list
        }
    }
}
?>

<script>

    function showSibling(self) {        // TODO - make better using .closest
        if (self.nodeName != 'TR') {
            if (self.options[self.selectedIndex].value == "none") return;
        }
        while (self.nodeName != 'TR') {
            self = self.parentElement;
        }
        do {
            self = self.nextSibling;
        } while (self.nodeName != 'TR');
        self.style.display = '';
    }

    function states(select) {
        var state = select.value;
        var courses = $("#course"); // container to be placed in

        courses.removeAttr("disabled", "disabled");     // To ensure they at least search for it

        $.ajax({  // Get a reduced list of all courses within a state
            url: (document.location + state + "/"), success: function (result) {
                courses.html(result);
            }
        });
    }

    function course(select) {
        var course = select.value;
        var uri = select.href;

        // Jump to a new page using Pjax
        if (course == "Add")
            return $.pjax({url: ('http://' + window.location.hostname + '/AddCourse/'), container: '#ajax-content'});

        // Replace the content while
        $.ajax({ url: ('http://' + window.location + course + '/'), success: function (result) {
                $("#ajax-content");
            }
        });
    }
</script>


<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Post Score
        <small style="color: ghostwhite;">Course Select</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#" style="color: ghostwhite; "><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#" style="color: ghostwhite;">Forms</a></li>
        <li class="active" style="color: ghostwhite;">Advanced Elements</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <!-- SELECT COURSE -->
    <div class="box box-custom" style="background-color: #2c3b41; border-top-color: #2c3b41; color: ghostwhite !important;">

        <div class="box-header">
            <h3 class="box-title" style="color: ghostwhite;">Where Was Your Round?</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <!-- /.box-header -->

        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>State</label>
                        <select class="form-control select2" style="width: 100%;" onchange="states(this)">
                            <option selected="selected" disabled value="">State</option>
                            <option value="Alabama">Alabama</option>
                            <option value="Alaska">Alaska</option>
                            <option value="California">California</option>
                            <option value="Delaware">Delaware</option>
                            <option value="Tennessee">Tennessee</option>
                            <option value="Texas">Texas</option>
                            <option value="Washington">Washington</option>
                        </select>
                    </div><!-- /.form-group -->

                    <div data-pjax class="form-group">
                        <label>Course</label>
                        <select id="course" class="form-control select2" <?=(!isset($this->courses) ? 'disabled="disabled"' : null);?>
                                onchange="course(this)" style="width: 100%;">
                        </select>
                    </div><!-- /.form-group -->
                </div><!-- /.col -->
            </div>
        </div>

    </div>
    
</section><!-- /.content -->

<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
</script>



