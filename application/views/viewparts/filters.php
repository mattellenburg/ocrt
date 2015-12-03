<div id="filters">
    <h2>Location Filters</h2>
    <div class="content">
        <div>
            <?php echo $filters ?>
            <?= form_open() ?>
            <p>
                <label for="filterrating">Average Rating:</label>
                <label><input type="radio" id="filterrating" name="filterrating" value="1" onclick="stars(this.name);" /><img id="filterratingstar1" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>
                <label><input type="radio" id="filterrating" name="filterrating" value="2" onclick="stars(this.name);" /><img id="filterratingstar2" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>
                <label><input type="radio" id="filterrating" name="filterrating" value="3" onclick="stars(this.name);" /><img id="filterratingstar3" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>
                <label><input type="radio" id="filterrating" name="filterrating" value="4" onclick="stars(this.name);" /><img id="filterratingstar4" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>
                <label><input type="radio" id="filterrating" name="filterrating" value="5" onclick="stars(this.name);" /><img id="filterratingstar5" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>
                or higher
            </p>
            <label for="search">Title/Description Search:</label><input type="text" id="filtersearch" name="filtersearch" /></p>

            <label for="filterkeywords">Keywords:</label>
            <div class="keywords">
            <?php foreach ($keywords as $filterkeyword): ?>
                <input id="filterkeywords[]" name="filterkeywords[]" type="checkbox" value=" <?php echo $filterkeyword->id ?> " /><?php echo $filterkeyword->keyword ?><br>
            <?php endforeach; ?>
            </div>

            <?php if (isset($_SESSION['user_id'])) : ?>
                <p><label for="mysubmissions">Submitted by Me:</label><input type="checkbox" id="mysubmissions" name="mysubmissions"></p>
            <?php endif; ?>
                <p><input type="submit" value="Search" /><input type="button" value="Clear" onclick="redirectURL(<?php echo $mapview ?>, <?php echo $zoom ?>, <?php echo $latitude ?>, <?php echo $longitude ?>, <?php if (isset($pointid)) { echo $pointid; } ?>, 1);" /></p>
            </form>
        </div>
    </div>
</div>
