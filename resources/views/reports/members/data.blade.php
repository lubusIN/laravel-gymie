@extends('app')

@section('header_scripts')

    <!-- Stimusloft Reports.JS -->
    <script src="{{ URL::asset('assets/reports/js/stimulsoft.reports.js') }}" type="text/javascript"></script>
    <!-- Report Viewer Office2013 style -->
    <link href="{{ URL::asset('assets/reports/css/stimulsoft.viewer.office2013.css') }}" rel="stylesheet"/>
    <link href="{{ URL::asset('assets/reports/css/stimulsoft.designer.office2013.white.blue.css') }}" rel="stylesheet"/>
    <script src="{{ URL::asset('assets/reports/js/stimulsoft.viewer.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('assets/reports/js/stimulsoft.designer.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        // Report Data
        var dataSet = new System.Data.DataSet("members_data");
        dataSet.readJsonFile("http://localhost/gymie-web/public/reportData/members");


        // Create a new report instance and load report from server
        var report = new Stimulsoft.Report.StiReport();
        report.regData(dataSet.dataSetName, "", dataSet);
        report.loadFile("{{ URL::asset('assets/reports/mrt/members.mrt') }}");

        // View report options
        var options = new Stimulsoft.Viewer.StiViewerOptions();
        options.toolbar.showFullScreenButton = false;
        options.toolbar.showDesignButton = true;

        // Report Viewer
        var viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);
        viewer.report = report;

        // Add the design button event
        viewer.onReportDesign = function (e) {
            this.visible = false;
            if (designer == null) createDesigner();
            designer.visible = true;
            designer.report = e.report;
        };


        // Report Designer
        var designer = new Stimulsoft.Designer.StiDesigner(null, "StiDesigner", false);
        designer.report = report;


    </script>
@stop

@section('content')

    <?php use Carbon\Carbon; ?>

    <div class="rightside bg-grey-100">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title no-line-height">Members
                @permission(['manage-gymie','manage-members','add-member'])
                <small>Data of all gym members</small>
            </h1>
            @endpermission
        </div><!-- / PageHead -->

        <div class="container-fluid">
            <div class="row">

                <div id="viewerContent">
                    <script type="text/javascript">
                        viewer.renderHtml();
                        designer.renderHtml();
                        designer.visible = false;
                    </script>
                </div>

            </div>
        </div>

    </div>

@stop