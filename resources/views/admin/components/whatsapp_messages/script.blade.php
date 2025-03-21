<script>
    $(document).ready(function() {
        $("#recipient_phone").select2({
            tags: true, 
            tokenSeparators: [',', ' '], 
            placeholder: "Enter phone numbers",
            allowClear: true,
            createTag: function(params) {
                let val = params.term.trim();
                if (!/^\d+$/.test(val)) {
                    
                    return null;
                }

                return {
                    id: val,
                    text: val
                };
            },
            maximumSelectionLength: 10 
        });

        $('#recipient_phone').on('select2:open', function () {
            var inputField = $('.select2-search__field');
            inputField.on('input', function () {
                var value = $(this).val();
                if (!/^\d*$/.test(value)) {
                    $(this).val(value.replace(/[^0-9]/g, ''));
                }
            });
        });

        $("#sms").select2({
            tags: true, 
            tokenSeparators: [',', ' '], 
            placeholder: "Enter phone numbers",
            allowClear: true,
            createTag: function(params) {
                let val = params.term.trim();
                if (!/^\d+$/.test(val)) {
                    
                    return null;
                }

                return {
                    id: val,
                    text: val
                };
            },
            maximumSelectionLength: 10 
        });

        $('#sms').on('select2:open', function () {
            var inputField = $('.select2-search__field');
            inputField.on('input', function () {
                var value = $(this).val();
                if (!/^\d*$/.test(value)) {
                    $(this).val(value.replace(/[^0-9]/g, ''));
                }
            });
        });
    });
</script>