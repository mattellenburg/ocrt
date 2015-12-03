<script src="http://maps.googleapis.com/maps/api/js"></script>
<script>
    var getQueryString = function ( field, url ) {
        var href = url ? url : window.location.href;
        var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
        var string = reg.exec(href);
        return string ? string[1] : null;
    };

    function getImage(pending, icon) {
        if (pending == 'pending') {
            image = " <?php echo base_url('assets/images/star.png') ?> ";
        }
        else if (icon == 1) {
            image = " <?php echo base_url('assets/images/Playground-50.png') ?> ";
        }
        else if (icon == 2) {
            image = " <?php echo base_url('assets/images/Pullups Filled-50.png') ?> ";
        }
        else if (icon == 3) {
            image = " <?php echo base_url('assets/images/City Bench-50.png') ?> ";
        }
        else if (icon == 4) {
            image = " <?php echo base_url('assets/images/Weight-50.png') ?> ";
        }
        else if (icon == 5) {
            image = " <?php echo base_url('assets/images/Pushups-50.png') ?> ";
        }
        else if (icon == 6) {
            image = " <?php echo base_url('assets/images/Stadium-50.png') ?> ";
        }
        else if (icon == 7) {
            image = " <?php echo base_url('assets/images/Trekking-50.png') ?> ";
        }
        else if (icon == 8) {
            image = " <?php echo base_url('assets/images/Climbing Filled-50.png') ?> ";
        }
        else if (icon == 9) {
            image = " <?php echo base_url('assets/images/Wakeup Hill on Stairs-50.png') ?> ";
        }
        else {
            image = " <?php echo base_url('assets/images/Marker-50.png') ?> ";
        }
        
        return image;
    }
</script>