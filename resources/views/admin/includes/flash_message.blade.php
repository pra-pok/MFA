@if(session('alert-success'))
    <script>
        showAlert("{{ session('alert-success') }}", true);
    </script>
@endif

@if(session('alert-danger'))
    <script>
        showAlert("{{ session('alert-danger') }}", false);
    </script>
@endif
{{--<script>--}}
{{--    $('#myForm').submit(function(event) {--}}
{{--        event.preventDefault();--}}
{{--        showConfirmDialog("Confirm Action", "Are you sure you want to submit this form?", function() {--}}
{{--            $('#myForm')[0].submit();--}}
{{--        });--}}
{{--    });--}}
{{--</script>--}}
