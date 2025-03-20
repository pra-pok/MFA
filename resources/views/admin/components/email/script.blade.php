{{-- <script>
    $(document).ready(function() {

    $("#recipient_email").select2({
        tags: true,
        tokenSeparators: [',', ' '],
        placeholder: "Enter the email address",
        allowClear: true,
        createTag: function(params) {
            let val = params.term.trim();
            return {
                id: val,
                text: val
            };
        },
        maximumSelectionLength: 10
    });
    $('#recipient_email').on('select2:open', function() {
        var inputField = $('.select2-search__field');
        inputField.on('input', function() {
            var value = $(this).val();
            if (!/^\d*$/.test(value)) {
                $(this).val(value.replace(/[^0-9]/g, ''));
            }
        });
    });
    $('#organization_signup_id').select2({
        placeholder: "Search for a College/School",
        allowClear: true,
        ajax: {
            url: "{{ route('organizationemail.search') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term // Search query
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            id: item.id,
                            text: item.full_name
                        };
                    })
                };
            },
            cache: true
        }
    });
});
</script> --}}

<script>
    $(document).ready(function() {
    $("#recipient_email").select2({
        tags: true,
        tokenSeparators: [',', ' '],
        placeholder: "Enter the email address",
        allowClear: true,
        createTag: function(params) {
            let val = params.term.trim();
            return {
                id: val,
                text: val
            };
        },
        maximumSelectionLength: 10
    });

    // Remove the number-only restriction for email input
    $('#recipient_email').on('select2:open', function() {
        var inputField = $(".select2-container--open .select2-search__field");
        inputField.off('input'); // Ensure no previous restrictions
    });

    $('#organization_signup_id').select2({
        placeholder: "Search for a College/School",
        allowClear: true,
        ajax: {
            url: "{{ route('organizationemail.search') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term // Search query
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            id: item.id,
                            text: item.full_name
                        };
                    })
                };
            },
            cache: true
        }
    });
});

    </script>
